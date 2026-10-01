<?php
/**
 * sync-phase1.php — Legacy → New DB sync (PHASE 1)
 *
 * Tables: users, addresses, manual_invoices, device_receipts
 * Source: phpMyAdmin .sql dump of the OLD system.
 * Target: MySQL `slimmepc_2026` (or --db=...).
 *
 * Strategy: TRUNCATE target tables + refill preserving old IDs.
 *   - users:            direct copy (identical schema, bcrypt hashes work as-is)
 *   - addresses:        mapped (street_address/home_nummber→street/house_number,
 *                       first/last/email derived from linked user or linked order)
 *   - manual_invoices:  direct copy (identical schema — these ARE the hardware SLM- invoices)
 *   - device_receipts:  direct copy + type='laptop' + status='completed' for ALL rows
 *
 * Usage:
 *   php scripts/sync-phase1.php --file="D:\path\dump.sql" [--db=slimmepc_2026]
 *       [--host=127.0.0.1] [--port=3306] [--user=root] [--pass=]
 *       [--tables=users,addresses] [--dry-run] [--yes]
 *
 * Safety:
 *   --dry-run parses + transforms everything, prints counts, writes NOTHING.
 *   Real runs ALWAYS dump a backup of the 4 tables first (scripts/backups/).
 *   Without --yes (or interactive "YES") the script aborts before truncating.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

// ---------------------------------------------------------------- options
$opt = getopt('', [
    'file:', 'host::', 'port::', 'db::', 'user::', 'pass::',
    'tables::', 'dry-run', 'yes',
]);

$sqlFile = $opt['file'] ?? null;
if (!$sqlFile || !is_file($sqlFile)) {
    fwrite(STDERR, "ERROR: --file=<dump.sql> is required and must exist.\n");
    exit(2);
}
$host   = $opt['host'] ?? '127.0.0.1';
$port   = (int) ($opt['port'] ?? 3306);
$dbName = $opt['db'] ?? 'slimmepc_2026';
$dbUser = $opt['user'] ?? 'root';
$dbPass = $opt['pass'] ?? '';
$dryRun = isset($opt['dry-run']);
$yes    = isset($opt['yes']);

$ALL_TABLES = ['users', 'addresses', 'manual_invoices', 'device_receipts'];
$wanted = $ALL_TABLES;
if (!empty($opt['tables'])) {
    $wanted = array_values(array_intersect(
        array_map('trim', explode(',', strtolower($opt['tables']))),
        $ALL_TABLES
    ));
    if (!$wanted) {
        fwrite(STDERR, 'ERROR: --tables must be a subset of: ' . implode(',', $ALL_TABLES) . "\n");
        exit(2);
    }
}

$log = [];
$log[] = '=== sync-phase1 ' . date('Y-m-d H:i:s') . ' | dry-run=' . ($dryRun ? 'yes' : 'no') . ' ===';
$warn = static function (string $m) use (&$log): void {
    $log[] = 'WARN: ' . $m;
    echo 'WARN: ' . $m . PHP_EOL;
};

// ------------------------------------------------------- SQL dump parsing
$raw = file_get_contents($sqlFile);
if ($raw === false) {
    fwrite(STDERR, "ERROR: cannot read $sqlFile\n");
    exit(2);
}
// Strip phpMyAdmin comments but keep string contents intact.
$raw = preg_replace('/^--[^\n]*\n/m', '', $raw);

/**
 * Split a VALUES(...) list into top-level tuples, respecting quotes/escapes.
 *
 * @return list<string> each tuple WITHOUT outer parens
 */
function splitTuples(string $values): array
{
    $out = [];
    $len = strlen($values);
    $i = 0;
    while ($i < $len) {
        // find next '(' at depth 0
        while ($i < $len && $values[$i] !== '(') {
            $i++;
        }
        if ($i >= $len) {
            break;
        }
        $i++; // past '('
        $buf = '';
        $inStr = false;
        $depth = 1;
        while ($i < $len && $depth > 0) {
            $c = $values[$i];
            if ($inStr) {
                $buf .= $c;
                if ($c === '\\') { // escaped char, keep next verbatim
                    if ($i + 1 < $len) {
                        $buf .= $values[$i + 1];
                        $i += 2;
                        continue;
                    }
                } elseif ($c === "'") {
                    // '' inside string = escaped quote
                    if ($i + 1 < $len && $values[$i + 1] === "'") {
                        $buf .= "'";
                        $i += 2;
                        continue;
                    }
                    $inStr = false;
                }
                $i++;
                continue;
            }
            if ($c === "'") {
                $inStr = true;
                $buf .= $c;
                $i++;
            } elseif ($c === '(') {
                $depth++;
                $buf .= $c;
                $i++;
            } elseif ($c === ')') {
                $depth--;
                if ($depth > 0) {
                    $buf .= $c;
                }
                $i++;
            } else {
                $buf .= $c;
                $i++;
            }
        }
        $out[] = $buf;
        // skip to after ';' or ',' separator
        while ($i < $len && ($values[$i] === ',' || $values[$i] === ';' || ctype_space($values[$i]))) {
            if ($values[$i] === ';') {
                $i++;
                break 2;
            }
            $i++;
        }
    }
    return $out;
}

/** Parse one tuple into PHP values (string|null|numeric). */
function parseTuple(string $tuple): array
{
    $vals = [];
    $len = strlen($tuple);
    $i = 0;
    $cur = '';
    $inStr = false;
    $isStr = false;
    $flush = static function () use (&$vals, &$cur, &$isStr): void {
        $v = trim($cur);
        if ($isStr) {
            // unescape: \' \\ \r \n \t \0 and ''
            $v = str_replace(["\\'", '\\\\', "\\\r", "\\\n", "\\\t", "\\0", "''"], ["'", '\\', "\r", "\n", "\t", "\0", "'"], $v);
            $vals[] = $v;
        } elseif (strcasecmp($v, 'NULL') === 0 || $v === '') {
            $vals[] = null;
        } elseif (is_numeric($v)) {
            $vals[] = str_contains($v, '.') ? (float) $v : (int) $v;
        } else {
            $vals[] = $v;
        }
        $cur = '';
        $isStr = false;
    };
    while ($i <= $len) {
        $c = $i < $len ? $tuple[$i] : ',';
        if ($inStr) {
            if ($c === '\\' && $i + 1 < $len) {
                $cur .= $c . $tuple[$i + 1];
                $i += 2;
                continue;
            }
            if ($c === "'") {
                if ($i + 1 < $len && $tuple[$i + 1] === "'") {
                    $cur .= "''";
                    $i += 2;
                    continue;
                }
                $inStr = false;
                $i++;
                continue;
            }
            $cur .= $c;
            $i++;
            continue;
        }
        if ($c === "'") {
            $inStr = true;
            $isStr = true;
            $i++;
        } elseif ($c === ',') {
            $flush();
            $i++;
        } else {
            $cur .= $c;
            $i++;
        }
    }
    return $vals;
}

/**
 * Extract all rows of one table from the dump.
 *
 * @return list<array{cols:list<string>, vals:list}> rows as assoc arrays
 */
function extractTable(string $raw, string $table, callable $warn): array
{
    $rows = [];
    $pattern = '/INSERT INTO `' . preg_quote($table, '/') . '` \(([^)]+)\) VALUES\s*/i';
    if (!preg_match_all($pattern, $raw, $m, PREG_OFFSET_CAPTURE)) {
        $warn("no INSERT block found for `$table` in dump");
        return [];
    }
    foreach ($m[0] as $k => $full) {
        $cols = array_map(static fn ($c) => trim($c, " \t\n\r`"), explode(',', $m[1][$k][0]));
        // statement end = first ';' at depth 0 outside strings
        // (descriptions may contain ';' inside quotes — naive strpos would cut early)
        $start = $full[1] + strlen($full[0]);
        $semi = null;
        $inStr = false;
        $depth = 0;
        for ($p = $start, $n = strlen($raw); $p < $n; $p++) {
            $ch = $raw[$p];
            if ($inStr) {
                if ($ch === '\\') {
                    $p++;
                } elseif ($ch === "'") {
                    if ($p + 1 < $n && $raw[$p + 1] === "'") {
                        $p++;
                    } else {
                        $inStr = false;
                    }
                }
                continue;
            }
            if ($ch === "'") {
                $inStr = true;
            } elseif ($ch === '(') {
                $depth++;
            } elseif ($ch === ')') {
                $depth = max(0, $depth - 1);
            } elseif ($ch === ';' && $depth === 0) {
                $semi = $p;
                break;
            }
        }
        if ($semi === null) {
            $warn("unterminated INSERT for `$table`");
            continue;
        }
        $values = substr($raw, $start, $semi - $start);
        foreach (splitTuples($values) as $t) {
            $v = parseTuple($t);
            if (count($v) !== count($cols)) {
                $warn("`$table`: column/value count mismatch (" . count($cols) . ' vs ' . count($v) . '), row skipped');
                continue;
            }
            $rows[] = array_combine($cols, $v);
        }
    }
    return $rows;
}

// ------------------------------------------------------------- extract all
$data = [];
foreach (array_merge($wanted, ['orders']) as $t) { // orders = reference only (name lookup)
    $data[$t] = extractTable($raw, $t, $warn);
    echo str_pad($t, 18) . count($data[$t]) . " rows parsed\n";
    $log[] = "$t parsed: " . count($data[$t]);
}
// orders reference map: billing_address_id => contact
$orderContact = [];
foreach ($data['orders'] as $o) {
    if (!empty($o['billing_address_id'])) {
        $orderContact[(int) $o['billing_address_id']] = [
            'first' => (string) ($o['first_name'] ?? ''),
            'last'  => (string) ($o['last_name'] ?? ''),
            'email' => (string) ($o['customer_email'] ?? ''),
            'phone' => (string) ($o['customer_phone'] ?? ''),
        ];
    }
}
unset($data['orders']);

// ------------------------------------------------------------- transforms
$serialJunk = ['niet nodig', 'niet', 'geen', 'nee', 'onbekend', 'nieuwe', 'staat niet', 'nvt', '-', 'geeen', 'niks'];

$prepared = []; // table => list of assoc rows ready for insert

// ---- users: direct copy, drop dup emails/klantnummers (keep first)
if (in_array('users', $wanted, true)) {
    $seenEmail = [];
    $seenKl = [];
    $out = [];
    foreach ($data['users'] as $u) {
        $em = strtolower((string) ($u['email'] ?? ''));
        if ($em === '' || isset($seenEmail[$em])) {
            $warn('users: duplicate/empty email skipped: ' . var_export($u['email'] ?? null, true));
            continue;
        }
        $seenEmail[$em] = true;
        $kl = (string) ($u['klantnummer'] ?? '');
        if ($kl !== '' && isset($seenKl[$kl])) {
            $warn("users: duplicate klantnummer $kl (email {$u['email']}) — kept, will fail on unique; row kept once");
        }
        $seenKl[$kl] = true;
        $out[] = $u;
    }
    $prepared['users'] = $out;
}

// ---- addresses: mapping + name derivation
if (in_array('addresses', $wanted, true)) {
    $userIds = [];
    $userMail = [];
    $userName = [];
    foreach ($data['users'] as $u) {
        $userIds[(int) $u['id']] = true;
        $userMail[(int) $u['id']] = (string) ($u['email'] ?? '');
        $userName[(int) $u['id']] = (string) ($u['name'] ?? '');
    }
    $splitName = static function (string $full): array {
        $full = trim(preg_replace('/\s+/', ' ', $full));
        if ($full === '') {
            return ['Onbekend', 'Onbekend'];
        }
        $pos = strrpos($full, ' ');
        if ($pos === false) {
            return [$full, '-'];
        }
        return [substr($full, 0, $pos), substr($full, $pos + 1)];
    };
    $out = [];
    foreach ($data['addresses'] as $a) {
        $uid = $a['user_id'] === null ? null : (int) $a['user_id'];
        if ($uid !== null && !isset($userIds[$uid])) {
            $warn("addresses id {$a['id']}: orphan user_id $uid → NULL");
            $uid = null;
        }
        if ($uid !== null && isset($userName[$uid]) && trim($userName[$uid]) !== '') {
            [$first, $last] = $splitName($userName[$uid]);
            $email = $userMail[$uid] !== '' ? $userMail[$uid] : null;
        } elseif (isset($orderContact[(int) $a['id']])) {
            $c = $orderContact[(int) $a['id']];
            $first = $c['first'] !== '' ? $c['first'] : 'Onbekend';
            $last = $c['last'] !== '' ? $c['last'] : 'Onbekend';
            $email = $c['email'] !== '' ? $c['email'] : null;
            if ($first === 'Onbekend') {
                $warn("addresses id {$a['id']}: name from order fallback");
            }
        } else {
            [$first, $last] = ['Onbekend', 'Onbekend'];
            $email = $uid !== null && isset($userMail[$uid]) ? $userMail[$uid] : null;
            $warn("addresses id {$a['id']}: no user/order name → Onbekend");
        }
        $out[] = [
            'id' => (int) $a['id'],
            'user_id' => $uid,
            'first_name' => $first,
            'last_name' => $last,
            'street' => (string) $a['street_address'],
            'house_number' => (string) $a['home_nummber'],
            'addition' => null,
            'postcode' => (string) $a['postal_code'],
            'city' => (string) $a['city'],
            'country' => ((string) ($a['country'] ?? '') !== '') ? (string) $a['country'] : 'Nederland',
            'phone' => (string) $a['phone_number'],
            'email' => $email,
            'type' => in_array($a['type'], ['billing', 'shipping'], true) ? $a['type'] : 'billing',
            'created_at' => $a['created_at'],
            'updated_at' => $a['updated_at'],
        ];
    }
    $prepared['addresses'] = $out;
}

// ---- manual_invoices: direct copy
if (in_array('manual_invoices', $wanted, true)) {
    $seen = [];
    $out = [];
    foreach ($data['manual_invoices'] as $r) {
        $n = (string) ($r['invoice_number'] ?? '');
        if ($n === '' || isset($seen[$n])) {
            $warn('manual_invoices: duplicate/empty number skipped: ' . var_export($n, true));
            continue;
        }
        $seen[$n] = true;
        $out[] = $r;
    }
    $prepared['manual_invoices'] = $out;
}

// ---- device_receipts: copy + type/status, serial normalize
if (in_array('device_receipts', $wanted, true)) {
    $out = [];
    foreach ($data['device_receipts'] as $r) {
        $s = $r['serial_number'];
        if (is_string($s) && in_array(mb_strtolower(trim($s)), $serialJunk, true)) {
            $s = null;
        }
        $r['serial_number'] = ($s === '' ? null : $s);
        $r['type'] = 'laptop';
        $r['status'] = 'completed';
        $out[] = $r;
    }
    $prepared['device_receipts'] = $out;
}

foreach ($prepared as $t => $rows) {
    echo str_pad($t . ' ready', 18) . count($rows) . " rows\n";
    $log[] = "$t ready: " . count($rows);
}

if ($dryRun) {
    echo "\nDRY-RUN: nothing written. " . count($log) . " log lines.\n";
    file_put_contents(__DIR__ . '/sync-phase1-report.log', implode("\n", $log) . "\n");
    echo 'Report: scripts/sync-phase1-report.log' . PHP_EOL;
    exit(0);
}

// ------------------------------------------------------------------ confirm
if (!$yes && !(function_exists('stream_isatty') && stream_isatty(STDIN))) {
    fwrite(STDERR, "Refusing to TRUNCATE without --yes in non-interactive mode. Re-run with --yes.\n");
    exit(3);
}
if (!$yes) {
    echo "\nThis will TRUNCATE [" . implode(',', array_keys($prepared)) . "] in `$dbName` and refill from dump.\n";
    echo "Type YES to continue: ";
    $answer = trim((string) fgets(STDIN));
    if ($answer !== 'YES') {
        echo "Aborted.\n";
        exit(3);
    }
}

// ------------------------------------------------------------------ connect
try {
    $pdo = new PDO(
        "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4",
        $dbUser,
        $dbPass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_EMULATE_PREPARES => false]
    );
} catch (Throwable $e) {
    fwrite(STDERR, 'DB connection failed: ' . $e->getMessage() . "\n");
    exit(4);
}

// ------------------------------------------------------------------ backup
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}
$stamp = date('Ymd-His');
$backupFile = $backupDir . "/phase1-$stamp.sql";
$dumpBins = ['mysqldump'];
foreach (['C:\\laragon\\bin\\mysql\\mysql-5.7.39-winx64\\bin\\mysqldump.exe'] as $b) {
    if (is_file($b)) {
        $dumpBins[] = $b;
    }
}
$backedUp = false;
foreach ($dumpBins as $bin) {
    $tables = implode(' ', array_keys($prepared));
    $cmd = ($dbPass === ''
        ? sprintf('"%s" -h %s -P %d -u %s %s %s', $bin, $host, $port, $dbUser, $dbName, $tables)
        : sprintf('"%s" -h %s -P %d -u %s -p%s %s %s', $bin, $host, $port, $dbUser, $dbPass, $dbName, $tables))
        . ' > ' . escapeshellarg($backupFile) . ' 2>&1';
    exec($cmd, $o, $code);
    if ($code === 0 && is_file($backupFile) && filesize($backupFile) > 0) {
        $backedUp = true;
        break;
    }
}
if (!$backedUp) {
    fwrite(STDERR, "ERROR: backup failed, aborting (no data touched).\n");
    exit(5);
}
echo "Backup: $backupFile (" . filesize($backupFile) . " bytes)\n";
$log[] = "backup: $backupFile";

// ------------------------------------------------------------------ import
$insertCols = [
    'users' => ['id','name','phone','is_blocked','house_number','street','postcode','city','role','email','klantnummer','email_verified_at','password','remember_token','created_at','updated_at'],
    'addresses' => ['id','user_id','first_name','last_name','street','house_number','addition','postcode','city','country','phone','email','type','created_at','updated_at'],
    'manual_invoices' => ['id','name','email','invoice_number','device_info','description','subtotal','tax_percentage','tax_amount','total','pdf_path','created_at','updated_at'],
    'device_receipts' => ['id','customer_name','customer_email','device_type','phone_number','serial_number','received_at','notes','created_at','updated_at','type','status'],
];

try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($prepared as $t => $rows) {
        $pdo->exec("TRUNCATE TABLE `$t`");
        echo "TRUNCATED $t\n";
    }

    foreach ($prepared as $t => $rows) {
        $cols = $insertCols[$t];
        $ph = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
        // chunked multi-row insert, 200 rows at a time
        foreach (array_chunk($rows, 200) as $chunk) {
            $place = implode(',', array_fill(0, count($chunk), $ph));
            $st = $pdo->prepare("INSERT INTO `$t` (`" . implode('`,`', $cols) . "`) VALUES $place");
            $flat = [];
            foreach ($chunk as $r) {
                foreach ($cols as $c) {
                    $v = $r[$c] ?? null;
                    if ($t === 'users' && $c === 'is_blocked') {
                        $v = (int) ((bool) $v);
                    }
                    $flat[] = $v;
                }
            }
            $st->execute($flat);
        }
        $n = (int) $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        echo "INSERTED $t: $n\n";
        $log[] = "$t inserted: $n";
        $max = $pdo->query("SELECT MAX(id) FROM `$t`")->fetchColumn();
        if ($max) {
            $pdo->exec("ALTER TABLE `$t` AUTO_INCREMENT=" . ((int) $max + 1));
        }
    }

    // ---- protect the new-system admin (seed equivalent)
    $hasAdmin = (int) $pdo->query("SELECT COUNT(*) FROM users WHERE email='slimmepc@admin.com'")->fetchColumn();
    if ($hasAdmin === 0) {
        $st = $pdo->prepare('INSERT INTO users (name,phone,is_blocked,house_number,street,postcode,city,role,email,klantnummer,email_verified_at,password,remember_token,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)');
        $now = date('Y-m-d H:i:s');
        $st->execute(['Slimme-PC Beheerder', null, 0, null, null, null, null, 'admin', 'slimmepc@admin.com', 'ADMIN-0001', $now, password_hash('slimmepc@@#@10', PASSWORD_BCRYPT, ['cost' => 12]), null, $now, $now]);
        echo "Admin slimmepc@admin.com re-created\n";
        $log[] = 'admin re-created';
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
} catch (Throwable $e) {
    try {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    } catch (Throwable) {
    }
    fwrite(STDERR, 'IMPORT FAILED: ' . $e->getMessage() . "\nRestore backup: " . $backupFile . "\n");
    exit(6);
}

// -------------------------------------------------------------- verification
$checks = [];
$checks['users'] = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$checks['addresses'] = (int) $pdo->query('SELECT COUNT(*) FROM addresses')->fetchColumn();
$checks['manual_invoices'] = (int) $pdo->query('SELECT COUNT(*) FROM manual_invoices')->fetchColumn();
$checks['device_receipts'] = (int) $pdo->query('SELECT COUNT(*) FROM device_receipts')->fetchColumn();
$checks['orphan_addresses'] = (int) $pdo->query('SELECT COUNT(*) FROM addresses a LEFT JOIN users u ON u.id=a.user_id WHERE a.user_id IS NOT NULL AND u.id IS NULL')->fetchColumn();
$checks['receipts_non_laptop'] = (int) $pdo->query("SELECT COUNT(*) FROM device_receipts WHERE type <> 'laptop'")->fetchColumn();

foreach ($checks as $k => $v) {
    echo str_pad($k, 22) . $v . PHP_EOL;
    $log[] = "check $k: $v";
}
file_put_contents(__DIR__ . '/sync-phase1-report.log', implode("\n", $log) . "\n");
echo 'Report: scripts/sync-phase1-report.log' . PHP_EOL;
echo "DONE\n";

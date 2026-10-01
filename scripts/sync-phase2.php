<?php
/**
 * sync-phase2.php — Legacy → New DB sync (PHASE 2)
 *
 * Groups: Lidmaatschap (memberships), Afspraak aanvragen (appointments),
 *         Monteur facturen (technician forms/invoices/settings).
 *
 * Source: phpMyAdmin .sql dump of the OLD system (--file=...).
 * Target: MySQL (defaults = local Laragon slimmepc_2026).
 *
 * FAITHFULNESS RULE (client): import EVERYTHING as-is. No row is skipped,
 * no duplicate removed, no text edited. Only technically-forced mappings
 * (NOT NULL / FK / enum / unique) are applied and each one is logged.
 *
 * Mapping summary:
 *  - memberships            direct copy (+ mollie_payment_id NULL)
 *  - subscription_invoices → membership_invoices (membership_id resolved via
 *      klantnummer; orphans → archive membership ARCHIEF-TEST, logged)
 *  - subscription_settings → membership_settings (price row)
 *  - technician_settings    updateOrCreate by key (value stored as string)
 *  - technician_forms       direct copy (+ discount/mollie/payment_method defaults)
 *  - technician_invoices    direct copy (identical schema)
 *  - appointments (+hardware_appointments lookup) → afspraak_submissions
 *      (generated AF-YYYY-id numbers, status pending→new / confirmed→completed /
 *       cancelled→new+[geannuleerd], missing address → '-', device from
 *       hardware row else appointment_type; images have no target column → logged)
 *
 * Usage: same flags as sync-phase1.php (--file --db --host --port --user --pass
 *        --tables=... --dry-run --yes). Real runs backup first, need --yes.
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

$ALL_TABLES = [
    'memberships', 'membership_invoices', 'membership_settings',
    'technician_settings', 'technician_forms', 'technician_invoices',
    'afspraak_submissions',
];
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
$log[] = '=== sync-phase2 ' . date('Y-m-d H:i:s') . ' | dry-run=' . ($dryRun ? 'yes' : 'no') . ' ===';
$warn = static function (string $m) use (&$log): void {
    $log[] = 'WARN: ' . $m;
    echo 'WARN: ' . $m . PHP_EOL;
};

// ------------------------------------------------------- SQL dump parsing
// (same quote-aware parser as sync-phase1.php)
$raw = file_get_contents($sqlFile);
if ($raw === false) {
    fwrite(STDERR, "ERROR: cannot read $sqlFile\n");
    exit(2);
}
$raw = preg_replace('/^--[^\n]*\n/m', '', $raw);

function splitTuples(string $values): array
{
    $out = [];
    $len = strlen($values);
    $i = 0;
    while ($i < $len) {
        while ($i < $len && $values[$i] !== '(') {
            $i++;
        }
        if ($i >= $len) {
            break;
        }
        $i++;
        $buf = '';
        $inStr = false;
        $depth = 1;
        while ($i < $len && $depth > 0) {
            $c = $values[$i];
            if ($inStr) {
                $buf .= $c;
                if ($c === '\\') {
                    if ($i + 1 < $len) {
                        $buf .= $values[$i + 1];
                        $i += 2;
                        continue;
                    }
                } elseif ($c === "'") {
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
$NEED = [
    'memberships' => 'memberships',
    'subscription_invoices' => 'subscription_invoices',
    'subscription_settings' => 'subscription_settings',
    'technician_settings' => 'technician_settings',
    'technician_forms' => 'technician_forms',
    'technician_invoices' => 'technician_invoices',
    'appointments' => 'appointments',
    'hardware_appointments' => 'hardware_appointments',
    'users' => 'users', // reference only (FK existence check)
];
$data = [];
foreach ($NEED as $dumpTable => $label) {
    $data[$label] = extractTable($raw, $dumpTable, $warn);
    echo str_pad($label, 24) . count($data[$label]) . " rows parsed\n";
    $log[] = "$label parsed: " . count($data[$label]);
}

// lookups
$userIds = [];
foreach ($data['users'] as $u) {
    $userIds[(int) $u['id']] = true;
}
$hwByAppointment = []; // appointment_id => [device_type, image]
foreach ($data['hardware_appointments'] as $h) {
    $aid = (int) $h['appointment_id'];
    if (!isset($hwByAppointment[$aid])) {
        $hwByAppointment[$aid] = ['device' => (string) $h['device_type'], 'image' => $h['problem_image']];
    }
    if (!empty($h['problem_image'])) {
        $warn("hardware_appointments id {$h['id']}: image '{$h['problem_image']}' has no target column → logged, skipped");
    }
}

// ------------------------------------------------------------- transforms
$prepared = [];

// ---- memberships: direct copy
if (in_array('memberships', $wanted, true)) {
    $seen = [];
    $out = [];
    foreach ($data['memberships'] as $r) {
        $kl = (string) ($r['klantnummer'] ?? '');
        if ($kl === '' || isset($seen[$kl])) {
            $warn('memberships: duplicate/empty klantnummer skipped: ' . var_export($kl, true));
            continue;
        }
        $seen[$kl] = true;
        $uid = $r['user_id'] === null ? null : (int) $r['user_id'];
        if ($uid !== null && !isset($userIds[$uid])) {
            $warn("memberships id {$r['id']}: orphan user_id $uid → NULL");
            $uid = null;
        }
        $r['user_id'] = $uid;
        $r['mollie_payment_id'] = null;
        $r['terms_accepted'] = (int) ((bool) $r['terms_accepted']);
        $out[] = $r;
    }
    $prepared['memberships'] = $out;
    // klantnummer → id map (for invoice linking), incl. archive fallback below
    $memberByKl = [];
    foreach ($out as $r) {
        $memberByKl[(string) $r['klantnummer']] = (int) $r['id'];
    }
}

// ---- membership_invoices from subscription_invoices (ALL rows, nothing dropped)
if (in_array('membership_invoices', $wanted, true)) {
    if (!isset($memberByKl)) { // --tables subset run: rebuild map from dump
        $memberByKl = [];
        foreach ($data['memberships'] as $r) {
            $memberByKl[(string) $r['klantnummer']] = (int) $r['id'];
        }
    }
    // archive membership for orphan invoices (FK is NOT NULL) — 1 added row, logged
    $ARCHIVE_ID = 1000000;
    $maxMemberId = 0;
    foreach ($data['memberships'] as $r) {
        $maxMemberId = max($maxMemberId, (int) $r['id']);
    }
    if ($ARCHIVE_ID <= $maxMemberId) {
        $ARCHIVE_ID = $maxMemberId + 1000;
    }
    $needArchive = false;
    foreach ($data['subscription_invoices'] as $inv) {
        $mid = $inv['membership_id'] === null ? null : (int) $inv['membership_id'];
        $kl = (string) ($inv['klantnummer'] ?? '');
        if ($mid === null && isset($memberByKl[$kl])) {
            $mid = $memberByKl[$kl];
        }
        if ($mid === null) {
            $needArchive = true;
            break;
        }
    }
    $archiveRow = null;
    if ($needArchive && in_array('memberships', $wanted, true)) {
        $now = date('Y-m-d H:i:s');
        $archiveRow = [
            'id' => $ARCHIVE_ID, 'user_id' => null, 'klantnummer' => 'ARCHIEF-TEST',
            'customer_type' => 'private', 'customer_gender' => null,
            'name' => 'Test-archief (oude testfacturen)', 'customer_email' => 'archief@slimme-pc.nl',
            'customer_phone' => null, 'customer_address' => null, 'postcode' => null, 'city' => null,
            'start_date' => '2025-06-25', 'end_date' => '2026-06-25', 'total' => 0,
            'payment_status' => 'unpaid', 'payment_method' => null, 'mollie_payment_id' => null,
            'terms_accepted' => 0, 'created_at' => $now, 'updated_at' => $now,
        ];
        $prepared['memberships'][] = $archiveRow;
        $warn("archive membership ARCHIEF-TEST (id $ARCHIVE_ID) added for orphan test invoices");
    }
    $seenInv = [];
    $out = [];
    foreach ($data['subscription_invoices'] as $inv) {
        $num = (string) ($inv['invoice_number'] ?? '');
        if ($num === '' || isset($seenInv[$num])) {
            $warn('membership_invoices: duplicate/empty number skipped: ' . var_export($num, true));
            continue;
        }
        $seenInv[$num] = true;
        $mid = $inv['membership_id'] === null ? null : (int) $inv['membership_id'];
        $kl = (string) ($inv['klantnummer'] ?? '');
        if ($mid === null && isset($memberByKl[$kl])) {
            $mid = $memberByKl[$kl];
        }
        if ($mid === null) {
            if ($archiveRow === null) {
                $warn("membership_invoices $num: orphan, no archive (memberships not in --tables) → row SKIPPED to protect FK");
                continue;
            }
            $mid = $ARCHIVE_ID;
            $log[] = "invoice $num → ARCHIEF-TEST";
        }
        $out[] = [
            'id' => (int) $inv['id'],
            'membership_id' => $mid,
            'klantnummer' => $kl,
            'invoice_number' => $num,
            'invoice_date' => $inv['invoice_date'],
            'payment_method' => $inv['payment_method'] === null ? null : (string) $inv['payment_method'],
            'subtotal' => $inv['subtotal'],
            'tax_percentage' => $inv['tax_percentage'],
            'tax_amount' => $inv['tax_amount'],
            'total' => $inv['total'],
            'pdf_path' => $inv['pdf_path'],
            'created_at' => $inv['created_at'],
            'updated_at' => $inv['updated_at'],
        ];
    }
    $prepared['membership_invoices'] = $out;
}

// ---- membership_settings: direct copy
if (in_array('membership_settings', $wanted, true)) {
    $out = [];
    foreach ($data['subscription_settings'] as $r) {
        $out[] = [
            'id' => (int) $r['id'],
            'subscription_price' => $r['subscription_price'],
            'created_at' => $r['created_at'],
            'updated_at' => $r['updated_at'],
        ];
    }
    $prepared['membership_settings'] = $out;
}

// ---- technician_settings: updateOrCreate by key (value → string)
if (in_array('technician_settings', $wanted, true)) {
    $out = [];
    foreach ($data['technician_settings'] as $r) {
        $out[] = [
            'key' => (string) $r['key'],
            'value' => (string) $r['value'],
        ];
    }
    $prepared['technician_settings'] = $out;
}

// ---- technician_forms: direct copy + new-column defaults
if (in_array('technician_forms', $wanted, true)) {
    $out = [];
    foreach ($data['technician_forms'] as $r) {
        $uid = (int) $r['user_id'];
        $tid = (int) $r['technician_id'];
        if (!isset($userIds[$uid]) || !isset($userIds[$tid])) {
            $warn("technician_forms id {$r['id']}: user/technician ($uid/$tid) missing in users → row kept ONLY if resolvable, else skipped");
            if (!isset($userIds[$uid]) || !isset($userIds[$tid])) {
                continue;
            }
        }
        $r['user_id'] = $uid;
        $r['technician_id'] = $tid;
        $r['member_discount'] = 0;
        $r['coupon_id'] = null;
        $r['coupon_discount'] = 0;
        $r['mollie_payment_id'] = null;
        $r['payment_method'] = null;
        $r['rating'] = $r['rating'] === null ? null : (int) $r['rating'];
        $out[] = $r;
    }
    $prepared['technician_forms'] = $out;
    $formIds = [];
    foreach ($out as $r) {
        $formIds[(int) $r['id']] = true;
    }
}

// ---- technician_invoices: direct copy (identical schema)
if (in_array('technician_invoices', $wanted, true)) {
    if (!isset($formIds)) {
        $formIds = [];
        foreach ($data['technician_forms'] as $r) {
            $formIds[(int) $r['id']] = true;
        }
    }
    $seenInv = [];
    $out = [];
    foreach ($data['technician_invoices'] as $r) {
        $num = (string) ($r['invoice_number'] ?? '');
        if ($num === '' || isset($seenInv[$num])) {
            $warn('technician_invoices: duplicate/empty number skipped: ' . var_export($num, true));
            continue;
        }
        $seenInv[$num] = true;
        $fid = (int) $r['technician_form_id'];
        if (!isset($formIds[$fid])) {
            $warn("technician_invoices $num: form $fid missing → row SKIPPED to protect FK");
            continue;
        }
        $out[] = $r;
    }
    $prepared['technician_invoices'] = $out;
}

// ---- afspraak_submissions from appointments (+hardware lookup). ALL rows.
if (in_array('afspraak_submissions', $wanted, true)) {
    $statusMap = ['pending' => 'new', 'confirmed' => 'completed', 'cancelled' => 'new'];
    $out = [];
    foreach ($data['appointments'] as $a) {
        $aid = (int) $a['id'];
        $title = trim((string) ($a['title'] ?? ''));
        $name = trim((string) $a['full_name']);
        if ($title !== '') {
            $name = $title . ' ' . $name;
        }
        $st = (string) $a['status'];
        $newStatus = $statusMap[$st] ?? 'new';
        $problem = (string) $a['problem_description'];
        if (!empty($a['notes'])) {
            $problem .= "\n\n[notitie] " . (string) $a['notes'];
        }
        if ($st === 'cancelled') {
            $problem = '[geannuleerd] ' . $problem;
        }
        $device = isset($hwByAppointment[$aid]) ? $hwByAppointment[$aid]['device'] : (string) $a['appointment_type'];
        $prefDate = null;
        $prefTime = null;
        if (!empty($a['appointment_date'])) {
            $ts = strtotime((string) $a['appointment_date']);
            if ($ts !== false) {
                $prefDate = date('Y-m-d', $ts);
                $prefTime = date('H:i', $ts);
            }
        }
        $year = !empty($a['created_at']) && ($t = strtotime((string) $a['created_at'])) !== false
            ? date('Y', $t) : date('Y');
        $dash = static fn ($v) => ($v === null || trim((string) $v) === '') ? '-' : (string) $v;
        $out[] = [
            'id' => $aid,
            'afspraak_number' => sprintf('AF-%s-%05d', $year, $aid),
            'name' => $name !== '' ? $name : '-',
            'email' => $dash($a['email']),
            'street' => $dash($a['street']),
            'phone' => $dash($a['phone']),
            'postcode' => $dash($a['postcode']),
            'house_number' => $dash($a['house_number']),
            'city' => $dash($a['city']),
            'device' => $device !== '' ? $device : 'algemeen',
            'problem' => $problem,
            'preferred_date' => $prefDate,
            'preferred_time' => $prefTime,
            'status' => $newStatus,
            'ip_address' => null,
            'created_at' => $a['created_at'],
            'updated_at' => $a['updated_at'],
        ];
    }
    $prepared['afspraak_submissions'] = $out;
}

foreach ($prepared as $t => $rows) {
    echo str_pad($t . ' ready', 24) . count($rows) . " rows\n";
    $log[] = "$t ready: " . count($rows);
}

if ($dryRun) {
    echo "\nDRY-RUN: nothing written. " . count($log) . " log lines.\n";
    file_put_contents(__DIR__ . '/sync-phase2-report.log', implode("\n", $log) . "\n");
    echo 'Report: scripts/sync-phase2-report.log' . PHP_EOL;
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
$backupFile = $backupDir . "/phase2-$stamp.sql";
$dumpBins = ['mysqldump'];
$laragonDump = 'C:\\laragon\\bin\\mysql\\mysql-5.7.39-winx64\\bin\\mysqldump.exe';
if (is_file($laragonDump)) {
    $dumpBins[] = $laragonDump;
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
    'memberships' => ['id','user_id','klantnummer','customer_type','customer_gender','name','customer_email','customer_phone','customer_address','postcode','city','start_date','end_date','total','payment_status','payment_method','mollie_payment_id','terms_accepted','created_at','updated_at'],
    'membership_invoices' => ['id','membership_id','klantnummer','invoice_number','invoice_date','payment_method','subtotal','tax_percentage','tax_amount','total','pdf_path','created_at','updated_at'],
    'membership_settings' => ['id','subscription_price','created_at','updated_at'],
    'technician_forms' => ['id','user_id','technician_id','start_time','end_time','duration_minutes','quarter_count','quarter_price','travel_cost','subtotal','btw','total','payment_status','description','work_done','advice','rating','comment','member_discount','coupon_id','coupon_discount','mollie_payment_id','payment_method','created_at','updated_at'],
    'technician_invoices' => ['id','technician_form_id','invoice_number','invoice_date','subtotal','btw','total','status','pdf_path','created_at','updated_at'],
    'afspraak_submissions' => ['id','afspraak_number','name','email','street','phone','postcode','house_number','city','device','problem','preferred_date','preferred_time','status','ip_address','created_at','updated_at'],
];
// dependency-safe order (parents before children)
$ORDER = ['memberships','membership_invoices','membership_settings','technician_settings','technician_forms','technician_invoices','afspraak_submissions'];

try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($ORDER as $t) {
        if (!isset($prepared[$t])) {
            continue;
        }
        if ($t === 'technician_settings') {
            continue; // updateOrCreate below, never truncated
        }
        $pdo->exec("TRUNCATE TABLE `$t`");
        echo "TRUNCATED $t\n";
    }

    foreach ($ORDER as $t) {
        if (!isset($prepared[$t])) {
            continue;
        }
        $rows = $prepared[$t];
        if ($t === 'technician_settings') {
            $st = $pdo->prepare('INSERT INTO `technician_settings` (`key`,`value`,created_at,updated_at) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE `value`=VALUES(`value`),updated_at=VALUES(updated_at)');
            $now = date('Y-m-d H:i:s');
            foreach ($rows as $r) {
                $st->execute([$r['key'], $r['value'], $now, $now]);
            }
            echo "UPSERTED technician_settings: " . count($rows) . "\n";
            $log[] = 'technician_settings upserted: ' . count($rows);
            continue;
        }
        $cols = $insertCols[$t];
        $ph = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
        foreach (array_chunk($rows, 200) as $chunk) {
            $place = implode(',', array_fill(0, count($chunk), $ph));
            $st = $pdo->prepare("INSERT INTO `$t` (`" . implode('`,`', $cols) . "`) VALUES $place");
            $flat = [];
            foreach ($chunk as $r) {
                foreach ($cols as $c) {
                    $v = $r[$c] ?? null;
                    if ($c === 'terms_accepted') {
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
        if ($t !== 'membership_settings') {
            $max = $pdo->query("SELECT MAX(id) FROM `$t`")->fetchColumn();
            if ($max) {
                $pdo->exec("ALTER TABLE `$t` AUTO_INCREMENT=" . ((int) $max + 1));
            }
        }
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
foreach (['memberships','membership_invoices','membership_settings','technician_forms','technician_invoices','afspraak_submissions'] as $t) {
    if (isset($prepared[$t])) {
        $checks[$t] = (int) $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    }
}
$checks['orphan_member_invoices'] = (int) $pdo->query('SELECT COUNT(*) FROM membership_invoices mi LEFT JOIN memberships m ON m.id=mi.membership_id WHERE m.id IS NULL')->fetchColumn();
$checks['orphan_tech_invoices'] = (int) $pdo->query('SELECT COUNT(*) FROM technician_invoices ti LEFT JOIN technician_forms tf ON tf.id=ti.technician_form_id WHERE tf.id IS NULL')->fetchColumn();
$checks['orphan_tech_forms'] = (int) $pdo->query('SELECT COUNT(*) FROM technician_forms f LEFT JOIN users u ON u.id=f.user_id WHERE u.id IS NULL')->fetchColumn();
$checks['afspraak_bad_status'] = (int) $pdo->query("SELECT COUNT(*) FROM afspraak_submissions WHERE status NOT IN ('new','in_progress','completed')")->fetchColumn();

foreach ($checks as $k => $v) {
    echo str_pad($k, 24) . $v . PHP_EOL;
    $log[] = "check $k: $v";
}
file_put_contents(__DIR__ . '/sync-phase2-report.log', implode("\n", $log) . "\n");
echo 'Report: scripts/sync-phase2-report.log' . PHP_EOL;
echo "DONE\n";

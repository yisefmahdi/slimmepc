<?php
/**
 * sync-delta-2026-10-11.php — INSERT-ONLY delta sync (no TRUNCATE, ever).
 *
 * Adds rows that exist in the 11-10 dump but not in the DB:
 *  - device_receipts 231-242 ......... same ids (no collision possible)
 *  - appointments 168,169,170 ....... same ids (+ hardware_appointments lookup)
 *  - addresses 65 → REMAPPED (live id 65 taken)
 *  - orders 65 → REMAPPED (live ids 65-68 are real customer orders)
 *  - order_items 66 → REMAPPED (live id 66 taken), product_id NULL
 *  - order_invoices 60 → REMAPPED (live id 60 taken), keeps INV-2026-000060
 *
 * Remap is fixed in $REMAP below (verified against live DB on 2026-10-11).
 * Any id that already exists and is NOT in $REMAP is skipped + logged
 * (never overwritten, never deleted).
 *
 * Transforms are identical to sync-phase1/2/3a (same parser, same rules).
 *
 * Usage: --file=dump.sql [--db/--host/--port/--user/--pass/--config] [--dry-run] [--yes]
 * Real runs backup the 6 tables first. --yes required non-interactive.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$opt = getopt('', [
    'file:', 'host::', 'port::', 'db::', 'user::', 'pass::',
    'dry-run', 'yes', 'config::',
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
if (!empty($opt['config'])) {
    $cfg = json_decode((string) @file_get_contents((string) $opt['config']), true);
    if (!is_array($cfg)) {
        fwrite(STDERR, "ERROR: cannot read --config file.\n");
        exit(2);
    }
    foreach (['host' => 'host', 'port' => 'port', 'db' => 'dbName', 'user' => 'dbUser', 'pass' => 'dbPass'] as $k => $var) {
        if (array_key_exists($k, $cfg)) {
            $$var = $k === 'port' ? (int) $cfg[$k] : (string) $cfg[$k];
        }
    }
}

// Wanted dump ids per table (+ fixed remap for live collisions)
$WANT = [
    'device_receipts' => [231,232,233,234,235,236,237,238,239,240,241,242],
    'appointments' => [168,169,170],
    'addresses' => [65],
    'orders' => [65],
    'order_items' => [66],
    'order_invoices' => [60], // old `invoices` table
];
$REMAP = [ // dump_id => new_id (live rows must never be touched)
    'addresses' => [65 => 66],
    'orders' => [65 => 69],
    'order_items' => [66 => 71],
    'order_invoices' => [60 => 64],
];
// FK rewiring after remap: [table => [col => [old_ref => new_ref]]]
$REWIRE = [
    'orders' => [],
    'order_items' => ['order_id' => [65 => 69]],
    'order_invoices' => ['order_id' => [65 => 69]],
];

$log = [];
$log[] = '=== sync-delta-2026-10-11 ' . date('Y-m-d H:i:s') . ' | dry-run=' . ($dryRun ? 'yes' : 'no') . ' ===';
$warn = static function (string $m) use (&$log): void {
    $log[] = 'WARN: ' . $m;
    echo 'WARN: ' . $m . PHP_EOL;
};

// ------------------------------------------------------- parser (same as sync scripts)
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
                $warn("`$table`: column/value count mismatch, row skipped");
                continue;
            }
            $rows[(int) $v[array_search('id', $cols, true)]] = array_combine($cols, $v);
        }
    }
    return $rows;
}

// ------------------------------------------------------------- load wanted rows
$dumpTables = ['device_receipts' => 'device_receipts', 'appointments' => 'appointments', 'hardware_appointments' => 'hardware_appointments', 'addresses' => 'addresses', 'orders' => 'orders', 'order_items' => 'order_items', 'invoices' => 'invoices', 'users' => 'users'];
$D = [];
foreach ($dumpTables as $dt => $label) {
    $D[$label] = extractTable($raw, $dt, $warn);
}
$userIds = [];
foreach ($D['users'] as $id => $u) {
    $userIds[$id] = true;
}
$hwByApp = [];
foreach ($D['hardware_appointments'] as $id => $h) {
    $aid = (int) $h['appointment_id'];
    if (!isset($hwByApp[$aid])) {
        $hwByApp[$aid] = (string) $h['device_type'];
    }
}
$orderEmail = [];
foreach ($D['orders'] as $id => $o) {
    $orderEmail[$id] = (string) ($o['customer_email'] ?? '');
}
$orderContact = [];
foreach ($D['orders'] as $id => $o) {
    if (!empty($o['billing_address_id'])) {
        $orderContact[(int) $o['billing_address_id']] = [
            'first' => (string) ($o['first_name'] ?? ''), 'last' => (string) ($o['last_name'] ?? ''),
            'email' => (string) ($o['customer_email'] ?? ''),
        ];
    }
}
$userName = [];
$userMail = [];
foreach ($D['users'] as $id => $u) {
    $userName[$id] = (string) ($u['name'] ?? '');
    $userMail[$id] = (string) ($u['email'] ?? '');
}
$splitName = static function (string $full): array {
    $full = trim(preg_replace('/\s+/', ' ', $full));
    if ($full === '') {
        return ['Onbekend', 'Onbekend'];
    }
    $pos = strrpos($full, ' ');
    return $pos === false ? [$full, '-'] : [substr($full, 0, $pos), substr($full, $pos + 1)];
};
$serialJunk = ['niet nodig','niet','geen','nee','onbekend','nieuwe','staat niet','nvt','-','geeen','niks'];

// ------------------------------------------------------------- build delta rows
$delta = []; // target_table => list of row assoc (with final ids)

foreach ($WANT['device_receipts'] as $id) {
    if (!isset($D['device_receipts'][$id])) {
        $warn("device_receipts $id not in dump → skipped");
        continue;
    }
    $r = $D['device_receipts'][$id];
    $s = $r['serial_number'];
    if (is_string($s) && in_array(mb_strtolower(trim($s)), $serialJunk, true)) {
        $s = null;
    }
    $delta['device_receipts'][] = [
        'id' => $id, 'customer_name' => $r['customer_name'], 'customer_email' => $r['customer_email'],
        'device_type' => $r['device_type'], 'phone_number' => $r['phone_number'],
        'serial_number' => ($s === '' ? null : $s), 'received_at' => $r['received_at'],
        'notes' => $r['notes'], 'created_at' => $r['created_at'], 'updated_at' => $r['updated_at'],
        'type' => 'laptop', 'status' => 'completed',
    ];
}

$statusMap = ['pending' => 'new', 'confirmed' => 'completed', 'cancelled' => 'new'];
foreach ($WANT['appointments'] as $aid) {
    if (!isset($D['appointments'][$aid])) {
        $warn("appointments $aid not in dump → skipped");
        continue;
    }
    $a = $D['appointments'][$aid];
    $title = trim((string) ($a['title'] ?? ''));
    $name = trim((string) $a['full_name']);
    if ($title !== '') {
        $name = $title . ' ' . $name;
    }
    $st = (string) $a['status'];
    $problem = (string) $a['problem_description'];
    if (!empty($a['notes'])) {
        $problem .= "\n\n[notitie] " . (string) $a['notes'];
    }
    if ($st === 'cancelled') {
        $problem = '[geannuleerd] ' . $problem;
    }
    $device = $hwByApp[$aid] ?? (string) $a['appointment_type'];
    $prefDate = null;
    $prefTime = null;
    if (!empty($a['appointment_date']) && ($ts = strtotime((string) $a['appointment_date'])) !== false) {
        $prefDate = date('Y-m-d', $ts);
        $prefTime = date('H:i', $ts);
    }
    $year = (!empty($a['created_at']) && ($t = strtotime((string) $a['created_at'])) !== false) ? date('Y', $t) : date('Y');
    $dash = static fn ($v) => ($v === null || trim((string) $v) === '') ? '-' : (string) $v;
    $delta['afspraak_submissions'][] = [
        'id' => $aid,
        'afspraak_number' => sprintf('AF-%s-%05d', $year, $aid),
        'name' => $name !== '' ? $name : '-',
        'email' => $dash($a['email']), 'street' => $dash($a['street']), 'phone' => $dash($a['phone']),
        'postcode' => $dash($a['postcode']), 'house_number' => $dash($a['house_number']), 'city' => $dash($a['city']),
        'device' => $device !== '' ? $device : 'algemeen', 'problem' => $problem,
        'preferred_date' => $prefDate, 'preferred_time' => $prefTime,
        'status' => $statusMap[$st] ?? 'new', 'ip_address' => null,
        'created_at' => $a['created_at'], 'updated_at' => $a['updated_at'],
    ];
}

foreach ($WANT['addresses'] as $id) {
    if (!isset($D['addresses'][$id])) {
        $warn("addresses $id not in dump → skipped");
        continue;
    }
    $a = $D['addresses'][$id];
    $uid = $a['user_id'] === null ? null : (int) $a['user_id'];
    if ($uid !== null && !isset($userIds[$uid])) {
        $uid = null;
    }
    if ($uid !== null && trim($userName[$uid] ?? '') !== '') {
        [$first, $last] = $splitName($userName[$uid]);
        $email = $userMail[$uid] !== '' ? $userMail[$uid] : null;
    } elseif (isset($orderContact[$id])) {
        $c = $orderContact[$id];
        $first = $c['first'] !== '' ? $c['first'] : 'Onbekend';
        $last = $c['last'] !== '' ? $c['last'] : 'Onbekend';
        $email = $c['email'] !== '' ? $c['email'] : null;
    } else {
        [$first, $last] = ['Onbekend', 'Onbekend'];
        $email = null;
    }
    $newId = $REMAP['addresses'][$id] ?? $id;
    if ($newId !== $id) {
        $log[] = "addresses $id → remapped to $newId (live collision)";
    }
    $delta['addresses'][] = [
        'id' => $newId, 'user_id' => $uid, 'first_name' => $first, 'last_name' => $last,
        'street' => (string) $a['street_address'], 'house_number' => (string) $a['home_nummber'],
        'addition' => null, 'postcode' => (string) $a['postal_code'], 'city' => (string) $a['city'],
        'country' => ((string) ($a['country'] ?? '') !== '') ? (string) $a['country'] : 'Nederland',
        'phone' => (string) $a['phone_number'], 'email' => $email,
        'type' => in_array($a['type'], ['billing', 'shipping'], true) ? $a['type'] : 'billing',
        'created_at' => $a['created_at'], 'updated_at' => $a['updated_at'],
    ];
}

$payMap = ['paid' => 'paid', 'unpaid' => 'pending'];
foreach ($WANT['orders'] as $id) {
    if (!isset($D['orders'][$id])) {
        $warn("orders $id not in dump → skipped");
        continue;
    }
    $o = $D['orders'][$id];
    $newId = $REMAP['orders'][$id] ?? $id;
    if ($newId !== $id) {
        $log[] = "orders $id → remapped to $newId (live collision)";
    }
    $uid = $o['user_id'] === null ? null : (int) $o['user_id'];
    $bill = $o['billing_address_id'] === null ? null : (int) $o['billing_address_id'];
    $ship = $o['shipping_address_id'] === null ? null : (int) $o['shipping_address_id'];
    // rewire FK refs that were remapped
    if (isset($REMAP['addresses'][$bill])) {
        $bill = $REMAP['addresses'][$bill];
    }
    if (isset($REMAP['addresses'][$ship])) {
        $ship = $REMAP['addresses'][$ship];
    }
    $delta['orders'][] = [
        'id' => $newId, 'order_number' => (string) $o['order_number'], 'user_id' => $uid,
        'billing_address_id' => $bill, 'shipping_address_id' => $ship,
        'klantnummer' => $o['klantnummer'], 'customer_email' => (string) $o['customer_email'],
        'customer_phone' => (string) $o['customer_phone'], 'subtotal' => $o['subtotal'],
        'tax_percentage' => $o['tax_percentage'], 'tax_amount' => $o['tax_amount'],
        'discount_code' => $o['discount_code'], 'coupon_id' => null, 'discount_amount' => 0,
        'shipping_method' => 'delivery', 'shipping_cost' => 0, 'total_price' => $o['total_price'],
        'payment_status' => $payMap[(string) $o['payment_status']] ?? 'pending',
        'payment_method' => $o['payment_method'] === null ? null : (string) $o['payment_method'],
        'mollie_payment_id' => null, 'order_status' => (string) $o['order_status'],
        'created_at' => $o['created_at'], 'updated_at' => $o['updated_at'],
    ];
}

foreach ($WANT['order_items'] as $id) {
    if (!isset($D['order_items'][$id])) {
        $warn("order_items $id not in dump → skipped");
        continue;
    }
    $it = $D['order_items'][$id];
    $newId = $REMAP['order_items'][$id] ?? $id;
    $oid = (int) $it['order_id'];
    if (isset($REWIRE['order_items']['order_id'][$oid])) {
        $oid = $REWIRE['order_items']['order_id'][$oid];
    }
    $delta['order_items'][] = [
        'id' => $newId, 'order_id' => $oid, 'product_id' => null,
        'product_name' => (string) $it['product_name'], 'product_price' => $it['product_price'],
        'quantity' => (int) $it['quantity'], 'total_price' => $it['total_price'],
        'created_at' => $it['created_at'], 'updated_at' => $it['updated_at'],
    ];
}

foreach ($WANT['order_invoices'] as $id) {
    if (!isset($D['invoices'][$id])) {
        $warn("invoices $id not in dump → skipped");
        continue;
    }
    $inv = $D['invoices'][$id];
    $newId = $REMAP['order_invoices'][$id] ?? $id;
    $oid = (int) $inv['order_id'];
    if (isset($REWIRE['order_invoices']['order_id'][$oid])) {
        $oid = $REWIRE['order_invoices']['order_id'][$oid];
    }
    $year = (!empty($inv['invoice_date']) && ($t = strtotime((string) $inv['invoice_date'])) !== false) ? date('Y', $t) : date('Y');
    // keep original dump-id-based number only when it cannot collide; else suffix with new id
    $newNum = sprintf('INV-%s-%06d', $year, $id);
    $street = (string) $inv['street_address'];
    if (!empty($inv['home_number'])) {
        $street .= ' ' . (string) $inv['home_number'];
    }
    $delta['order_invoices'][] = [
        'id' => $newId, 'order_id' => $oid, 'invoice_number' => $newNum,
        'invoice_date' => $inv['invoice_date'], 'customer_name' => (string) $inv['customer_name'],
        'customer_email' => $orderEmail[$id] ?? null, 'customer_phone' => (string) $inv['customer_phone'],
        'street_address' => $street, 'postal_code' => (string) $inv['postal_code'],
        'city' => (string) $inv['city'], 'klantnummer' => (string) $inv['klantnummer'],
        'subtotal' => $inv['subtotal'], 'tax_percentage' => $inv['tax_percentage'],
        'tax_amount' => $inv['tax_amount'], 'discount_amount' => 0, 'shipping_cost' => 0,
        'total' => $inv['total'], 'payment_method' => (string) $inv['payment_method'],
        'pdf_path' => $inv['pdf_path'], 'created_at' => $inv['created_at'], 'updated_at' => $inv['updated_at'],
    ];
    $log[] = "invoice map {$inv['invoice_number']} → $newNum (order $oid)";
}

foreach ($delta as $t => $rows) {
    echo str_pad($t . ' delta', 24) . count($rows) . " rows\n";
    $log[] = "$t delta: " . count($rows);
}

if ($dryRun) {
    echo "\nDRY-RUN: nothing written.\n";
    file_put_contents(__DIR__ . '/sync-delta-report.log', implode("\n", $log) . "\n");
    echo 'Report: scripts/sync-delta-report.log' . PHP_EOL;
    exit(0);
}

// ------------------------------------------------------------------ confirm
if (!$yes && !(function_exists('stream_isatty') && stream_isatty(STDIN))) {
    fwrite(STDERR, "INSERT-ONLY mode, but refusing without --yes in non-interactive mode. Re-run with --yes.\n");
    exit(3);
}
if (!$yes) {
    echo "\nINSERT-ONLY (no TRUNCATE, no DELETE) into `$dbName`: " . implode(',', array_keys($delta)) . ".\nType YES to continue: ";
    if (trim((string) fgets(STDIN)) !== 'YES') {
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

// ------------------------------------------------------------------ backup (safety snapshot, no truncate follows)
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}
$backupFile = $backupDir . '/delta-pre-' . date('Ymd-His') . '.sql';
$dumpBins = ['mysqldump'];
$laragonDump = 'C:\\laragon\\bin\\mysql\\mysql-5.7.39-winx64\\bin\\mysqldump.exe';
if (is_file($laragonDump)) {
    $dumpBins[] = $laragonDump;
}
$backedUp = false;
foreach ($dumpBins as $bin) {
    $tables = 'device_receipts afspraak_submissions addresses orders order_items order_invoices';
    $cmd = ($dbPass === ''
        ? sprintf('"%s" -h %s -P %d -u %s %s %s', $bin, $host, $port, $dbUser, $dbName, $tables)
        : sprintf('"%s" -h %s -P %d -u %s -p%s %s %s', $bin, $host, $port, $dbUser, escapeshellarg($dbPass), $dbName, $tables))
        . ' > ' . escapeshellarg($backupFile) . ' 2>&1';
    exec($cmd, $o, $code);
    if ($code === 0 && is_file($backupFile) && filesize($backupFile) > 0) {
        $backedUp = true;
        break;
    }
}
if (!$backedUp) {
    fwrite(STDERR, "ERROR: backup failed, aborting (nothing written).\n");
    exit(5);
}
echo "Backup: $backupFile (" . filesize($backupFile) . " bytes)\n";
$log[] = "backup: $backupFile";

// ------------------------------------------------------------------ insert-only import
$insertCols = [
    'device_receipts' => ['id','customer_name','customer_email','device_type','phone_number','serial_number','received_at','notes','created_at','updated_at','type','status'],
    'afspraak_submissions' => ['id','afspraak_number','name','email','street','phone','postcode','house_number','city','device','problem','preferred_date','preferred_time','status','ip_address','created_at','updated_at'],
    'addresses' => ['id','user_id','first_name','last_name','street','house_number','addition','postcode','city','country','phone','email','type','created_at','updated_at'],
    'orders' => ['id','order_number','user_id','billing_address_id','shipping_address_id','klantnummer','customer_email','customer_phone','subtotal','tax_percentage','tax_amount','discount_code','coupon_id','discount_amount','shipping_method','shipping_cost','total_price','payment_status','payment_method','mollie_payment_id','order_status','created_at','updated_at'],
    'order_items' => ['id','order_id','product_id','product_name','product_price','quantity','total_price','created_at','updated_at'],
    'order_invoices' => ['id','order_id','invoice_number','invoice_date','customer_name','customer_email','customer_phone','street_address','postal_code','city','klantnummer','subtotal','tax_percentage','tax_amount','discount_amount','shipping_cost','total','payment_method','pdf_path','created_at','updated_at'],
];
$ORDER = ['device_receipts', 'afspraak_submissions', 'addresses', 'orders', 'order_items', 'order_invoices'];
$uniqChecks = [ // business-key guards (abort row on collision)
    'orders' => ['order_number'],
    'order_invoices' => ['invoice_number'],
    'afspraak_submissions' => ['afspraak_number'],
];

try {
    $counts = [];
    foreach ($ORDER as $t) {
        if (empty($delta[$t])) {
            continue;
        }
        $cols = $insertCols[$t];
        $inserted = 0;
        $skipped = 0;
        foreach ($delta[$t] as $r) {
            $exists = (int) $pdo->query("SELECT COUNT(*) FROM `$t` WHERE id=" . (int) $r['id'])->fetchColumn();
            if ($exists > 0) {
                $warn("$t id {$r['id']}: already exists → SKIPPED (never overwritten)");
                $skipped++;
                continue;
            }
            $collision = false;
            foreach ($uniqChecks[$t] ?? [] as $uk) {
                $q = $pdo->prepare("SELECT COUNT(*) FROM `$t` WHERE `$uk`=?");
                $q->execute([$r[$uk]]);
                if ((int) $q->fetchColumn() > 0) {
                    $warn("$t id {$r['id']}: $uk '{$r[$uk]}' already used → SKIPPED");
                    $collision = true;
                    break;
                }
            }
            if ($collision) {
                $skipped++;
                continue;
            }
            $ph = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
            $st = $pdo->prepare("INSERT INTO `$t` (`" . implode('`,`', $cols) . "`) VALUES $ph");
            $flat = [];
            foreach ($cols as $c) {
                $flat[] = $r[$c] ?? null;
            }
            $st->execute($flat);
            $inserted++;
        }
        echo "DELTA $t: inserted=$inserted skipped=$skipped\n";
        $log[] = "$t inserted=$inserted skipped=$skipped";
        $counts[$t] = $inserted;
        $max = $pdo->query("SELECT MAX(id) FROM `$t`")->fetchColumn();
        if ($max) {
            $pdo->exec("ALTER TABLE `$t` AUTO_INCREMENT=" . ((int) $max + 1));
        }
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'DELTA FAILED: ' . $e->getMessage() . "\nPartial inserts may exist — see report + backup: " . $backupFile . "\n");
    exit(6);
}

// -------------------------------------------------------------- verification
$checks = [];
foreach (['device_receipts','afspraak_submissions','addresses','orders','order_items','order_invoices'] as $t) {
    $checks[$t] = (int) $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
}
foreach ($checks as $k => $v) {
    echo str_pad($k, 24) . $v . PHP_EOL;
    $log[] = "check $k: $v";
}
file_put_contents(__DIR__ . '/sync-delta-report.log', implode("\n", $log) . "\n");
echo 'Report: scripts/sync-delta-report.log' . PHP_EOL;
echo "DONE\n";

<?php
/**
 * sync-phase3-orders.php — Legacy → New DB sync (PHASE 3a: shop orders, NO products)
 *
 * Tables: orders (29) + order_items (~30, product_id=NULL for all) +
 *         order_invoices from old `invoices` (~26, new INV- numbers).
 *
 * Source: phpMyAdmin .sql dump of the OLD system (--file=...).
 * Target: MySQL (defaults = local Laragon slimmepc_2026).
 *
 * FAITHFULNESS RULE (client): import EVERYTHING as-is. payment_method kept in
 * original text (IDEAL/credit-card/PayPal). Only technically-forced mappings
 * (NOT NULL / FK / enum / unique) are applied and each one is logged.
 *
 * Key decisions (locked with client):
 *  - products NOT synced → order_items.product_id = NULL for ALL rows
 *    (new products table holds different seed products; linking by id would corrupt data)
 *  - old invoices → order_invoices with NEW numbers INV-<year>-<oldid6>;
 *    old→new number map is written to the report
 *  - old payment_status paid→paid / unpaid→pending
 *  - dropped columns without target (logged): customer_gender, customer_type,
 *    first_name, last_name, warranty_info, company_details; home_number is
 *    appended to street_address (no separate column in new table)
 *
 * Usage: same flags as sync-phase1/2 (--file --db --host --port --user --pass
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

$ALL_TABLES = ['orders', 'order_items', 'order_invoices'];
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
$log[] = '=== sync-phase3-orders ' . date('Y-m-d H:i:s') . ' | dry-run=' . ($dryRun ? 'yes' : 'no') . ' ===';
$warn = static function (string $m) use (&$log): void {
    $log[] = 'WARN: ' . $m;
    echo 'WARN: ' . $m . PHP_EOL;
};

// ------------------------------------------------------- SQL dump parsing
// (same quote-aware parser as sync-phase1/2.php)
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
    'orders' => 'orders',
    'order_items' => 'order_items',
    'invoices' => 'invoices',
    'users' => 'users',         // reference only (FK existence check)
    'addresses' => 'addresses', // reference only (FK existence check)
];
$data = [];
foreach ($NEED as $dumpTable => $label) {
    $data[$label] = extractTable($raw, $dumpTable, $warn);
    echo str_pad($label, 18) . count($data[$label]) . " rows parsed\n";
    $log[] = "$label parsed: " . count($data[$label]);
}

$userIds = [];
foreach ($data['users'] as $u) {
    $userIds[(int) $u['id']] = true;
}
$addrIds = [];
foreach ($data['addresses'] as $a) {
    $addrIds[(int) $a['id']] = true;
}
// order email lookup (for order_invoices.customer_email — old invoices lack it)
$orderEmail = [];
foreach ($data['orders'] as $o) {
    $orderEmail[(int) $o['id']] = (string) ($o['customer_email'] ?? '');
}

// ------------------------------------------------------------- transforms
$prepared = [];
$payMap = ['paid' => 'paid', 'unpaid' => 'pending'];

// ---- orders
if (in_array('orders', $wanted, true)) {
    $seenNum = [];
    $out = [];
    foreach ($data['orders'] as $o) {
        $num = (string) ($o['order_number'] ?? '');
        if ($num === '' || isset($seenNum[$num])) {
            $warn('orders: duplicate/empty order_number skipped: ' . var_export($num, true));
            continue;
        }
        $seenNum[$num] = true;
        $uid = $o['user_id'] === null ? null : (int) $o['user_id'];
        if ($uid !== null && !isset($userIds[$uid])) {
            $warn("orders $num: orphan user_id $uid → NULL");
            $uid = null;
        }
        $bill = $o['billing_address_id'] === null ? null : (int) $o['billing_address_id'];
        if ($bill !== null && !isset($addrIds[$bill])) {
            $warn("orders $num: billing_address $bill missing → NULL");
            $bill = null;
        }
        $ship = $o['shipping_address_id'] === null ? null : (int) $o['shipping_address_id'];
        if ($ship !== null && !isset($addrIds[$ship])) {
            $warn("orders $num: shipping_address $ship missing → NULL");
            $ship = null;
        }
        $ps = (string) $o['payment_status'];
        if (!isset($payMap[$ps])) {
            $warn("orders $num: unknown payment_status '$ps' → pending");
        }
        $out[] = [
            'id' => (int) $o['id'],
            'order_number' => $num,
            'user_id' => $uid,
            'billing_address_id' => $bill,
            'shipping_address_id' => $ship,
            'klantnummer' => $o['klantnummer'],
            'customer_email' => (string) $o['customer_email'],
            'customer_phone' => (string) $o['customer_phone'],
            'subtotal' => $o['subtotal'],
            'tax_percentage' => $o['tax_percentage'],
            'tax_amount' => $o['tax_amount'],
            'discount_code' => $o['discount_code'],
            'coupon_id' => null,
            'discount_amount' => 0,
            'shipping_method' => 'delivery',
            'shipping_cost' => 0,
            'total_price' => $o['total_price'],
            'payment_status' => $payMap[$ps] ?? 'pending',
            'payment_method' => $o['payment_method'] === null ? null : (string) $o['payment_method'],
            'mollie_payment_id' => null,
            'order_status' => (string) $o['order_status'],
            'created_at' => $o['created_at'],
            'updated_at' => $o['updated_at'],
        ];
    }
    $prepared['orders'] = $out;
    $orderIds = [];
    foreach ($out as $r) {
        $orderIds[(int) $r['id']] = true;
    }
}

// ---- order_items (product_id = NULL for all — products not synced)
if (in_array('order_items', $wanted, true)) {
    if (!isset($orderIds)) {
        $orderIds = [];
        foreach ($data['orders'] as $o) {
            $orderIds[(int) $o['id']] = true;
        }
    }
    $out = [];
    foreach ($data['order_items'] as $it) {
        $oid = (int) $it['order_id'];
        if (!isset($orderIds[$oid])) {
            $warn("order_items id {$it['id']}: order $oid missing → row SKIPPED to protect FK");
            continue;
        }
        if ($it['product_id'] !== null) {
            $log[] = "order_items id {$it['id']}: old product_id {$it['product_id']} → NULL (products not synced)";
        }
        $out[] = [
            'id' => (int) $it['id'],
            'order_id' => $oid,
            'product_id' => null,
            'product_name' => (string) $it['product_name'],
            'product_price' => $it['product_price'],
            'quantity' => (int) $it['quantity'],
            'total_price' => $it['total_price'],
            'created_at' => $it['created_at'],
            'updated_at' => $it['updated_at'],
        ];
    }
    $prepared['order_items'] = $out;
}

// ---- order_invoices from old invoices (ALL rows, new INV- numbers)
if (in_array('order_invoices', $wanted, true)) {
    if (!isset($orderIds)) {
        $orderIds = [];
        foreach ($data['orders'] as $o) {
            $orderIds[(int) $o['id']] = true;
        }
    }
    $seenInv = [];
    $out = [];
    foreach ($data['invoices'] as $inv) {
        $oid = (int) $inv['order_id'];
        if (!isset($orderIds[$oid])) {
            $warn("invoices id {$inv['id']}: order $oid missing → row SKIPPED to protect FK");
            continue;
        }
        $oldNum = (string) ($inv['invoice_number'] ?? '');
        $year = !empty($inv['invoice_date']) && ($t = strtotime((string) $inv['invoice_date'])) !== false
            ? date('Y', $t) : date('Y');
        $newNum = sprintf('INV-%s-%06d', $year, (int) $inv['id']);
        if (isset($seenInv[$newNum])) {
            $warn("invoices id {$inv['id']}: generated number $newNum collides → row SKIPPED");
            continue;
        }
        $seenInv[$newNum] = true;
        $log[] = "invoice map $oldNum → $newNum (order $oid)";
        $street = (string) $inv['street_address'];
        if (!empty($inv['home_number'])) {
            $street .= ' ' . (string) $inv['home_number'];
        }
        $out[] = [
            'id' => (int) $inv['id'],
            'order_id' => $oid,
            'invoice_number' => $newNum,
            'invoice_date' => $inv['invoice_date'],
            'customer_name' => (string) $inv['customer_name'],
            'customer_email' => $orderEmail[$oid] ?? null,
            'customer_phone' => (string) $inv['customer_phone'],
            'street_address' => $street,
            'postal_code' => (string) $inv['postal_code'],
            'city' => (string) $inv['city'],
            'klantnummer' => (string) $inv['klantnummer'],
            'subtotal' => $inv['subtotal'],
            'tax_percentage' => $inv['tax_percentage'],
            'tax_amount' => $inv['tax_amount'],
            'discount_amount' => 0,
            'shipping_cost' => 0,
            'total' => $inv['total'],
            'payment_method' => (string) $inv['payment_method'],
            'pdf_path' => $inv['pdf_path'],
            'created_at' => $inv['created_at'],
            'updated_at' => $inv['updated_at'],
        ];
    }
    $prepared['order_invoices'] = $out;
}

foreach ($prepared as $t => $rows) {
    echo str_pad($t . ' ready', 20) . count($rows) . " rows\n";
    $log[] = "$t ready: " . count($rows);
}

if ($dryRun) {
    echo "\nDRY-RUN: nothing written. " . count($log) . " log lines.\n";
    file_put_contents(__DIR__ . '/sync-phase3-orders-report.log', implode("\n", $log) . "\n");
    echo 'Report: scripts/sync-phase3-orders-report.log' . PHP_EOL;
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
$backupFile = $backupDir . "/phase3-orders-$stamp.sql";
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
    'orders' => ['id','order_number','user_id','billing_address_id','shipping_address_id','klantnummer','customer_email','customer_phone','subtotal','tax_percentage','tax_amount','discount_code','coupon_id','discount_amount','shipping_method','shipping_cost','total_price','payment_status','payment_method','mollie_payment_id','order_status','created_at','updated_at'],
    'order_items' => ['id','order_id','product_id','product_name','product_price','quantity','total_price','created_at','updated_at'],
    'order_invoices' => ['id','order_id','invoice_number','invoice_date','customer_name','customer_email','customer_phone','street_address','postal_code','city','klantnummer','subtotal','tax_percentage','tax_amount','discount_amount','shipping_cost','total','payment_method','pdf_path','created_at','updated_at'],
];
$ORDER = ['orders', 'order_items', 'order_invoices'];

try {
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    foreach ($ORDER as $t) {
        if (!isset($prepared[$t])) {
            continue;
        }
        $pdo->exec("TRUNCATE TABLE `$t`");
        echo "TRUNCATED $t\n";
    }

    foreach ($ORDER as $t) {
        if (!isset($prepared[$t])) {
            continue;
        }
        $rows = $prepared[$t];
        $cols = $insertCols[$t];
        $ph = '(' . implode(',', array_fill(0, count($cols), '?')) . ')';
        foreach (array_chunk($rows, 200) as $chunk) {
            $place = implode(',', array_fill(0, count($chunk), $ph));
            $st = $pdo->prepare("INSERT INTO `$t` (`" . implode('`,`', $cols) . "`) VALUES $place");
            $flat = [];
            foreach ($chunk as $r) {
                foreach ($cols as $c) {
                    $flat[] = $r[$c] ?? null;
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
foreach (['orders', 'order_items', 'order_invoices'] as $t) {
    if (isset($prepared[$t])) {
        $checks[$t] = (int) $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    }
}
$checks['orphan_items'] = (int) $pdo->query('SELECT COUNT(*) FROM order_items i LEFT JOIN orders o ON o.id=i.order_id WHERE o.id IS NULL')->fetchColumn();
$checks['orphan_invoices'] = (int) $pdo->query('SELECT COUNT(*) FROM order_invoices i LEFT JOIN orders o ON o.id=i.order_id WHERE o.id IS NULL')->fetchColumn();
$checks['items_with_product'] = (int) $pdo->query('SELECT COUNT(*) FROM order_items WHERE product_id IS NOT NULL')->fetchColumn();
$checks['dup_order_numbers'] = (int) $pdo->query('SELECT COUNT(*) FROM (SELECT order_number FROM orders GROUP BY order_number HAVING COUNT(*)>1) d')->fetchColumn();
$checks['dup_invoice_numbers'] = (int) $pdo->query('SELECT COUNT(*) FROM (SELECT invoice_number FROM order_invoices GROUP BY invoice_number HAVING COUNT(*)>1) d')->fetchColumn();

foreach ($checks as $k => $v) {
    echo str_pad($k, 22) . $v . PHP_EOL;
    $log[] = "check $k: $v";
}
file_put_contents(__DIR__ . '/sync-phase3-orders-report.log', implode("\n", $log) . "\n");
echo 'Report: scripts/sync-phase3-orders-report.log' . PHP_EOL;
echo "DONE\n";

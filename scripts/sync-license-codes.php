<?php
/**
 * sync-license-codes.php — Legacy → New DB sync (digital products: license codes)
 *
 * Tables: license_codes (full import, old IDs preserved) + product link backfill
 *         (products.download_32bit_url/download_64bit_url/manual_url filled
 *          from the old dump where the new product still has them empty).
 *
 * Source: phpMyAdmin .sql dump of the OLD system (--file=...).
 *         Take a FRESH dump (phpMyAdmin → Export) so sold codes + their orders
 *         are current. Old local phase*.sql dumps do NOT contain license_codes.
 * Target: MySQL (defaults = local Laragon slimmepc_2026).
 *
 * Mapping (logged):
 *  - old product → new product by slug first, then case-insensitive title.
 *    Every new product that receives codes is flagged is_digital=1.
 *  - sold codes: old order --(order_number)--> new order id (orders were
 *    synced by sync-phase3-orders). order_item_id = first item of that new
 *    order with the same new product_id. If the old order is missing in the
 *    new DB the code stays `sold` with order_id=NULL + WARN (never resold).
 *  - codes whose `code` already exists in the new table are skipped (unique).
 *
 * Usage: same flags as sync-phase1/2/3 (--file --db --host --port --user --pass
 *        --tables=... --dry-run --yes). Real runs backup first, need --yes.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

// ---------------------------------------------------------------- options
$opt = getopt('', [
    'file:', 'host::', 'port::', 'db::', 'user::', 'pass::',
    'tables::', 'dry-run', 'yes', 'config::',
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

$ALL_TABLES = ['license_codes', 'product_links'];
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
$log[] = '=== sync-license-codes ' . date('Y-m-d H:i:s') . ' | dry-run=' . ($dryRun ? 'yes' : 'no') . ' ===';
$warn = static function (string $m) use (&$log): void {
    $log[] = 'WARN: ' . $m;
    echo 'WARN: ' . $m . PHP_EOL;
};

// ------------------------------------------------------- SQL dump parsing
// (same quote-aware parser as sync-phase1/2/3.php)
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

// ------------------------------------------------------------- extract old
$NEED = [
    'license_codes' => 'license_codes',
    'orders' => 'orders',     // reference only (old id → order_number)
    'products' => 'products', // reference only (old id → title/slug/links)
];
$data = [];
foreach ($NEED as $dumpTable => $label) {
    $data[$label] = extractTable($raw, $dumpTable, $warn);
    echo str_pad($label, 18) . count($data[$label]) . " rows parsed\n";
    $log[] = "$label parsed: " . count($data[$label]);
}

$oldOrderNumber = [];
foreach ($data['orders'] as $o) {
    $oldOrderNumber[(int) ($o['id'] ?? 0)] = (string) ($o['order_number'] ?? '');
}
$oldProducts = [];
foreach ($data['products'] as $p) {
    $oldProducts[(int) ($p['id'] ?? 0)] = $p;
}

// ------------------------------------------------------------- connect new
$pdo = new PDO(
    "mysql:host={$host};port={$port};dbname={$dbName};charset=utf8mb4",
    $dbUser,
    $dbPass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
);

// New products lookup: slug → id, title-lower → id
$newBySlug = [];
$newByTitle = [];
$newProducts = $pdo->query('SELECT id, title, slug, is_digital, download_32bit_url, download_64bit_url, manual_url FROM products')->fetchAll();
foreach ($newProducts as $np) {
    $newBySlug[(string) ($np['slug'] ?? '')] = (int) $np['id'];
    $newByTitle[mb_strtolower(trim((string) ($np['title'] ?? '')))] = (int) $np['id'];
}
$newProductRow = [];
foreach ($newProducts as $np) {
    $newProductRow[(int) $np['id']] = $np;
}

// New orders lookup: order_number → id
$newOrderId = [];
foreach ($pdo->query('SELECT id, order_number FROM orders')->fetchAll() as $no) {
    $newOrderId[(string) $no['order_number']] = (int) $no['id'];
}
// New order items per order: order_id → [{id, product_id}]
$newItemsByOrder = [];
foreach ($pdo->query('SELECT id, order_id, product_id FROM order_items')->fetchAll() as $ni) {
    $newItemsByOrder[(int) $ni['order_id']][] = $ni;
}
// Existing codes (unique guard)
$existingCodes = [];
foreach ($pdo->query('SELECT code FROM license_codes')->fetchAll() as $ec) {
    $existingCodes[(string) $ec['code']] = true;
}

// ------------------------------------------------------------- transforms
$preparedCodes = [];
$linkBackfills = []; // product_id => [col => url]
$flagDigital = [];
$stats = ['parsed' => count($data['license_codes']), 'insert' => 0, 'skipped_dup' => 0, 'skipped_product' => 0, 'sold_linked' => 0, 'sold_orphan' => 0, 'links' => 0];

if (in_array('license_codes', $wanted, true)) {
    foreach ($data['license_codes'] as $lc) {
        $code = trim((string) ($lc['code'] ?? ''));
        if ($code === '') {
            $warn('license_codes id ' . ($lc['id'] ?? '?') . ': empty code, skipped');
            $stats['skipped_product']++;
            continue;
        }
        if (isset($existingCodes[$code])) {
            $stats['skipped_dup']++;
            continue;
        }
        $existingCodes[$code] = true;

        $oldPid = (int) ($lc['product_id'] ?? 0);
        $oldP = $oldProducts[$oldPid] ?? null;
        $newPid = null;
        if ($oldP) {
            $slug = (string) ($oldP['slug'] ?? '');
            if ($slug !== '' && isset($newBySlug[$slug])) {
                $newPid = $newBySlug[$slug];
            } else {
                $t = mb_strtolower(trim((string) ($oldP['title'] ?? '')));
                $newPid = ($t !== '' && isset($newByTitle[$t])) ? $newByTitle[$t] : null;
            }
        }
        if (!$newPid) {
            $warn("code `$code`: old product id $oldPid ('" . ($oldP['title'] ?? '?') . "') has no match in new products, skipped");
            $stats['skipped_product']++;
            continue;
        }
        $flagDigital[$newPid] = true;

        $status = strtolower((string) ($lc['status'] ?? 'available'));
        $status = $status === 'sold' ? 'sold' : 'available';
        $newOrder = null;
        $newItem = null;
        if ($status === 'sold') {
            $oldOid = (int) ($lc['order_id'] ?? 0);
            $oldNum = $oldOrderNumber[$oldOid] ?? '';
            if ($oldNum !== '' && isset($newOrderId[$oldNum])) {
                $newOrder = $newOrderId[$oldNum];
                $stats['sold_linked']++;
                foreach ($newItemsByOrder[$newOrder] ?? [] as $ni) {
                    if ((int) $ni['product_id'] === $newPid) {
                        $newItem = (int) $ni['id'];
                        break;
                    }
                }
            } else {
                $stats['sold_orphan']++;
                $warn("code `$code`: old order id $oldOid ('$oldNum') not found in new orders — kept sold, order link empty");
            }
        }

        $preparedCodes[] = [
            'id' => (int) ($lc['id'] ?? 0),
            'product_id' => $newPid,
            'code' => $code,
            'status' => $status,
            'order_id' => $newOrder,
            'order_item_id' => $newItem,
            'assigned_at' => $status === 'sold' ? ($lc['updated_at'] ?? $lc['created_at'] ?? date('Y-m-d H:i:s')) : null,
            'created_at' => $lc['created_at'] ?? date('Y-m-d H:i:s'),
            'updated_at' => $lc['updated_at'] ?? date('Y-m-d H:i:s'),
        ];
        $stats['insert']++;
    }
}

if (in_array('product_links', $wanted, true)) {
    foreach ($oldProducts as $oldPid => $oldP) {
        $slug = (string) ($oldP['slug'] ?? '');
        $newPid = null;
        if ($slug !== '' && isset($newBySlug[$slug])) {
            $newPid = $newBySlug[$slug];
        } else {
            $t = mb_strtolower(trim((string) ($oldP['title'] ?? '')));
            $newPid = ($t !== '' && isset($newByTitle[$t])) ? $newByTitle[$t] : null;
        }
        if (!$newPid) {
            continue;
        }
        $cur = $newProductRow[$newPid];
        $fill = [];
        foreach (['download_32bit_url', 'download_64bit_url', 'manual_url'] as $col) {
            $old = trim((string) ($oldP[$col] ?? ''));
            if ($old !== '' && empty($cur[$col])) {
                $fill[$col] = $old;
            }
        }
        if ($fill) {
            $linkBackfills[$newPid] = $fill;
            $stats['links']++;
        }
    }
}

echo "\nPrepared: " . count($preparedCodes) . " license_codes, " . count($linkBackfills) . " product link backfills, " . count($flagDigital) . " products to flag digital\n";
$log[] = 'prepared codes: ' . count($preparedCodes) . ', link backfills: ' . count($linkBackfills);
$log[] = 'stats: ' . json_encode($stats);

if ($dryRun) {
    echo "DRY-RUN: nothing written.\n";
    foreach (array_slice($preparedCodes, 0, 10) as $c) {
        echo '  + ' . $c['code'] . ' → product ' . $c['product_id'] . ' [' . $c['status'] . ']' . ($c['order_id'] ? ' order ' . $c['order_id'] : '') . "\n";
    }
    file_put_contents(__DIR__ . '/sync-license-codes-report.log', implode("\n", $log) . "\n");
    exit(0);
}

// ------------------------------------------------------------- confirm
if (!$yes) {
    fwrite(STDERR, "Refusing to CLEAR without --yes. Re-run with --yes.\n");
    exit(2);
}

echo "\nThis will CLEAR [license_codes] in `$dbName` and refill from dump"
    . (empty($linkBackfills) ? '' : ' (+ backfill product download links, + flag digital products)') . ".\n";

// ------------------------------------------------------------------ backup
$backupDir = __DIR__ . '/backups';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}
$stamp = date('Ymd-His');
$backupFile = $backupDir . "/license-codes-$stamp.sql";
$dumpBins = ['mysqldump'];
$laragonDump = 'C:\\laragon\\bin\\mysql\\mysql-5.7.39-winx64\\bin\\mysqldump.exe';
if (is_file($laragonDump)) {
    $dumpBins[] = $laragonDump;
}
$backedUp = false;
foreach ($dumpBins as $bin) {
    $cmd = escapeshellarg($bin)
        . ' -h ' . escapeshellarg($host) . ' -P ' . (int) $port
        . ' -u ' . escapeshellarg($dbUser)
        . ($dbPass !== '' ? ' -p' . escapeshellarg($dbPass) : '')
        . ' ' . escapeshellarg($dbName) . ' license_codes'
        . ' > ' . escapeshellarg($backupFile) . ' 2>&1';
    $code = null;
    @exec($cmd, $o, $code);
    if ($code === 0 && is_file($backupFile) && filesize($backupFile) > 0) {
        $backedUp = true;
        break;
    }
}
if (!$backedUp) {
    fwrite(STDERR, "ERROR: backup failed, aborting (no data touched).\n");
    exit(2);
}
echo "Backup: $backupFile (" . filesize($backupFile) . " bytes)\n";
$log[] = "backup: $backupFile";

// ------------------------------------------------------------------ import
try {
    $pdo->beginTransaction();
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    // NOTE: DELETE (not TRUNCATE) — TRUNCATE is DDL in MySQL and implicitly
    // commits, which would silently break the transaction below.
    $pdo->exec('DELETE FROM `license_codes`');
    echo "CLEARED license_codes\n";

    $ins = $pdo->prepare('INSERT INTO license_codes (id, product_id, code, status, order_id, order_item_id, assigned_at, created_at, updated_at) VALUES (:id, :product_id, :code, :status, :order_id, :order_item_id, :assigned_at, :created_at, :updated_at)');
    foreach ($preparedCodes as $c) {
        $ins->execute([
            ':id' => $c['id'] ?: null,
            ':product_id' => $c['product_id'],
            ':code' => $c['code'],
            ':status' => $c['status'],
            ':order_id' => $c['order_id'],
            ':order_item_id' => $c['order_item_id'],
            ':assigned_at' => $c['assigned_at'],
            ':created_at' => $c['created_at'],
            ':updated_at' => $c['updated_at'],
        ]);
    }
    echo 'INSERTED ' . count($preparedCodes) . " license_codes\n";

    foreach ($linkBackfills as $pid => $fill) {
        $sets = [];
        $params = [':id' => $pid];
        foreach ($fill as $col => $url) {
            $sets[] = "`$col` = :$col";
            $params[":$col"] = $url;
        }
        $pdo->prepare('UPDATE products SET ' . implode(', ', $sets) . ' WHERE id = :id')->execute($params);
    }
    if ($linkBackfills) {
        echo 'BACKFILLED links for ' . count($linkBackfills) . " products\n";
    }

    if ($flagDigital) {
        $ids = implode(',', array_map('intval', array_keys($flagDigital)));
        $pdo->exec("UPDATE products SET is_digital = 1 WHERE id IN ($ids)");
        echo 'FLAGGED digital: ' . count($flagDigital) . " products\n";
    }

    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    fwrite(STDERR, 'IMPORT FAILED: ' . $e->getMessage() . "\nRestore backup: " . $backupFile . "\n");
    exit(1);
}

$log[] = 'done: inserted ' . count($preparedCodes);
file_put_contents(__DIR__ . '/sync-license-codes-report.log', implode("\n", $log) . "\n");
echo "Done. Report: scripts/sync-license-codes-report.log\n";

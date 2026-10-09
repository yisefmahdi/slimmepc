<?php

/**
 * sync-purchase-records.php — Legacy → New DB sync (purchase_sales_records, 23 rows)
 *
 * Old `purchase_sales_records` → new `purchase_sales_records`.
 * Schema is IDENTICAL (product_name, supplier_name, purchase_date,
 * purchase_price, customer_name, sale_date, sale_price, notes + timestamps).
 * Direct copy preserving ids.
 *
 * Usage: same flags as sync-phase1/2 (--file --db --host --port --user --pass
 *        --dry-run --yes --config=...). Real runs backup first.
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$opt = getopt('', [
    'file:', 'host::', 'port::', 'db::', 'user::', 'pass::',
    'dry-run', 'yes', 'config::',
]);

$sqlFile = $opt['file'] ?? null;
if (! $sqlFile || ! is_file($sqlFile)) {
    fwrite(STDERR, "ERROR: --file=<dump.sql> is required and must exist.\n");
    exit(2);
}
$host = $opt['host'] ?? '127.0.0.1';
$port = (int) ($opt['port'] ?? 3306);
$dbName = $opt['db'] ?? 'slimmepc_2026';
$dbUser = $opt['user'] ?? 'root';
$dbPass = $opt['pass'] ?? '';
$dryRun = isset($opt['dry-run']);
$yes = isset($opt['yes']);
if (! empty($opt['config'])) {
    $cfg = json_decode((string) @file_get_contents((string) $opt['config']), true);
    if (! is_array($cfg)) {
        fwrite(STDERR, "ERROR: cannot read --config file.\n");
        exit(2);
    }
    foreach (['host' => 'host', 'port' => 'port', 'db' => 'dbName', 'user' => 'dbUser', 'pass' => 'dbPass'] as $k => $var) {
        if (array_key_exists($k, $cfg)) {
            $$var = $k === 'port' ? (int) $cfg[$k] : (string) $cfg[$k];
        }
    }
}

$log = [];
$log[] = '=== sync-purchase-records '.date('Y-m-d H:i:s').' | dry-run='.($dryRun ? 'yes' : 'no').' ===';
$warn = static function (string $m) use (&$log): void {
    $log[] = 'WARN: '.$m;
    echo 'WARN: '.$m.PHP_EOL;
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
            $v = str_replace(["\\'", '\\\\', "\\\r", "\\\n", "\\\t", '\\0', "''"], ["'", '\\', "\r", "\n", "\t", "\0", "'"], $v);
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
                $cur .= $c.$tuple[$i + 1];
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
    $pattern = '/INSERT INTO `'.preg_quote($table, '/').'` \(([^)]+)\) VALUES\s*/i';
    if (! preg_match_all($pattern, $raw, $m, PREG_OFFSET_CAPTURE)) {
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
            $rows[] = array_combine($cols, $v);
        }
    }

    return $rows;
}

// ------------------------------------------------------------- direct copy
$data = extractTable($raw, 'purchase_sales_records', $warn);
echo 'purchase_sales_records parsed: '.count($data)."\n";
$log[] = 'parsed: '.count($data);

$cols = ['id', 'product_name', 'supplier_name', 'purchase_date', 'purchase_price', 'customer_name', 'sale_date', 'sale_price', 'notes', 'created_at', 'updated_at'];
$rows = [];
foreach ($data as $r) {
    $row = [];
    foreach ($cols as $c) {
        $row[$c] = $r[$c] ?? null;
    }
    $row['id'] = (int) $row['id'];
    $rows[] = $row;
}
echo 'purchase_sales_records ready: '.count($rows)."\n";
$log[] = 'ready: '.count($rows);

if ($dryRun) {
    echo "\nDRY-RUN: nothing written.\n";
    file_put_contents(__DIR__.'/sync-purchase-records-report.log', implode("\n", $log)."\n");
    echo 'Report: scripts/sync-purchase-records-report.log'.PHP_EOL;
    exit(0);
}

// ------------------------------------------------------------------ confirm
if (! $yes && ! (function_exists('stream_isatty') && stream_isatty(STDIN))) {
    fwrite(STDERR, "Refusing to TRUNCATE without --yes in non-interactive mode. Re-run with --yes.\n");
    exit(3);
}
if (! $yes) {
    echo "\nThis will TRUNCATE [purchase_sales_records] in `$dbName` and refill from dump.\nType YES to continue: ";
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
    fwrite(STDERR, 'DB connection failed: '.$e->getMessage()."\n");
    exit(4);
}

// ------------------------------------------------------------------ backup
$backupDir = __DIR__.'/backups';
if (! is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}
$backupFile = $backupDir.'/purchase-records-'.date('Ymd-His').'.sql';
$dumpBins = ['mysqldump'];
$laragonDump = 'C:\\laragon\\bin\\mysql\\mysql-5.7.39-winx64\\bin\\mysqldump.exe';
if (is_file($laragonDump)) {
    $dumpBins[] = $laragonDump;
}
$backedUp = false;
foreach ($dumpBins as $bin) {
    $cmd = ($dbPass === ''
        ? sprintf('"%s" -h %s -P %d -u %s %s %s', $bin, $host, $port, $dbUser, $dbName, 'purchase_sales_records')
        : sprintf('"%s" -h %s -P %d -u %s -p%s %s %s', $bin, $host, $port, $dbUser, escapeshellarg($dbPass), $dbName, 'purchase_sales_records'))
        .' > '.escapeshellarg($backupFile).' 2>&1';
    exec($cmd, $o, $code);
    if ($code === 0 && is_file($backupFile) && filesize($backupFile) > 0) {
        $backedUp = true;
        break;
    }
}
if (! $backedUp) {
    fwrite(STDERR, "ERROR: backup failed, aborting (no data touched).\n");
    exit(5);
}
echo "Backup: $backupFile (".filesize($backupFile)." bytes)\n";
$log[] = "backup: $backupFile";

// ------------------------------------------------------------------ import
try {
    $pdo->exec('TRUNCATE TABLE `purchase_sales_records`');
    echo "TRUNCATED purchase_sales_records\n";
    $st = $pdo->prepare('INSERT INTO `purchase_sales_records` (`'.implode('`,`', $cols).'`) VALUES (?,?,?,?,?,?,?,?,?,?,?)');
    foreach ($rows as $r) {
        $st->execute([$r['id'], $r['product_name'], $r['supplier_name'], $r['purchase_date'], $r['purchase_price'], $r['customer_name'], $r['sale_date'], $r['sale_price'], $r['notes'], $r['created_at'], $r['updated_at']]);
    }
    $n = (int) $pdo->query('SELECT COUNT(*) FROM `purchase_sales_records`')->fetchColumn();
    echo "INSERTED purchase_sales_records: $n\n";
    $log[] = "inserted: $n";
    $max = $pdo->query('SELECT MAX(id) FROM `purchase_sales_records`')->fetchColumn();
    if ($max) {
        $pdo->exec('ALTER TABLE `purchase_sales_records` AUTO_INCREMENT='.((int) $max + 1));
    }
} catch (Throwable $e) {
    fwrite(STDERR, 'IMPORT FAILED: '.$e->getMessage()."\nRestore backup: ".$backupFile."\n");
    exit(6);
}

// -------------------------------------------------------------- verification
$checks = [
    'purchase_sales_records' => (int) $pdo->query('SELECT COUNT(*) FROM `purchase_sales_records`')->fetchColumn(),
    'sum_purchase' => $pdo->query('SELECT SUM(purchase_price) FROM `purchase_sales_records`')->fetchColumn(),
    'sum_sale' => $pdo->query('SELECT SUM(sale_price) FROM `purchase_sales_records`')->fetchColumn(),
];
foreach ($checks as $k => $v) {
    echo str_pad($k, 24).$v.PHP_EOL;
    $log[] = "check $k: $v";
}
file_put_contents(__DIR__.'/sync-purchase-records-report.log', implode("\n", $log)."\n");
echo 'Report: scripts/sync-purchase-records-report.log'.PHP_EOL;
echo "DONE\n";

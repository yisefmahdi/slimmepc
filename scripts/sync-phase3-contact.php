<?php

/**
 * sync-phase3-contact.php — Legacy → New DB sync (PHASE 3b: contact messages)
 *
 * Old `contact_messages` (10 rows) → new `contact_submissions` (+ `contact_replies`
 * for rows that have a reply).
 *
 * Mapping:
 *  - name/email/phone  ← customer_name/customer_email/customer_phone (as-is)
 *  - subject           ← question_type (as-is)
 *  - request_type      ← keyword scan over question_type+message:
 *      verkoop (kopen/verkopen/printer/product/webshop/prijs/kosten…),
 *      reparatie (reparatie/defect/kapot/werkt niet/start niet/schade/water…),
 *      onderhoud (onderhoud/apk/schoonmaak…), else ander. Logged per row.
 *  - message           ← as-is; status new, or replied + admin reply row when
 *      old `reply` is filled (sender=admin, source=dashboard)
 *  - attachment/ip     ← NULL (no source columns); ids preserved
 *  - dropped (logged): user_id (no column in new table), customer_address
 *
 * Usage: same flags as sync-phase1/2 (--file --db --host --port --user --pass
 *        --tables=... --dry-run --yes --config=...). Real runs backup first.
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
$log[] = '=== sync-phase3-contact '.date('Y-m-d H:i:s').' | dry-run='.($dryRun ? 'yes' : 'no').' ===';
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

// ------------------------------------------------------------- extract
$data = extractTable($raw, 'contact_messages', $warn);
echo 'contact_messages parsed: '.count($data)."\n";
$log[] = 'contact_messages parsed: '.count($data);

// ------------------------------------------------------------- transform
$kw = [
    'verkoop' => ['webshop', 'product', 'kopen', 'verkopen', 'verkoop', 'printer', 'prijs', 'kosten', 'bestellen', 'aanschaffen'],
    'reparatie' => ['reparatie', 'repareer', 'defect', 'kapot', 'werkt niet', 'doet niet', 'start niet', 'schade', 'water', 'gevallen', 'crashed', 'vastloopt', 'uitvalt', 'diagnose', 'klacht'],
    'onderhoud' => ['onderhoud', 'apk', 'schoonmaak', 'schoonmaken', 'update'],
];
$subs = [];
$replies = [];
$rid = 1;
foreach ($data as $m) {
    $id = (int) $m['id'];
    $hay = mb_strtolower((string) $m['question_type'].' '.(string) $m['message']);
    $rtype = 'ander';
    foreach ($kw as $type => $words) {
        foreach ($words as $w) {
            if (str_contains($hay, $w)) {
                $rtype = $type;
                break 2;
            }
        }
    }
    $log[] = "contact $id: question_type='".$m['question_type']."' → request_type=$rtype";
    if ($m['user_id'] !== null) {
        $warn("contact id $id: user_id {$m['user_id']} has no target column → dropped");
    }
    if (! empty($m['customer_address'])) {
        $warn("contact id $id: customer_address has no target column → dropped");
    }
    $hasReply = trim((string) ($m['reply'] ?? '')) !== '';
    $subs[] = [
        'id' => $id,
        'name' => (string) $m['customer_name'],
        'email' => (string) $m['customer_email'],
        'phone' => (string) $m['customer_phone'],
        'subject' => (string) $m['question_type'],
        'request_type' => $rtype,
        'message' => (string) $m['message'],
        'attachment' => null,
        'status' => $hasReply ? 'replied' : 'new',
        'ip_address' => null,
        'created_at' => $m['created_at'],
        'updated_at' => $m['updated_at'],
    ];
    if ($hasReply) {
        $replies[] = [
            'id' => $rid++,
            'contact_submission_id' => $id,
            'sender' => 'admin',
            'body' => (string) $m['reply'],
            'attachment' => null,
            'source' => 'dashboard',
            'created_at' => $m['updated_at'],
            'updated_at' => $m['updated_at'],
        ];
    }
}
echo 'submissions ready: '.count($subs).', replies ready: '.count($replies)."\n";
$log[] = 'ready: '.count($subs).' submissions, '.count($replies).' replies';

if ($dryRun) {
    echo "\nDRY-RUN: nothing written.\n";
    file_put_contents(__DIR__.'/sync-phase3-contact-report.log', implode("\n", $log)."\n");
    echo 'Report: scripts/sync-phase3-contact-report.log'.PHP_EOL;
    exit(0);
}

// ------------------------------------------------------------------ confirm
if (! $yes && ! (function_exists('stream_isatty') && stream_isatty(STDIN))) {
    fwrite(STDERR, "Refusing to TRUNCATE without --yes in non-interactive mode. Re-run with --yes.\n");
    exit(3);
}
if (! $yes) {
    echo "\nThis will TRUNCATE [contact_submissions,contact_replies] in `$dbName` and refill from dump.\nType YES to continue: ";
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
$backupFile = $backupDir.'/phase3-contact-'.date('Ymd-His').'.sql';
$dumpBins = ['mysqldump'];
$laragonDump = 'C:\\laragon\\bin\\mysql\\mysql-5.7.39-winx64\\bin\\mysqldump.exe';
if (is_file($laragonDump)) {
    $dumpBins[] = $laragonDump;
}
$backedUp = false;
foreach ($dumpBins as $bin) {
    $cmd = ($dbPass === ''
        ? sprintf('"%s" -h %s -P %d -u %s %s %s', $bin, $host, $port, $dbUser, $dbName, 'contact_submissions contact_replies')
        : sprintf('"%s" -h %s -P %d -u %s -p%s %s %s', $bin, $host, $port, $dbUser, escapeshellarg($dbPass), $dbName, 'contact_submissions contact_replies'))
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
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    $pdo->exec('TRUNCATE TABLE `contact_replies`');
    $pdo->exec('TRUNCATE TABLE `contact_submissions`');
    echo "TRUNCATED contact_submissions, contact_replies\n";

    $st = $pdo->prepare('INSERT INTO `contact_submissions` (`id`,`name`,`email`,`phone`,`subject`,`request_type`,`message`,`attachment`,`status`,`ip_address`,`created_at`,`updated_at`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
    foreach ($subs as $r) {
        $st->execute([$r['id'], $r['name'], $r['email'], $r['phone'], $r['subject'], $r['request_type'], $r['message'], $r['attachment'], $r['status'], $r['ip_address'], $r['created_at'], $r['updated_at']]);
    }
    if ($replies) {
        $st = $pdo->prepare('INSERT INTO `contact_replies` (`id`,`contact_submission_id`,`sender`,`body`,`attachment`,`source`,`created_at`,`updated_at`) VALUES (?,?,?,?,?,?,?,?)');
        foreach ($replies as $r) {
            $st->execute([$r['id'], $r['contact_submission_id'], $r['sender'], $r['body'], $r['attachment'], $r['source'], $r['created_at'], $r['updated_at']]);
        }
    }
    foreach (['contact_submissions', 'contact_replies'] as $t) {
        $max = $pdo->query("SELECT MAX(id) FROM `$t`")->fetchColumn();
        if ($max) {
            $pdo->exec("ALTER TABLE `$t` AUTO_INCREMENT=".((int) $max + 1));
        }
    }
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
} catch (Throwable $e) {
    try {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    } catch (Throwable) {
    }
    fwrite(STDERR, 'IMPORT FAILED: '.$e->getMessage()."\nRestore backup: ".$backupFile."\n");
    exit(6);
}

// -------------------------------------------------------------- verification
$checks = [
    'submissions' => (int) $pdo->query('SELECT COUNT(*) FROM contact_submissions')->fetchColumn(),
    'replies' => (int) $pdo->query('SELECT COUNT(*) FROM contact_replies')->fetchColumn(),
    'orphan_replies' => (int) $pdo->query('SELECT COUNT(*) FROM contact_replies r LEFT JOIN contact_submissions s ON s.id=r.contact_submission_id WHERE s.id IS NULL')->fetchColumn(),
    'bad_status' => (int) $pdo->query("SELECT COUNT(*) FROM contact_submissions WHERE status NOT IN ('new','in_progress','replied','closed')")->fetchColumn(),
];
foreach ($checks as $k => $v) {
    echo str_pad($k, 18).$v.PHP_EOL;
    $log[] = "check $k: $v";
}
file_put_contents(__DIR__.'/sync-phase3-contact-report.log', implode("\n", $log)."\n");
echo 'Report: scripts/sync-phase3-contact-report.log'.PHP_EOL;
echo "DONE\n";

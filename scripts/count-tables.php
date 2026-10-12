<?php
/**
 * count-tables.php — Row-count snapshot of every table (read-only, never writes DB).
 *
 * CLI:  php count-tables.php --env=/path/.env [--save=/path/counts.json] [--token=...]
 * HTTP: count-tables.php?token=SECRET  (token MUST match --token or TOKEN const)
 *
 * Output: {"generated_at":..., "database":..., "tables": {name: count}}
 * Saves snapshot to --save path when given (used to keep a copy on each server).
 */

declare(strict_types=1);

error_reporting(E_ALL & ~E_DEPRECATED);

const FALLBACK_TOKEN = '';

$isCli = PHP_SAPI === 'cli';
$opt = $isCli ? getopt('', ['env::', 'save::', 'token::']) : [];

$envPath = $opt['env'] ?? (__DIR__ . '/.env');
$token = $opt['token'] ?? FALLBACK_TOKEN;

if (! $isCli) {
    header('Content-Type: application/json');
    $given = (string) ($_GET['token'] ?? '');
    if ($token === '' || ! hash_equals($token, $given)) {
        http_response_code(403);
        echo json_encode(['error' => 'forbidden']);
        exit;
    }
}

$env = [];
foreach ((array) @file($envPath, FILE_IGNORE_NEW_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || $line[0] === '#') {
        continue;
    }
    $pos = strpos($line, '=');
    if ($pos !== false) {
        $env[trim(substr($line, 0, $pos))] = trim(substr($line, $pos + 1));
    }
}

try {
    $pdo = new PDO(
        'mysql:host=' . ($env['DB_HOST'] ?? '127.0.0.1') . ';port=' . ($env['DB_PORT'] ?? 3306) . ';dbname=' . ($env['DB_DATABASE'] ?? '') . ';charset=utf8mb4',
        $env['DB_USERNAME'] ?? '',
        $env['DB_PASSWORD'] ?? '',
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $tables = [];
    foreach ($pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_NUM) as $row) {
        $t = $row[0];
        $tables[$t] = (int) $pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
    }
    ksort($tables);
    $out = [
        'generated_at' => date('c'),
        'database' => $env['DB_DATABASE'] ?? '',
        'tables' => $tables,
    ];
    if (! empty($opt['save'])) {
        file_put_contents($opt['save'], json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        $out['saved_to'] = $opt['save'];
    }
    echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . ($isCli ? "\n" : '');
} catch (Throwable $e) {
    if (! $isCli) {
        http_response_code(500);
    }
    echo json_encode(['error' => $e->getMessage()]) . ($isCli ? "\n" : '');
    exit(1);
}

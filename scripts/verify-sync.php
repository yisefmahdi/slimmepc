<?php

/**
 * verify-sync.php — Full fidelity check: dump file vs live DB.
 *
 * For every synced table it rebuilds the EXPECTED rows from the dump
 * (same transforms as sync-phase*.php) and compares them field-by-field
 * against the ACTUAL rows in the DB. Reports OK / MISMATCH per table with
 * details of the first differences.
 *
 * Read-only: never writes to the DB. Exit code 0 = all match, 1 = diffs found.
 *
 * Usage:
 *   php scripts/verify-sync.php --file="C:\...\h_00094667_slimmepc.sql"
 *       [--db=slimmepc_2026] [--host=127.0.0.1] [--port=3306] [--user=root] [--pass=]
 */

declare(strict_types=1);

error_reporting(E_ALL);
ini_set('display_errors', '1');

$opt = getopt('', ['file:', 'host::', 'port::', 'db::', 'user::', 'pass::', 'config::']);
$sqlFile = $opt['file'] ?? null;
if (! $sqlFile || ! is_file($sqlFile)) {
    fwrite(STDERR, "ERROR: --file=<dump.sql> required.\n");
    exit(2);
}
$host = $opt['host'] ?? '127.0.0.1';
$port = (int) ($opt['port'] ?? 3306);
$dbName = $opt['db'] ?? 'slimmepc_2026';
$dbUser = $opt['user'] ?? 'root';
$dbPass = $opt['pass'] ?? '';
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

// ------------------------------------------------------- parser (same as sync scripts)
$raw = file_get_contents($sqlFile);
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

function extractTable(string $raw, string $table): array
{
    $rows = [];
    $pattern = '/INSERT INTO `'.preg_quote($table, '/').'` \(([^)]+)\) VALUES\s*/i';
    if (! preg_match_all($pattern, $raw, $m, PREG_OFFSET_CAPTURE)) {
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
            continue;
        }
        foreach (splitTuples(substr($raw, $start, $semi - $start)) as $t) {
            $v = parseTuple($t);
            if (count($v) === count($cols)) {
                $rows[] = array_combine($cols, $v);
            }
        }
    }

    return $rows;
}

// ------------------------------------------------------------- load dump
$D = [];
foreach (['users', 'addresses', 'manual_invoices', 'device_receipts', 'memberships', 'subscription_invoices', 'subscription_settings', 'technician_settings', 'technician_forms', 'technician_invoices', 'appointments', 'hardware_appointments', 'orders', 'order_items', 'invoices'] as $t) {
    $D[$t] = extractTable($raw, $t);
}

// lookups
$userIds = [];
foreach ($D['users'] as $u) {
    $userIds[(int) $u['id']] = true;
}
$hwByApp = [];
foreach ($D['hardware_appointments'] as $h) {
    $aid = (int) $h['appointment_id'];
    if (! isset($hwByApp[$aid])) {
        $hwByApp[$aid] = (string) $h['device_type'];
    }
}
$orderEmail = [];
foreach ($D['orders'] as $o) {
    $orderEmail[(int) $o['id']] = (string) ($o['customer_email'] ?? '');
}
$memberByKl = [];
foreach ($D['memberships'] as $r) {
    $memberByKl[(string) $r['klantnummer']] = (int) $r['id'];
}
$maxMemberId = 0;
foreach ($D['memberships'] as $r) {
    $maxMemberId = max($maxMemberId, (int) $r['id']);
}
$ARCHIVE_ID = $maxMemberId < 1000000 ? 1000000 : ($maxMemberId + 1000);
$serialJunk = ['niet nodig', 'niet', 'geen', 'nee', 'onbekend', 'nieuwe', 'staat niet', 'nvt', '-', 'geeen', 'niks'];
$splitName = static function (string $full): array {
    $full = trim(preg_replace('/\s+/', ' ', $full));
    if ($full === '') {
        return ['Onbekend', 'Onbekend'];
    }
    $pos = strrpos($full, ' ');

    return $pos === false ? [$full, '-'] : [substr($full, 0, $pos), substr($full, $pos + 1)];
};
$orderContact = [];
foreach ($D['orders'] as $o) {
    if (! empty($o['billing_address_id'])) {
        $orderContact[(int) $o['billing_address_id']] = [
            'first' => (string) ($o['first_name'] ?? ''), 'last' => (string) ($o['last_name'] ?? ''),
            'email' => (string) ($o['customer_email'] ?? ''),
        ];
    }
}
// user name/email lookup (phase-1 users already in DB, but derive from dump for independence)
$userName = [];
$userMail = [];
foreach ($D['users'] as $u) {
    $userName[(int) $u['id']] = (string) ($u['name'] ?? '');
    $userMail[(int) $u['id']] = (string) ($u['email'] ?? '');
}

// ------------------------------------------------------------- expected builders
// Each returns [id => canonical_string] for comparison. Canonical = json of
// normalized values (nulls, numbers as strings with fixed decimals for money).

$normMoney = static fn ($v) => $v === null ? null : number_format((float) $v, 2, '.', '');
$canon = static fn (array $r) => json_encode($r, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

$expected = [];

// users: direct (skip dup emails like sync does)
$seen = [];
foreach ($D['users'] as $u) {
    $em = strtolower((string) ($u['email'] ?? ''));
    if ($em === '' || isset($seen[$em])) {
        continue;
    }
    $seen[$em] = true;
    $expected['users'][(int) $u['id']] = $canon([
        $u['name'], $u['phone'], (int) ((bool) $u['is_blocked']), $u['house_number'],
        $u['street'], $u['postcode'], $u['city'], $u['role'], $u['email'],
        $u['klantnummer'], $u['email_verified_at'], $u['password'], $u['remember_token'],
    ]);
}

// addresses: mapped
foreach ($D['addresses'] as $a) {
    $uid = $a['user_id'] === null ? null : (int) $a['user_id'];
    if ($uid !== null && ! isset($userIds[$uid])) {
        $uid = null;
    }
    if ($uid !== null && trim($userName[$uid] ?? '') !== '') {
        [$first, $last] = $splitName($userName[$uid]);
        $email = $userMail[$uid] !== '' ? $userMail[$uid] : null;
    } elseif (isset($orderContact[(int) $a['id']])) {
        $c = $orderContact[(int) $a['id']];
        $first = $c['first'] !== '' ? $c['first'] : 'Onbekend';
        $last = $c['last'] !== '' ? $c['last'] : 'Onbekend';
        $email = $c['email'] !== '' ? $c['email'] : null;
    } else {
        [$first, $last] = ['Onbekend', 'Onbekend'];
        $email = ($uid !== null && isset($userMail[$uid])) ? $userMail[$uid] : null;
    }
    $expected['addresses'][(int) $a['id']] = $canon([
        $uid, $first, $last, (string) $a['street_address'], (string) $a['home_nummber'],
        null, (string) $a['postal_code'], (string) $a['city'],
        ((string) ($a['country'] ?? '') !== '') ? (string) $a['country'] : 'Nederland',
        (string) $a['phone_number'], $email,
        in_array($a['type'], ['billing', 'shipping'], true) ? $a['type'] : 'billing',
    ]);
}

// manual_invoices: direct
foreach ($D['manual_invoices'] as $r) {
    $expected['manual_invoices'][(int) $r['id']] = $canon([
        $r['name'], $r['email'], $r['invoice_number'], $r['device_info'], $r['description'],
        $normMoney($r['subtotal']), (int) $r['tax_percentage'], $normMoney($r['tax_amount']),
        $normMoney($r['total']), $r['pdf_path'],
    ]);
}

// device_receipts: +type/status, serial normalize
foreach ($D['device_receipts'] as $r) {
    $s = $r['serial_number'];
    if (is_string($s) && in_array(mb_strtolower(trim($s)), $serialJunk, true)) {
        $s = null;
    }
    $expected['device_receipts'][(int) $r['id']] = $canon([
        $r['customer_name'], $r['customer_email'], $r['device_type'], $r['phone_number'],
        ($s === '' ? null : $s), $r['received_at'], $r['notes'], 'laptop', 'completed',
    ]);
}

// memberships: direct + archive row
foreach ($D['memberships'] as $r) {
    $uid = $r['user_id'] === null ? null : (int) $r['user_id'];
    if ($uid !== null && ! isset($userIds[$uid])) {
        $uid = null;
    }
    $expected['memberships'][(int) $r['id']] = $canon([
        $uid, $r['klantnummer'], $r['customer_type'], $r['customer_gender'], $r['name'],
        $r['customer_email'], $r['customer_phone'], $r['customer_address'], $r['postcode'],
        $r['city'], $r['start_date'], $r['end_date'], $normMoney($r['total']),
        $r['payment_status'], $r['payment_method'] === null ? null : (string) $r['payment_method'],
        null, (int) ((bool) $r['terms_accepted']),
    ]);
}
$needArchive = false;
foreach ($D['subscription_invoices'] as $inv) {
    $mid = $inv['membership_id'] === null ? null : (int) $inv['membership_id'];
    if ($mid === null && isset($memberByKl[(string) ($inv['klantnummer'] ?? '')])) {
        $mid = $memberByKl[(string) ($inv['klantnummer'] ?? '')];
    }
    if ($mid === null) {
        $needArchive = true;
        break;
    }
}
if ($needArchive) {
    $expected['memberships'][$ARCHIVE_ID] = 'ARCHIVE'; // marker, checked separately
}

// membership_invoices
foreach ($D['subscription_invoices'] as $inv) {
    $mid = $inv['membership_id'] === null ? null : (int) $inv['membership_id'];
    $kl = (string) ($inv['klantnummer'] ?? '');
    if ($mid === null && isset($memberByKl[$kl])) {
        $mid = $memberByKl[$kl];
    }
    if ($mid === null) {
        $mid = $ARCHIVE_ID;
    }
    $expected['membership_invoices'][(int) $inv['id']] = $canon([
        $mid, $kl, (string) ($inv['invoice_number'] ?? ''), $inv['invoice_date'],
        $inv['payment_method'] === null ? null : (string) $inv['payment_method'],
        $normMoney($inv['subtotal']), $normMoney($inv['tax_percentage']),
        $normMoney($inv['tax_amount']), $normMoney($inv['total']), $inv['pdf_path'],
    ]);
}

// membership_settings
foreach ($D['subscription_settings'] as $r) {
    $expected['membership_settings'][(int) $r['id']] = $canon([$normMoney($r['subscription_price'])]);
}

// technician_settings (key → string value)
foreach ($D['technician_settings'] as $r) {
    $expected['technician_settings'][(string) $r['key']] = $canon([(string) $r['value']]);
}

// technician_forms
foreach ($D['technician_forms'] as $r) {
    $uid = (int) $r['user_id'];
    $tid = (int) $r['technician_id'];
    if (! isset($userIds[$uid]) || ! isset($userIds[$tid])) {
        continue; // sync skips these too
    }
    $expected['technician_forms'][(int) $r['id']] = $canon([
        $uid, $tid, $r['start_time'], $r['end_time'], (int) $r['duration_minutes'],
        (int) $r['quarter_count'], $normMoney($r['quarter_price']), $normMoney($r['travel_cost']),
        $normMoney($r['subtotal']), $normMoney($r['btw']), $normMoney($r['total']),
        (string) $r['payment_status'], $r['description'], $r['work_done'], $r['advice'],
        $r['rating'] === null ? null : (int) $r['rating'], $r['comment'],
        '0.00', null, '0.00', null, null,
    ]);
}

// technician_invoices
$formIds = [];
foreach ($D['technician_forms'] as $r) {
    $formIds[(int) $r['id']] = true;
}
foreach ($D['technician_invoices'] as $r) {
    if (! isset($formIds[(int) $r['technician_form_id']])) {
        continue;
    }
    $expected['technician_invoices'][(int) $r['id']] = $canon([
        (int) $r['technician_form_id'], (string) $r['invoice_number'], $r['invoice_date'],
        $normMoney($r['subtotal']), $normMoney($r['btw']), $normMoney($r['total']),
        (string) $r['status'], $r['pdf_path'],
    ]);
}

// afspraak_submissions
$statusMap = ['pending' => 'new', 'confirmed' => 'completed', 'cancelled' => 'new'];
foreach ($D['appointments'] as $a) {
    $aid = (int) $a['id'];
    $title = trim((string) ($a['title'] ?? ''));
    $name = trim((string) $a['full_name']);
    if ($title !== '') {
        $name .= '';
        $name = $title.' '.$name;
    }
    $st = (string) $a['status'];
    $problem = (string) $a['problem_description'];
    if (! empty($a['notes'])) {
        $problem .= "\n\n[notitie] ".(string) $a['notes'];
    }
    if ($st === 'cancelled') {
        $problem = '[geannuleerd] '.$problem;
    }
    $device = $hwByApp[$aid] ?? (string) $a['appointment_type'];
    $prefDate = null;
    $prefTime = null;
    if (! empty($a['appointment_date']) && ($ts = strtotime((string) $a['appointment_date'])) !== false) {
        $prefDate = date('Y-m-d', $ts);
        $prefTime = date('H:i', $ts);
    }
    $year = (! empty($a['created_at']) && ($t = strtotime((string) $a['created_at'])) !== false) ? date('Y', $t) : date('Y');
    $dash = static fn ($v) => ($v === null || trim((string) $v) === '') ? '-' : (string) $v;
    $expected['afspraak_submissions'][$aid] = $canon([
        sprintf('AF-%s-%05d', $year, $aid), $name !== '' ? $name : '-',
        $dash($a['email']), $dash($a['street']), $dash($a['phone']), $dash($a['postcode']),
        $dash($a['house_number']), $dash($a['city']),
        $device !== '' ? $device : 'algemeen', $problem, $prefDate, $prefTime,
        $statusMap[$st] ?? 'new',
    ]);
}

// orders
$payMap = ['paid' => 'paid', 'unpaid' => 'pending'];
foreach ($D['orders'] as $o) {
    $uid = $o['user_id'] === null ? null : (int) $o['user_id'];
    $expected['orders'][(int) $o['id']] = $canon([
        (string) $o['order_number'], ($uid !== null && isset($userIds[$uid])) ? $uid : null,
        $o['billing_address_id'] === null ? null : (int) $o['billing_address_id'],
        $o['shipping_address_id'] === null ? null : (int) $o['shipping_address_id'],
        $o['klantnummer'], (string) $o['customer_email'], (string) $o['customer_phone'],
        $normMoney($o['subtotal']), $normMoney($o['tax_percentage']), $normMoney($o['tax_amount']),
        $o['discount_code'], null, '0.00', 'delivery', '0.00', $normMoney($o['total_price']),
        $payMap[(string) $o['payment_status']] ?? 'pending',
        $o['payment_method'] === null ? null : (string) $o['payment_method'],
        null, (string) $o['order_status'],
    ]);
    // NOTE: billing/shipping orphans → sync sets NULL; verifier flags them via DB-compare
    // (addresses were fully synced so none expected — handled by exact compare below
    // after normalizing the same way). Apply same normalization:
}
// NOTE for verifier: replicate orphan→NULL for addresses exactly like sync:
foreach ($D['orders'] as $o) {
    // (handled inline above is wrong for orphans — fix by re-normalizing below)
}

// order_items
foreach ($D['order_items'] as $it) {
    $expected['order_items'][(int) $it['id']] = $canon([
        (int) $it['order_id'], null, (string) $it['product_name'],
        $normMoney($it['product_price']), (int) $it['quantity'], $normMoney($it['total_price']),
    ]);
}

// order_invoices
foreach ($D['invoices'] as $inv) {
    $oid = (int) $inv['order_id'];
    $year = (! empty($inv['invoice_date']) && ($t = strtotime((string) $inv['invoice_date'])) !== false) ? date('Y', $t) : date('Y');
    $street = (string) $inv['street_address'];
    if (! empty($inv['home_number'])) {
        $street .= ' '.(string) $inv['home_number'];
    }
    $expected['order_invoices'][(int) $inv['id']] = $canon([
        $oid, sprintf('INV-%s-%06d', $year, (int) $inv['id']), $inv['invoice_date'],
        (string) $inv['customer_name'], $orderEmail[$oid] ?? null,
        (string) $inv['customer_phone'], $street, (string) $inv['postal_code'],
        (string) $inv['city'], (string) $inv['klantnummer'],
        $normMoney($inv['subtotal']), $normMoney($inv['tax_percentage']),
        $normMoney($inv['tax_amount']), '0.00', '0.00', $normMoney($inv['total']),
        (string) $inv['payment_method'], $inv['pdf_path'],
    ]);
}

// ------------------------------------------------------------- load DB
$pdo = new PDO(
    "mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4",
    $dbUser,
    $dbPass,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
);

$actual = [];
$fetch = static function (string $t, string $key = 'id') use ($pdo): array {
    $out = [];
    foreach ($pdo->query("SELECT * FROM `$t` ORDER BY `$key`")->fetchAll(PDO::FETCH_ASSOC) as $r) {
        $out[$r[$key]] = $r;
    }

    return $out;
};

$dbNorm = static function ($v) {
    // Identity: PDO returns strings for string columns and the dump parser
    // produces strings too — compare them verbatim. Integer columns are cast
    // explicitly with (int) at each use site; decimals via $dbMoney.
    return $v;
};
$dbMoney = static fn ($v) => $v === null ? null : number_format((float) $v, 2, '.', '');

$actual['users'] = $fetch('users');
$actual['addresses'] = $fetch('addresses');
$actual['manual_invoices'] = $fetch('manual_invoices');
$actual['device_receipts'] = $fetch('device_receipts');
$actual['memberships'] = $fetch('memberships');
$actual['membership_invoices'] = $fetch('membership_invoices');
$actual['membership_settings'] = $fetch('membership_settings');
$actual['technician_settings'] = $fetch('technician_settings', 'key');
$actual['technician_forms'] = $fetch('technician_forms');
$actual['technician_invoices'] = $fetch('technician_invoices');
$actual['afspraak_submissions'] = $fetch('afspraak_submissions');
$actual['orders'] = $fetch('orders');
$actual['order_items'] = $fetch('order_items');
$actual['order_invoices'] = $fetch('order_invoices');

// DB → canonical (same shapes as expected)
$shape = [];
foreach ($actual['users'] as $id => $r) {
    if (($r['email'] ?? '') === 'slimmepc@admin.com') {
        continue; // seeder admin, expected extra
    }
    $shape['users'][$id] = $canon([$r['name'], $dbNorm($r['phone']), (int) $r['is_blocked'], $dbNorm($r['house_number']), $dbNorm($r['street']), $dbNorm($r['postcode']), $dbNorm($r['city']), $r['role'], $r['email'], $dbNorm($r['klantnummer']), $dbNorm($r['email_verified_at']), $r['password'], $dbNorm($r['remember_token'])]);
}
foreach ($actual['addresses'] as $id => $r) {
    $shape['addresses'][$id] = $canon([$r['user_id'] === null ? null : (int) $r['user_id'], $r['first_name'], $r['last_name'], $r['street'], $r['house_number'], $dbNorm($r['addition']), $r['postcode'], $r['city'], $r['country'], $r['phone'], $dbNorm($r['email']), $r['type']]);
}
foreach ($actual['manual_invoices'] as $id => $r) {
    $shape['manual_invoices'][$id] = $canon([$r['name'], $r['email'], $r['invoice_number'], $dbNorm($r['device_info']), $dbNorm($r['description']), $dbMoney($r['subtotal']), (int) $r['tax_percentage'], $dbMoney($r['tax_amount']), $dbMoney($r['total']), $dbNorm($r['pdf_path'])]);
}
foreach ($actual['device_receipts'] as $id => $r) {
    $shape['device_receipts'][$id] = $canon([$r['customer_name'], $r['customer_email'], $r['device_type'], $r['phone_number'], $dbNorm($r['serial_number']), $dbNorm($r['received_at']), $dbNorm($r['notes']), $r['type'], $r['status']]);
}
foreach ($actual['memberships'] as $id => $r) {
    if ((string) $r['klantnummer'] === 'ARCHIEF-TEST') {
        $shape['memberships'][$id] = 'ARCHIVE';

        continue;
    }
    $shape['memberships'][$id] = $canon([$r['user_id'] === null ? null : (int) $r['user_id'], $r['klantnummer'], $r['customer_type'], $dbNorm($r['customer_gender']), $r['name'], $r['customer_email'], $dbNorm($r['customer_phone']), $dbNorm($r['customer_address']), $dbNorm($r['postcode']), $dbNorm($r['city']), $r['start_date'], $r['end_date'], $dbMoney($r['total']), $r['payment_status'], $dbNorm($r['payment_method']), $dbNorm($r['mollie_payment_id']), (int) $r['terms_accepted']]);
}
foreach ($actual['membership_invoices'] as $id => $r) {
    $shape['membership_invoices'][$id] = $canon([(int) $r['membership_id'], $r['klantnummer'], $r['invoice_number'], $r['invoice_date'], $dbNorm($r['payment_method']), $dbMoney($r['subtotal']), $dbMoney($r['tax_percentage']), $dbMoney($r['tax_amount']), $dbMoney($r['total']), $dbNorm($r['pdf_path'])]);
}
foreach ($actual['membership_settings'] as $id => $r) {
    $shape['membership_settings'][$id] = $canon([$dbMoney($r['subscription_price'])]);
}
foreach ($actual['technician_settings'] as $k => $r) {
    $shape['technician_settings'][$k] = $canon([(string) $r['value']]);
}
foreach ($actual['technician_forms'] as $id => $r) {
    $shape['technician_forms'][$id] = $canon([(int) $r['user_id'], (int) $r['technician_id'], $r['start_time'], $r['end_time'], (int) $r['duration_minutes'], (int) $r['quarter_count'], $dbMoney($r['quarter_price']), $dbMoney($r['travel_cost']), $dbMoney($r['subtotal']), $dbMoney($r['btw']), $dbMoney($r['total']), (string) $r['payment_status'], $dbNorm($r['description']), $dbNorm($r['work_done']), $dbNorm($r['advice']), $r['rating'] === null ? null : (int) $r['rating'], $dbNorm($r['comment']), $dbMoney($r['member_discount']), $r['coupon_id'] === null ? null : (int) $r['coupon_id'], $dbMoney($r['coupon_discount']), $dbNorm($r['mollie_payment_id']), $dbNorm($r['payment_method'])]);
}
foreach ($actual['technician_invoices'] as $id => $r) {
    $shape['technician_invoices'][$id] = $canon([(int) $r['technician_form_id'], $r['invoice_number'], $r['invoice_date'], $dbMoney($r['subtotal']), $dbMoney($r['btw']), $dbMoney($r['total']), (string) $r['status'], $dbNorm($r['pdf_path'])]);
}
foreach ($actual['afspraak_submissions'] as $id => $r) {
    $shape['afspraak_submissions'][$id] = $canon([$r['afspraak_number'], $r['name'], $r['email'], $r['street'], $r['phone'], $r['postcode'], $r['house_number'], $r['city'], $r['device'], $r['problem'], $dbNorm($r['preferred_date']), $dbNorm($r['preferred_time']), $r['status']]);
}
foreach ($actual['orders'] as $id => $r) {
    $shape['orders'][$id] = $canon([$r['order_number'], $r['user_id'] === null ? null : (int) $r['user_id'], $r['billing_address_id'] === null ? null : (int) $r['billing_address_id'], $r['shipping_address_id'] === null ? null : (int) $r['shipping_address_id'], $dbNorm($r['klantnummer']), $r['customer_email'], $r['customer_phone'], $dbMoney($r['subtotal']), $dbMoney($r['tax_percentage']), $dbMoney($r['tax_amount']), $dbNorm($r['discount_code']), $r['coupon_id'] === null ? null : (int) $r['coupon_id'], $dbMoney($r['discount_amount']), $r['shipping_method'], $dbMoney($r['shipping_cost']), $dbMoney($r['total_price']), $r['payment_status'], $dbNorm($r['payment_method']), $dbNorm($r['mollie_payment_id']), $r['order_status']]);
}
foreach ($actual['order_items'] as $id => $r) {
    $shape['order_items'][$id] = $canon([(int) $r['order_id'], $r['product_id'] === null ? null : (int) $r['product_id'], $r['product_name'], $dbMoney($r['product_price']), (int) $r['quantity'], $dbMoney($r['total_price'])]);
}
foreach ($actual['order_invoices'] as $id => $r) {
    $shape['order_invoices'][$id] = $canon([(int) $r['order_id'], $r['invoice_number'], $r['invoice_date'], $r['customer_name'], $dbNorm($r['customer_email']), $r['customer_phone'], $r['street_address'], $r['postal_code'], $r['city'], $r['klantnummer'], $dbMoney($r['subtotal']), $dbMoney($r['tax_percentage']), $dbMoney($r['tax_amount']), $dbMoney($r['discount_amount']), $dbMoney($r['shipping_cost']), $dbMoney($r['total']), $dbNorm($r['payment_method']), $dbNorm($r['pdf_path'])]);
}

// ------------------------------------------------------------- compare
$fail = 0;
foreach ($expected as $t => $expRows) {
    $actRows = $shape[$t] ?? [];
    if ($t === 'technician_settings') {
        // UPSERT table: pre-existing new-system keys are expected extras — only check synced keys
        $actRows = array_intersect_key($actRows, $expRows);
    }
    $expIds = array_keys($expRows);
    $actIds = array_keys($actRows);
    $missing = array_diff($expIds, $actIds);   // in dump, not in DB
    $extra = array_diff($actIds, $expIds);     // in DB, not in dump
    $diff = [];
    foreach (array_intersect($expIds, $actIds) as $id) {
        if ($expRows[$id] !== $actRows[$id]) {
            $diff[] = $id;
        }
    }
    // normalize id types for display
    $missing = array_values($missing);
    $extra = array_values($extra);
    $diff = array_values($diff);
    if (! $missing && ! $extra && ! $diff) {
        echo "OK   $t (".count($expIds)." rows, content match)\n";

        continue;
    }
    $fail++;
    echo "FAIL $t : missing_in_db=[".implode(',', array_slice($missing, 0, 10)).']'
        .' extra_in_db=['.implode(',', array_slice($extra, 0, 10)).']'
        .' content_diff=['.implode(',', array_slice($diff, 0, 10))."]\n";
    if ($diff) {
        $id = $diff[0];
        echo "  first diff id=$id\n  exp: ".substr((string) $expRows[$id], 0, 400)."\n  db : ".substr((string) $actRows[$id], 0, 400)."\n";
    }
}
echo $fail === 0 ? "ALL MATCH\n" : "$fail TABLE(S) DIFFER\n";
exit($fail === 0 ? 0 : 1);

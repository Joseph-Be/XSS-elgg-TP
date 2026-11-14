<?php
/* Shared helpers for the CyberRange flag checks.
   Not web-accessible: /var/www/lab_private is outside every DocumentRoot. */

const LAB_DB_DSN    = 'mysql:host=10.9.0.6;port=3306;dbname=elgg_seed;charset=utf8mb4';
const LAB_DB_USER   = 'seed';
const LAB_DB_PASS   = 'dees';
const LAB_DB_PREFIX = 'elgg_';
const LAB_SESSION_COOKIE = 'Elgg';

function lab_flags()
{
    return require __DIR__ . '/flags.php';
}

function lab_db()
{
    return new PDO(LAB_DB_DSN, LAB_DB_USER, LAB_DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_TIMEOUT => 3,
    ]);
}

/* Extract the logged-in user's guid from a stored Elgg session blob. */
function lab_guid_from_session_data($data)
{
    if (is_string($data) && preg_match('/s:4:"guid";i:(\d+);/', $data, $m)) {
        return (int) $m[1];
    }
    return 0;
}

/* Username of the Elgg user owning the current session cookie, or null. */
function lab_session_username(PDO $pdo)
{
    $sid = isset($_COOKIE[LAB_SESSION_COOKIE]) ? (string) $_COOKIE[LAB_SESSION_COOKIE] : '';
    if ($sid === '') {
        return null;
    }
    $st = $pdo->prepare('SELECT data FROM ' . LAB_DB_PREFIX . 'users_sessions WHERE session = ?');
    $st->execute([$sid]);
    $guid = lab_guid_from_session_data($st->fetchColumn());
    if ($guid === 0) {
        return null;
    }
    $st = $pdo->prepare('SELECT value FROM ' . LAB_DB_PREFIX . "metadata WHERE entity_guid = ? AND name = 'username' LIMIT 1");
    $st->execute([$guid]);
    $name = $st->fetchColumn();
    return $name === false ? null : (string) $name;
}

function lab_user_guid(PDO $pdo, $username)
{
    $p = LAB_DB_PREFIX;
    $st = $pdo->prepare(
        "SELECT m.entity_guid FROM {$p}metadata m JOIN {$p}entities e ON e.guid = m.entity_guid
         WHERE e.type = 'user' AND m.name = 'username' AND m.value = ? LIMIT 1"
    );
    $st->execute([$username]);
    return (int) $st->fetchColumn();
}

function lab_is_friend(PDO $pdo, $ownerGuid, $friendGuid)
{
    $st = $pdo->prepare(
        'SELECT COUNT(*) FROM ' . LAB_DB_PREFIX . "entity_relationships
         WHERE guid_one = ? AND relationship = 'friend' AND guid_two = ?"
    );
    $st->execute([$ownerGuid, $friendGuid]);
    return (int) $st->fetchColumn() > 0;
}

/* True when one stored profile value of the user contains every needle. */
function lab_profile_contains(PDO $pdo, $guid, array $needles)
{
    $p = LAB_DB_PREFIX;
    $cond = implode(' AND ', array_fill(0, count($needles), 'INSTR(value, ?) > 0'));
    foreach (["{$p}metadata", "{$p}annotations"] as $table) {
        $st = $pdo->prepare("SELECT COUNT(*) FROM {$table} WHERE entity_guid = ? AND {$cond}");
        $st->execute(array_merge([$guid], $needles));
        if ((int) $st->fetchColumn() > 0) {
            return true;
        }
    }
    return false;
}

/* ---- CSP evaluation (Task 7) ------------------------------------------ */

/* Source list that governs scripts in one policy, or null if unrestricted. */
function lab_csp_script_sources($policy)
{
    $dirs = [];
    foreach (explode(';', $policy) as $part) {
        $tokens = preg_split('/\s+/', trim($part), -1, PREG_SPLIT_NO_EMPTY);
        if (!$tokens) {
            continue;
        }
        $name = strtolower(array_shift($tokens));
        if (!isset($dirs[$name])) {
            $dirs[$name] = $tokens;
        }
    }
    if (isset($dirs['script-src'])) {
        return $dirs['script-src'];
    }
    return isset($dirs['default-src']) ? $dirs['default-src'] : null;
}

function lab_csp_allows_host(array $sources, $host, $selfHost)
{
    foreach ($sources as $src) {
        $s = strtolower($src);
        if ($s === '*' || $s === 'http:') {
            return true;
        }
        if ($s === "'self'" && $host === $selfHost) {
            return true;
        }
        if ($s[0] === "'") {
            continue;
        }
        $s = preg_replace('#^https?://#', '', $s);
        $s = preg_replace('#[:/].*$#', '', $s);
        if ($s === $host) {
            return true;
        }
        if (strncmp($s, '*.', 2) === 0 && substr($host, -strlen($s) + 1) === substr($s, 1)) {
            return true;
        }
    }
    return false;
}

/* Areas 1, 2, 4, 5 and 6 of the CSP experiment must all be allowed by
   every policy the site sends, and a script policy must be in force. */
function lab_csp_passes(array $policies, $selfHost)
{
    $restricted = false;
    foreach ($policies as $policy) {
        $sources = lab_csp_script_sources($policy);
        if ($sources === null) {
            continue;
        }
        $restricted = true;
        if (!in_array("'nonce-111-111-111'", $sources, true)
            || !in_array("'nonce-222-222-222'", $sources, true)
            || !lab_csp_allows_host($sources, $selfHost, $selfHost)
            || !lab_csp_allows_host($sources, 'www.example60.com', $selfHost)
            || !lab_csp_allows_host($sources, 'www.example70.com', $selfHost)) {
            return false;
        }
    }
    return $restricted;
}

/* CSP policies actually served for a virtual host of this Apache. */
function lab_csp_fetch_policies($vhost)
{
    $ctx = stream_context_create(['http' => [
        'method' => 'GET',
        'header' => "Host: {$vhost}\r\nConnection: close\r\n",
        'timeout' => 4,
        'ignore_errors' => true,
    ]]);
    $http_response_header = [];
    @file_get_contents('http://127.0.0.1/', false, $ctx);
    $policies = [];
    foreach ($http_response_header as $line) {
        if (stripos($line, 'Content-Security-Policy:') === 0) {
            foreach (explode(',', substr($line, strlen('Content-Security-Policy:'))) as $p) {
                if (trim($p) !== '') {
                    $policies[] = trim($p);
                }
            }
        }
    }
    return $policies;
}

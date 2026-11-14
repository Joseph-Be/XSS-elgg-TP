<?php
/* CyberRange flag check for the Elgg tasks.
   The flags never leave the server unless the database shows that the
   corresponding attack actually took effect on Alice's account. */
require '/var/www/lab_private/lib.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

$out = ['user' => null, 'flags' => []];

try {
    $flags = lab_flags();
    $pdo   = lab_db();
    $user  = lab_session_username($pdo);
    $out['user'] = $user;

    if ($user === 'alice') {
        /* Tasks 1-3: the victim's browser-readable lab cookies. */
        setcookie('xss_task1', $flags['task1'], 0, '/');
        setcookie('xss_task2', $flags['task2'], 0, '/');
        setcookie('xss_task3', $flags['task3'], 0, '/');

        $alice = lab_user_guid($pdo, 'alice');
        $samy  = lab_user_guid($pdo, 'samy');

        /* Task 4: Samy is in Alice's friend list. */
        if ($alice && $samy && lab_is_friend($pdo, $alice, $samy)) {
            $out['flags']['task4'] = $flags['task4'];
        }
        /* Task 5: Alice's profile carries the marker. */
        if ($alice && lab_profile_contains($pdo, $alice, ['LAB5_PROFILE_UPDATED'])) {
            $out['flags']['task5'] = $flags['task5'];
        }
        /* Task 6: the worm (script + marker) was copied into Alice's profile. */
        if ($alice && lab_profile_contains($pdo, $alice, ['<script', 'LAB6_XSS_WORM'])) {
            $out['flags']['task6'] = $flags['task6'];
        }
    }
} catch (Throwable $e) {
    $out['error'] = 'lab check unavailable';
}

echo json_encode($out);

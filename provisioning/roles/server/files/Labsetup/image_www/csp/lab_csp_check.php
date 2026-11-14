<?php
/* CyberRange flag check for the CSP task. The server inspects the policy
   that www.example32c.com really sends; the flag is not in any page source. */
require '/var/www/lab_private/lib.php';

header('Content-Type: application/json');
header('Cache-Control: no-store');

$out = ['ok' => false];
try {
    $host = 'www.example32c.com';
    if (lab_csp_passes(lab_csp_fetch_policies($host), $host)) {
        $flags = lab_flags();
        $out = ['ok' => true, 'flag' => $flags['task7']];
    }
} catch (Throwable $e) {
    $out['error'] = 'lab check unavailable';
}
echo json_encode($out);

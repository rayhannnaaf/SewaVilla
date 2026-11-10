<?php
// Logout: hapus semua data session dan cookie session, lalu kembali ke halaman masuk.
require __DIR__ . '/includes/init.php';

$_SESSION = [];
if (ini_get('session.use_cookies')) {
    $p = session_get_cookie_params();
    setcookie(session_name(), '', [
        'expires' => time() - 3600,
        'path' => $p['path'],
        'domain' => $p['domain'],
        'secure' => $p['secure'],
        'httponly' => $p['httponly'],
        'samesite' => 'Lax',
    ]);
}
session_destroy();

redirect('login.php?keluar=1');

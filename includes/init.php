<?php
// Dipanggil paling atas di setiap halaman: memulai session dan menyediakan fungsi bantu.
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

define('USERS_FILE', __DIR__ . '/../data/users.json');
define('COOKIE_EMAIL', 'singgah_email');

// escape output agar aman dari XSS
function e($s)
{
    return htmlspecialchars((string) $s, ENT_QUOTES, 'UTF-8');
}

// huruf pertama nama (huruf besar), aman walau ekstensi mbstring belum aktif
function inisial($nama)
{
    $c = function_exists('mb_substr') ? mb_substr($nama, 0, 1) : substr($nama, 0, 1);
    return function_exists('mb_strtoupper') ? mb_strtoupper($c) : strtoupper($c);
}

function is_logged_in()
{
    return !empty($_SESSION['user']);
}

function require_login()
{
    if (!is_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

function redirect($to)
{
    header('Location: ' . $to);
    exit;
}

// pesan sekali tampil (flash) yang disimpan di session
function flash_set($msg)
{
    $_SESSION['flash'] = $msg;
}

function flash_get()
{
    $m = $_SESSION['flash'] ?? '';
    unset($_SESSION['flash']);
    return $m;
}

// token CSRF sederhana untuk form POST
function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_ok()
{
    return isset($_POST['csrf'], $_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}

// "database" pengguna berupa file JSON (cukup untuk tugas praktikum)
function load_users()
{
    if (!is_file(USERS_FILE)) {
        return [];
    }
    $data = json_decode((string) file_get_contents(USERS_FILE), true);
    return is_array($data) ? $data : [];
}

function save_users(array $users)
{
    if (!is_dir(dirname(USERS_FILE))) {
        mkdir(dirname(USERS_FILE), 0755, true);
    }
    file_put_contents(USERS_FILE, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE), LOCK_EX);
}

function find_user($email)
{
    foreach (load_users() as $u) {
        if (strcasecmp($u['email'], $email) === 0) {
            return $u;
        }
    }
    return null;
}

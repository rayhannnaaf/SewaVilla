<?php
require __DIR__ . '/includes/init.php';

// sudah login? langsung ke dashboard
if (is_logged_in()) {
    redirect('dashboard.php');
}

$err = [];
// cookie "ingat saya" mengisi email otomatis
$email = $_COOKIE[COOKIE_EMAIL] ?? '';
$ingat = isset($_COOKIE[COOKIE_EMAIL]);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $pw = $_POST['pw'] ?? '';
    $ingat = isset($_POST['ingat']);

    if (!csrf_ok()) {
        $err['pw'] = 'Sesi form kedaluwarsa. Muat ulang halaman.';
    } else {
        if ($email === '') {
            $err['email'] = 'Kolom ini wajib diisi.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $err['email'] = 'Format email belum benar.';
        }
        if ($pw === '') {
            $err['pw'] = 'Kolom ini wajib diisi.';
        }
    }

    if (!$err) {
        $user = find_user($email);
        if ($user && password_verify($pw, $user['hash'])) {
            session_regenerate_id(true); // cegah session fixation
            $_SESSION['user'] = ['nama' => $user['nama'], 'email' => $user['email']];
            $_SESSION['login_at'] = time();

            // cookie: simpan email 30 hari jika "Ingat saya" dicentang, hapus jika tidak
            $opt = ['path' => '/', 'httponly' => true, 'samesite' => 'Lax'];
            if ($ingat) {
                setcookie(COOKIE_EMAIL, $user['email'], $opt + ['expires' => time() + 60 * 60 * 24 * 30]);
            } else {
                setcookie(COOKIE_EMAIL, '', $opt + ['expires' => time() - 3600]);
            }
            redirect('dashboard.php');
        }
        $err['pw'] = 'Email atau kata sandi salah.';
    }
}

$flash = flash_get();
if (isset($_GET['keluar'])) {
    $flash = 'Kamu sudah keluar.';
}
$title = 'Masuk | Singgah';
$page = 'login';
require __DIR__ . '/includes/head.php';
?>
<div class="auth">
<aside class="side"><a href="index.php" class="logo"><b></b>Singgah</a><q>Selamat datang kembali. Liburanmu sudah menunggu.</q><small>© 2026 Singgah</small><span class="ring a"></span><span class="ring b"></span></aside>
<div class="formbox"><form data-form="login" method="post" action="login.php" novalidate>
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<h1>Masuk</h1><p class="sub">Lanjutkan merencanakan liburan kamu.</p>
<?php if ($flash): ?><p class="msg ok" role="status"><?= e($flash) ?></p><?php endif; ?>
<div class="fld<?= isset($err['email']) ? ' bad' : '' ?>"><label for="email">Email</label><div class="inp"><input id="email" name="email" type="email" placeholder="nama@email.com" autocomplete="email" value="<?= e($email) ?>"></div><p class="err"><?= e($err['email'] ?? '') ?></p></div>
<div class="fld<?= isset($err['pw']) ? ' bad' : '' ?>"><label for="pw">Kata sandi</label><div class="inp"><input id="pw" name="pw" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password"><button type="button" class="eye" aria-label="Tampilkan kata sandi"><i data-lucide="eye"></i></button></div><p class="err"><?= e($err['pw'] ?? '') ?></p></div>
<div class="row"><label class="chk"><input type="checkbox" name="ingat" <?= $ingat ? 'checked' : '' ?>>Ingat saya</label><a href="#">Lupa kata sandi?</a></div>
<button class="btn p" type="submit"><span>Masuk</span><i data-lucide="arrow-right"></i></button>
<p class="alt">Belum punya akun? <a href="register.php">Daftar</a></p>
</form></div></div>
<?php require __DIR__ . '/includes/scripts.php'; ?>

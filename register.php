<?php
require __DIR__ . '/includes/init.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$err = [];
$old = ['nama' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['nama'] = trim($_POST['nama'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $pw = $_POST['pw'] ?? '';
    $pw2 = $_POST['pw2'] ?? '';

    if (!csrf_ok()) {
        $err['nama'] = 'Sesi form kedaluwarsa. Muat ulang halaman.';
    } else {
        if ($old['nama'] === '') {
            $err['nama'] = 'Kolom ini wajib diisi.';
        }
        if ($old['email'] === '') {
            $err['email'] = 'Kolom ini wajib diisi.';
        } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
            $err['email'] = 'Format email belum benar.';
        } elseif (find_user($old['email'])) {
            $err['email'] = 'Email ini sudah terdaftar.';
        }
        if ($pw === '') {
            $err['pw'] = 'Kolom ini wajib diisi.';
        } elseif (strlen($pw) < 8) {
            $err['pw'] = 'Minimal 8 karakter.';
        }
        if ($pw2 === '') {
            $err['pw2'] = 'Kolom ini wajib diisi.';
        } elseif ($pw2 !== $pw) {
            $err['pw2'] = 'Kata sandi tidak sama.';
        }
        if (!isset($_POST['setuju'])) {
            $err['setuju'] = 'Centang dulu untuk lanjut.';
        }
    }

    if (!$err) {
        $users = load_users();
        $users[] = [
            'nama' => $old['nama'],
            'email' => $old['email'],
            'hash' => password_hash($pw, PASSWORD_DEFAULT),
            'dibuat' => date('c'),
        ];
        save_users($users);
        flash_set('Pendaftaran berhasil. Silakan masuk.');
        redirect('login.php');
    }
}

$title = 'Daftar | Singgah';
$page = 'register';
require __DIR__ . '/includes/head.php';
?>
<div class="auth">
<aside class="side"><a href="index.php" class="logo"><b></b>Singgah</a><q>Satu akun untuk semua tempat menginap.</q>
<small>© 2026 Singgah</small>
<span class="ring a"></span>
<span class="ring b"></span></aside>
<div class="formbox"><form data-form="register" method="post" action="register.php" novalidate>
<input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
<h1>Buat akun</h1><p class="sub">Gratis, tanpa biaya langganan.</p>
<div class="fld<?= isset($err['nama']) ? ' bad' : '' ?>"><label for="nama">Nama lengkap</label>
<div class="inp"><input id="nama" name="nama" type="text" placeholder="Nama kamu" autocomplete="name" value="<?= e($old['nama']) ?>"></div>
<p class="err"><?= e($err['nama'] ?? '') ?></p></div>
<div class="fld<?= isset($err['email']) ? ' bad' : '' ?>">
<label for="email">Email</label><div class="inp"><input id="email" name="email" type="email" placeholder="nama@email.com" autocomplete="email" value="<?= e($old['email']) ?>"></div>
<p class="err"><?= e($err['email'] ?? '') ?></p></div>
<div class="fld<?= isset($err['pw']) ? ' bad' : '' ?>"><label for="pw">Kata sandi</label>
<div class="inp"><input id="pw" name="pw" type="password" placeholder="Minimal 8 karakter" autocomplete="new-password"><button type="button" class="eye" aria-label="Tampilkan kata sandi"><i data-lucide="eye"></i></button></div><p class="err"><?= e($err['pw'] ?? '') ?></p></div>
<div class="fld<?= isset($err['pw2']) ? ' bad' : '' ?>"><label for="pw2">Ulangi kata sandi</label><div class="inp"><input id="pw2" name="pw2" type="password" placeholder="Ketik ulang kata sandi" autocomplete="new-password"><button type="button" class="eye" aria-label="Tampilkan kata sandi"><i data-lucide="eye"></i></button></div><p class="err"><?= e($err['pw2'] ?? '') ?></p></div>
<div class="fld<?= isset($err['setuju']) ? ' bad' : '' ?>"><label class="chk"><input type="checkbox" name="setuju" required>Aku setuju dengan syarat penggunaan</label><p class="err"><?= e($err['setuju'] ?? '') ?></p></div>
<button class="btn p" type="submit"><span>Daftar</span>
<i data-lucide="arrow-right"></i></button>
<p class="alt">Sudah punya akun? <a href="login.php">Masuk</a></p>
</form></div></div>
<?php require __DIR__ . '/includes/scripts.php'; ?>

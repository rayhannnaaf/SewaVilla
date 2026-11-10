<div class="topbar"><div class="wrap"><span><i data-lucide="tag"></i>Diskon 15% untuk menginap 3 malam atau lebih</span><span class="tb2"><i data-lucide="phone"></i>+62 812 0000 1234</span></div></div>
<header class="nav"><div class="wrap">
<a href="index.php" class="logo"><b></b>Singgah</a>
<button class="burger" aria-label="Buka menu" aria-expanded="false"><i data-lucide="menu"></i></button>
<nav class="links"><a href="#properti">Properti</a><a href="#kenapa">Keunggulan</a><a href="#mitra">Jadi mitra</a>
<?php if (is_logged_in()): ?>
<a href="dashboard.php">Halo, <?= e($_SESSION['user']['nama']) ?></a><a href="logout.php" class="btn p" data-out>Keluar</a>
<?php else: ?>
<a href="login.php">Masuk</a><a href="register.php" class="btn p">Daftar gratis</a>
<?php endif; ?>
</nav>
</div></header>

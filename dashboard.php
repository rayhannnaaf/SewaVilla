<?php
require __DIR__ . '/includes/init.php';
require_login(); // belum login -> kembali ke login.php
$nama = $_SESSION['user']['nama'];
$ini = inisial($nama);
$title = 'Dashboard | Singgah';
$page = 'dash';
require __DIR__ . '/includes/head.php';
?><div class="app">
<aside class="sb">
<a href="index.php" class="logo"><b></b>Singgah</a>
<a href="#" class="on"><i data-lucide="layout-dashboard"></i>Ringkasan</a>
<a href="#"><i data-lucide="home"></i>Properti</a>
<a href="#"><i data-lucide="list-checks"></i>Pemesanan</a>
<a href="#"><i data-lucide="calendar"></i>Kalender</a>
<a href="#"><i data-lucide="star"></i>Ulasan</a>
<a href="#"><i data-lucide="settings"></i>Pengaturan</a>
<a href="logout.php" class="out" data-out><i data-lucide="log-out"></i>Keluar</a>
</aside>
<div class="main">
<header class="top">
<button class="menu" aria-label="Buka menu" aria-expanded="false"><i data-lucide="menu"></i></button>
<div class="srch"><i data-lucide="search"></i><input id="q" type="search" placeholder="Cari tamu atau properti" aria-label="Cari pemesanan"></div>
<div class="me"><span data-user><?= e($nama) ?></span><div class="av" data-ini><?= e($ini) ?></div></div>
</header>
<div class="pg">
<h1 class="rv">Selamat datang, <span data-user><?= e($nama) ?></span></h1>
<p class="sub rv" style="--d:.05s">Ringkasan pemesanan dan hunian minggu ini.</p>
<div class="kpi">
<div class="k rv"><span>Pemesanan aktif</span><b data-count="24">24</b><em>5 baru hari ini</em></div>
<div class="k rv" style="--d:.07s"><span>Tingkat hunian</span><b data-count="78" data-suf="%">78%</b><em>Naik 6 poin</em></div>
<div class="k rv" style="--d:.14s"><span>Pendapatan bulan ini</span><b data-count="48" data-pre="Rp " data-suf=" jt">Rp 48 jt</b><em>Target 60 jt</em></div>
<div class="k rv" style="--d:.21s"><span>Properti terdaftar</span><b data-count="9">9</b><em>1 menunggu verifikasi</em></div>
</div>
<div class="grid2">
<div class="pn rv"><h3>Check-in per hari</h3>
<div class="bars"><i style="--h:45%"></i><i style="--h:62%"></i><i style="--h:38%"></i><i style="--h:88%"></i><i style="--h:70%"></i><i style="--h:28%"></i><i style="--h:20%"></i></div>
<div class="days"><span>Sen</span><span>Sel</span><span>Rab</span><span>Kam</span><span>Jum</span><span>Sab</span><span>Min</span></div></div>
<div class="pn rv" style="--d:.1s"><h3>Aktivitas terbaru</h3><ul class="act">
<li>Rina memesan Villa Kabut, 3 malam<time>5 mnt</time></li>
<li>Ulasan bintang 5 untuk Rumah Joglo<time>40 mnt</time></li>
<li>Pembayaran Rp 2,4 jt diterima<time>2 jam</time></li>
<li>Dimas membatalkan pesanan<time>Kemarin</time></li></ul></div>
</div>
<div class="pn rv"><h3>Daftar pemesanan</h3>
<div class="tw"><table><thead><tr><th>Tamu</th><th>Properti</th><th>Check-in</th><th>Malam</th><th>Status</th></tr></thead><tbody>
<tr><td>Rina Maharani</td><td>Villa Kabut</td><td>14 Okt</td><td>3</td><td><span class="tag go">Dikonfirmasi</span></td></tr>
<tr><td>Bayu Pratama</td><td>Rumah Joglo Kotagede</td><td>16 Okt</td><td>2</td><td><span class="tag go">Dikonfirmasi</span></td></tr>
<tr><td>Sari Wulandari</td><td>Villa Tepi Sawah</td><td>18 Okt</td><td>4</td><td><span class="tag">Menunggu</span></td></tr>
<tr><td>Dimas Aditya</td><td>Rumah Pantai Pacitan</td><td>20 Okt</td><td>2</td><td><span class="tag">Menunggu</span></td></tr>
<tr><td>Lina Kusuma</td><td>Villa Lembang Asri</td><td>9 Okt</td><td>3</td><td><span class="tag">Selesai</span></td></tr>
<tr><td>Agus Salim</td><td>Villa Kabut</td><td>6 Okt</td><td>2</td><td><span class="tag">Selesai</span></td></tr>
</tbody></table></div>
<p class="empty">Tidak ada pemesanan yang cocok dengan pencarian.</p></div>
</div></div></div>
<div class="scrim"></div>
<?php require __DIR__ . '/includes/scripts.php'; ?>

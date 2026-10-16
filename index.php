<?php
session_start();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>MYSmakensa — Portal Pelaporan Fasilitas Sekolah</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="landing-page">
  <!-- Navigasi utama -->
  <header class="site-nav container">
    <a class="brand" href="index.php"><span class="brand-mark"><i class="fa-solid fa-screwdriver-wrench"></i></span> Fasilitas<span>Ku</span></a>
    <a class="nav-link" href="#cara-kerja">Cara kerja</a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a class="button button-primary nav-cta" href="dashboard.php">Dashboard <i class="fa-solid fa-arrow-right"></i></a>
    <?php else: ?>
      <a class="button button-primary nav-cta" href="login.php">Masuk ke Sistem <i class="fa-solid fa-arrow-right"></i></a>
    <?php endif; ?>
  </header>

  <!-- Hero section -->
  <main>
    <section class="hero container">
      <div class="hero-copy">
        <div class="eyebrow"><span class="pulse-dot"></span> PORTAL PELAPORAN FASILITAS SEKOLAH</div>
        <h1>Fasilitas sekolah baik, <span>belajar jadi lebih nyaman.</span></h1>
        <p class="hero-description">Laporkan kerusakan fasilitas dengan mudah. Pantau perkembangan laporanmu, dari pengajuan hingga selesai, dalam satu tempat.</p>
        <div class="hero-actions">
          <?php if (isset($_SESSION['user_id'])): ?>
            <a class="button button-primary button-large" href="dashboard.php">Buka Dashboard <i class="fa-solid fa-arrow-right"></i></a>
          <?php else: ?>
            <a class="button button-primary button-large" href="login.php">Masuk ke Sistem <i class="fa-solid fa-arrow-right"></i></a>
          <?php endif; ?>
          <a class="text-link" href="#cara-kerja">Pelajari cara kerja <i class="fa-solid fa-chevron-down"></i></a>
        </div>
        <div class="trust-row"><span class="trust-icon"><i class="fa-solid fa-check"></i></span><span>Pelaporan mudah</span><span class="trust-icon"><i class="fa-solid fa-check"></i></span><span>Status transparan</span></div>
      </div>
      <!-- Ilustrasi dashboard -->
      <div class="hero-art" aria-label="Ilustrasi dashboard pelaporan">
        <div class="art-orbit orbit-one"></div><div class="art-orbit orbit-two"></div>
        <div class="floating-icon floating-tools"><i class="fa-solid fa-screwdriver-wrench"></i></div>
        <div class="floating-icon floating-check"><i class="fa-solid fa-check"></i></div>
        <div class="preview-card">
          <div class="preview-top"><div><span class="muted-label">RINGKASAN LAPORAN</span><h3>Halo, Pelapor! 👋</h3></div><span class="avatar"><i class="fa-solid fa-user"></i></span></div>
          <div class="preview-stat"><div class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></div><div><strong>Sistem Real-Time</strong><small>Terintegrasi Database</small></div><span class="stat-trend">Aktif</span></div>
          <div class="preview-progress"><div class="progress-heading"><span>Respon Cepat</span><strong>100%</strong></div><div class="progress-track"><span></span></div></div>
          <div class="preview-ticket"><span class="ticket-symbol"><i class="fa-solid fa-lightbulb"></i></span><div><strong>AC & Lampu Kelas</strong><small>Ruang kelas</small></div><span class="status status-processing">Diproses</span></div>
          <div class="preview-ticket"><span class="ticket-symbol green"><i class="fa-solid fa-faucet"></i></span><div><strong>Fasilitas Air</strong><small>Area Sekolah</small></div><span class="status status-done">Selesai</span></div>
        </div>
      </div>
    </section>

    <!-- Keunggulan portal -->
    <section class="features container" id="cara-kerja">
      <div class="section-heading"><span class="section-kicker">MUDAH DAN TRANSPARAN</span><h2>Kerusakan ditangani, prosesnya terpantau.</h2><p>Semua laporan tersimpan rapi di database agar pihak sekolah dapat menindaklanjuti secara instan.</p></div>
      <div class="feature-grid">
        <article class="feature-card"><div class="feature-icon lilac"><i class="fa-solid fa-file-circle-plus"></i></div><h3>Siswa Melapor</h3><p>Kirim laporan lengkap dengan lokasi, deskripsi, dan foto bukti kerusakan.</p></article>
        <article class="feature-card"><div class="feature-icon sky"><i class="fa-solid fa-list-check"></i></div><h3>Guru Verifikasi</h3><p>Guru memeriksa dan menyetujui (ACC / Pending / Tolak) laporan masuk.</p></article>
        <article class="feature-card"><div class="feature-icon mint"><i class="fa-solid fa-bell"></i></div><h3>Teknisi Eksekusi</h3><p>Teknisi menerima notifikasi langsung dan menyelesaikan perbaikan secara live.</p></article>
      </div>
    </section>
  </main>
  <!-- Footer -->
  <footer class="site-footer"><div class="container footer-inner"><a class="brand" href="index.php"><span class="brand-mark"><i class="fa-solid fa-screwdriver-wrench"></i></span> Fasilitas<span>Ku</span></a><p>© <?php echo date('Y'); ?> FasilitasKu · Sistem Pelaporan Kerusakan Fasilitas Sekolah</p></div></footer>
  <script src="assets/js/main.js"></script>
</body>
</html>
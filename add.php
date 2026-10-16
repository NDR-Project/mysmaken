<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$success = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id   = $_SESSION['user_id'];
    $lokasi    = trim($_POST['lokasi'] ?? '');
    $jenis     = trim($_POST['jenis'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');

    if ($lokasi === '' || $jenis === '' || $deskripsi === '') {
        $error = 'Lengkapi lokasi, jenis fasilitas, dan deskripsi laporan.';
    } else {
        $foto_name = NULL;
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array(strtolower($ext), $allowed)) {
                $foto_name = time() . '_' . rand(100, 999) . '.' . $ext;
                move_uploaded_file($_FILES['foto']['tmp_name'], 'uploads/' . $foto_name);
            }
        }

        $kode = 'LP-' . date('Y') . '-' . rand(100, 999);
        $stmt = mysqli_prepare($conn, "INSERT INTO laporan (kode, user_id, lokasi, jenis, deskripsi, foto) VALUES (?, ?, ?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sissss", $kode, $user_id, $lokasi, $jenis, $deskripsi, $foto_name);

        if (mysqli_stmt_execute($stmt)) {
            // Tambahkan notifikasi untuk Guru & Teknisi
            $msg_guru = "Laporan baru ($kode) dari {$_SESSION['nama']} di $lokasi: $jenis.";
            mysqli_query($conn, "INSERT INTO notifikasi (role, pesan, tipe) VALUES ('Guru', '$msg_guru', 'blue')");
            
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Gagal menyimpan laporan ke database.';
        }
    }
}
?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>Buat Laporan — FasilitasKu</title><link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"><link rel="stylesheet" href="assets/css/style.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"></head>
<body class="app-page">
  <aside class="sidebar"><a class="brand" href="index.php"><span class="brand-mark"><i class="fa-solid fa-screwdriver-wrench"></i></span> Fasilitas<span>Ku</span></a><div class="sidebar-label">MENU UTAMA</div><a class="side-link" href="dashboard.php"><i class="fa-solid fa-grid-2"></i> Dashboard</a><a class="side-link active" href="add.php"><i class="fa-solid fa-circle-plus"></i> Buat laporan</a><div class="sidebar-bottom"><div class="sidebar-help"><span class="help-icon"><i class="fa-regular fa-circle-question"></i></span><strong>Butuh bantuan?</strong><p>Hubungi guru piket atau admin sekolah.</p></div><a class="side-link" href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a></div></aside>
  <main class="app-main"><header class="app-topbar"><button class="mobile-menu" type="button" data-menu-toggle aria-label="Buka menu"><i class="fa-solid fa-bars"></i></button><div class="breadcrumb">Portal / <a href="dashboard.php">Dashboard</a> / <strong>Buat laporan</strong></div><div class="topbar-user"><span class="topbar-avatar"><i class="fa-solid fa-user"></i></span><div><strong><?php echo htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8'); ?></strong><small><?php echo htmlspecialchars($_SESSION['role'] ?? 'Siswa', ENT_QUOTES, 'UTF-8'); ?></small></div></div></header>
    <div class="dashboard-content form-page-content"><div class="page-title"><a class="back-link" href="dashboard.php"><i class="fa-solid fa-arrow-left"></i> Kembali ke dashboard</a><span class="section-kicker">LAPORKAN KERUSAKAN</span><h1>Buat laporan baru</h1><p>Ceritakan masalah fasilitas agar petugas dapat menindaklanjuti.</p></div>
      <section class="panel form-panel">
        <?php if ($error !== ''): ?><div class="alert alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span></div><?php endif; ?>
        <form method="post" enctype="multipart/form-data" class="form-stack" id="report-form">
          <div class="form-section-title"><span>01</span><div><h2>Detail fasilitas</h2><p>Berikan informasi yang mudah dikenali.</p></div></div>
          <div class="form-field"><label for="lokasi">Lokasi / kelas <span class="required">*</span></label><select id="lokasi" name="lokasi" required><option value="">Pilih lokasi kerusakan</option><option>Kelas X-A</option><option>Kelas X-B</option><option>Kelas XI-A</option><option>Kelas XI-B</option><option>Kelas XII-A</option><option>Perpustakaan</option><option>Laboratorium</option><option>Toilet</option><option>Lapangan</option><option>Kantin</option><option>Area lainnya</option></select></div>
          <div class="form-field"><label for="jenis">Jenis fasilitas <span class="required">*</span></label><select id="jenis" name="jenis" required><option value="">Pilih jenis fasilitas</option><option>Meja / kursi</option><option>Lampu / listrik</option><option>Pintu / jendela</option><option>Toilet / saluran air</option><option>Komputer / proyektor</option><option>Papan tulis</option><option>Bangunan / dinding</option><option>Lainnya</option></select></div>
          <div class="form-field"><label for="deskripsi">Deskripsi kerusakan <span class="required">*</span></label><textarea id="deskripsi" name="deskripsi" rows="5" placeholder="Jelaskan kondisi kerusakan, sejak kapan terjadi, dan informasi lain yang membantu..." required minlength="10"></textarea><div class="field-footer"><small>Minimal 10 karakter agar laporan jelas.</small><small><span id="char-count">0</span> karakter</small></div></div>
          <div class="form-section-title section-spaced"><span>02</span><div><h2>Foto pendukung</h2><p>Foto membantu petugas memahami kondisi fasilitas.</p></div></div>
          <div class="upload-zone" id="upload-zone"><input type="file" id="foto" name="foto" accept="image/png,image/jpeg,image/webp"><label for="foto" class="upload-label"><span class="upload-icon"><i class="fa-solid fa-cloud-arrow-up"></i></span><strong>Pilih foto atau tarik file ke sini</strong><span>JPG, PNG atau WEBP · Maks. 5 MB</span><span class="upload-filename" id="file-name">Belum ada file dipilih</span></label></div>
          <div class="form-actions"><a href="dashboard.php" class="button button-secondary">Batal</a><button type="submit" class="button button-primary"><i class="fa-regular fa-paper-plane"></i> Kirim laporan</button></div>
        </form>
      </section>
    </div>
  </main><script src="assets/js/main.js"></script>
</body></html>
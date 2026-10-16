<?php
session_start();
if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

$user_id = $_SESSION['user_id'];
$nama    = htmlspecialchars($_SESSION['nama'], ENT_QUOTES, 'UTF-8');
$role    = htmlspecialchars($_SESSION['role'], ENT_QUOTES, 'UTF-8');

// Ambil laporan dari MySQL berdasarkan Role
if ($role === 'Siswa') {
    $query = "SELECT l.*, u.nama as pelapor FROM laporan l JOIN users u ON l.user_id = u.id WHERE l.user_id = $user_id ORDER BY l.id DESC";
} else if ($role === 'Guru') {
    $query = "SELECT l.*, u.nama as pelapor FROM laporan l JOIN users u ON l.user_id = u.id ORDER BY l.id DESC";
} else { 
    $query = "SELECT l.*, u.nama as pelapor FROM laporan l JOIN users u ON l.user_id = u.id WHERE l.status IN ('Pending', 'Diproses', 'Selesai') ORDER BY l.id DESC";
}

$res_laporan = mysqli_query($conn, $query);

// Statistik
$total_laporan = 0;
$proses_laporan = 0;
$selesai_laporan = 0;

if ($role === 'Siswa') {
    $q_tot = mysqli_query($conn, "SELECT COUNT(*) as c FROM laporan WHERE user_id = $user_id");
    $q_prs = mysqli_query($conn, "SELECT COUNT(*) as c FROM laporan WHERE user_id = $user_id AND status IN ('Menunggu ACC', 'Pending', 'Diproses')");
    $q_cls = mysqli_query($conn, "SELECT COUNT(*) as c FROM laporan WHERE user_id = $user_id AND status = 'Selesai'");
} else {
    $q_tot = mysqli_query($conn, "SELECT COUNT(*) as c FROM laporan");
    $q_prs = mysqli_query($conn, "SELECT COUNT(*) as c FROM laporan WHERE status IN ('Menunggu ACC', 'Pending', 'Diproses')");
    $q_cls = mysqli_query($conn, "SELECT COUNT(*) as c FROM laporan WHERE status = 'Selesai'");
}

$total_laporan   = mysqli_fetch_assoc($q_tot)['c'] ?? 0;
$proses_laporan  = mysqli_fetch_assoc($q_prs)['c'] ?? 0;
$selesai_laporan = mysqli_fetch_assoc($q_cls)['c'] ?? 0;

// Ambil Notifikasi
$q_notif = mysqli_query($conn, "SELECT * FROM notifikasi WHERE user_id = $user_id OR role = '$role' ORDER BY id DESC LIMIT 5");

function statusClass($status) {
    $map = [
        'Menunggu ACC' => 'status-waiting',
        'Pending'      => 'status-pending',
        'Diproses'     => 'status-processing',
        'Selesai'      => 'status-done',
        'Ditolak'      => 'status-rejected'
    ];
    return $map[$status] ?? 'status-waiting';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard — FasilitasKu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="app-page">
  <aside class="sidebar">
    <a class="brand" href="index.php"><span class="brand-mark"><i class="fa-solid fa-screwdriver-wrench"></i></span> Fasilitas<span>Ku</span></a>
    <div class="sidebar-label">MENU UTAMA</div>
    <a class="side-link active" href="dashboard.php"><i class="fa-solid fa-grid-2"></i> Dashboard</a>
    <?php if ($role === 'Siswa'): ?>
      <a class="side-link" href="add.php"><i class="fa-solid fa-circle-plus"></i> Buat laporan</a>
    <?php endif; ?>
    <div class="sidebar-bottom">
      <div class="sidebar-help">
        <span class="help-icon"><i class="fa-regular fa-circle-question"></i></span>
        <strong>Akses <?= $role; ?></strong>
        <p>Sistem terhubung aktif ke MySQL.</p>
      </div>
      <a class="side-link" href="logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> Keluar</a>
    </div>
  </aside>

  <main class="app-main">
    <header class="app-topbar">
      <button class="mobile-menu" type="button" data-menu-toggle aria-label="Buka menu"><i class="fa-solid fa-bars"></i></button>
      <div class="breadcrumb">Portal / <strong>Dashboard <?= $role; ?></strong></div>
      <div class="topbar-user">
        <span class="topbar-avatar"><i class="fa-solid fa-user"></i></span>
        <div><strong><?php echo $nama; ?></strong><small><?php echo $role; ?></small></div>
      </div>
    </header>

    <div class="dashboard-content">
      <section class="welcome-row">
        <div>
          <span class="section-kicker">PORTAL REAL-TIME</span>
          <h1>Halo, <?php echo $nama; ?>! 👋</h1>
          <p>
            <?php if ($role === 'Siswa'): ?>
              Berikut perkembangan laporan fasilitas sekolahmu.
            <?php elseif ($role === 'Guru'): ?>
              Halaman verifikasi dan persetujuan (ACC) laporan fasilitas sekolah.
            <?php else: ?>
              Daftar instruksi tugas perbaikan fasilitas untuk teknisi.
            <?php endif; ?>
          </p>
        </div>
        <?php if ($role === 'Siswa'): ?>
          <a href="add.php" class="button button-primary"><i class="fa-solid fa-plus"></i> Buat Laporan Baru</a>
        <?php endif; ?>
      </section>

      <!-- Kartu statistik -->
      <section class="stats-grid">
        <article class="stat-card">
          <div class="stat-card-top"><span>Total laporan</span><span class="stat-icon blue"><i class="fa-solid fa-clipboard-list"></i></span></div>
          <strong><?= $total_laporan; ?></strong>
          <small>Semua laporan terdata</small>
        </article>
        <article class="stat-card">
          <div class="stat-card-top"><span>Dalam proses</span><span class="stat-icon orange"><i class="fa-solid fa-screwdriver-wrench"></i></span></div>
          <strong><?= $proses_laporan; ?></strong>
          <small>Menunggu / diproses</small>
        </article>
        <article class="stat-card">
          <div class="stat-card-top"><span>Selesai</span><span class="stat-icon green"><i class="fa-solid fa-circle-check"></i></span></div>
          <strong><?= $selesai_laporan; ?></strong>
          <small>Telah dituntaskan</small>
        </article>
      </section>

      <div class="dashboard-columns">
        <section class="panel report-panel">
          <div class="panel-heading">
            <div>
              <h2>Riwayat Laporan Fasilitas</h2>
              <p>Data tersimpan langsung dari database MySQL.</p>
            </div>
            <?php if ($role === 'Siswa'): ?>
              <a class="subtle-link" href="add.php">+ Buat baru</a>
            <?php endif; ?>
          </div>

          <div class="table-scroll">
            <table class="report-table">
              <thead>
                <tr>
                  <th>LAPORAN</th>
                  <th>TANGGAL</th>
                  <th>STATUS</th>
                  <?php if ($role === 'Guru' || $role === 'Teknisi'): ?>
                    <th>AKSI VERIFIKASI</th>
                  <?php endif; ?>
                </tr>
              </thead>
              <tbody>
                <?php if (mysqli_num_rows($res_laporan) > 0): ?>
                  <?php while ($row = mysqli_fetch_assoc($res_laporan)): ?>
                    <tr>
                      <td>
                        <div class="report-title"><?= htmlspecialchars($row['jenis']); ?> — <?= htmlspecialchars($row['lokasi']); ?></div>
                        <div class="report-meta">
                          <code><?= htmlspecialchars($row['kode']); ?></code> · Pelapor: <strong><?= htmlspecialchars($row['pelapor']); ?></strong>
                          <br><small><?= htmlspecialchars($row['deskripsi']); ?></small>
                          <?php if (!empty($row['foto'])): ?>
                            <br><a href="uploads/<?= htmlspecialchars($row['foto']); ?>" target="_blank" style="color: var(--primary); font-size: 10px; font-weight:700;"><i class="fa-solid fa-image"></i> Lihat Foto Bukti</a>
                          <?php endif; ?>
                          <?php if (!empty($row['catatan'])): ?>
                            <br><em style="color:#d78322; font-size:10px;">Catatan: <?= htmlspecialchars($row['catatan']); ?></em>
                          <?php endif; ?>
                        </div>
                      </td>
                      <td class="date-cell"><?= date('d M Y, H:i', strtotime($row['created_at'])); ?></td>
                      <td><span class="status <?= statusClass($row['status']); ?>"><?= htmlspecialchars($row['status']); ?></span></td>

                      <?php if ($role === 'Guru'): ?>
                        <!-- GURU HANYA BISA ACC / PENDING / TOLAK -->
                        <td>
                          <form action="process_acc.php" method="POST" style="display:flex; flex-direction:column; gap:4px;">
                            <input type="hidden" name="laporan_id" value="<?= $row['id']; ?>">
                            <select name="status" style="font-size:10px; padding:4px; border-radius:6px; border:1px solid #ccc;">
                              <option value="Menunggu ACC" <?= $row['status'] == 'Menunggu ACC' ? 'selected' : ''; ?>>Menunggu ACC</option>
                              <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : ''; ?>>ACC & Pending</option>
                              <option value="Diproses" <?= $row['status'] == 'Diproses' ? 'selected' : ''; ?>>ACC & Kerjakan (Diproses)</option>
                              <option value="Ditolak" <?= $row['status'] == 'Ditolak' ? 'selected' : ''; ?>>Tolak Laporan</option>
                            </select>
                            <input type="text" name="catatan" placeholder="Catatan guru..." style="font-size:10px; padding:4px; border-radius:6px; border:1px solid #ccc;" value="<?= htmlspecialchars($row['catatan'] ?? ''); ?>">
                            <button type="submit" class="button button-primary" style="padding:4px 8px; font-size:10px; width:100%;">Update Status</button>
                          </form>
                        </td>
                      <?php elseif ($role === 'Teknisi'): ?>
                        <!-- TEKNISI HANYA BISA UPDATE PENGERJAAN: DIPROSES / SELESAI -->
                        <td>
                          <form action="process_teknisi.php" method="POST" style="display:flex; flex-direction:column; gap:4px;">
                            <input type="hidden" name="laporan_id" value="<?= $row['id']; ?>">
                            <select name="status" style="font-size:10px; padding:4px; border-radius:6px; border:1px solid #ccc;">
                              <option value="Diproses" <?= $row['status'] == 'Diproses' ? 'selected' : ''; ?>>Sedang Diproses/Diperbaiki</option>
                              <option value="Selesai" <?= $row['status'] == 'Selesai' ? 'selected' : ''; ?>>Perbaikan Selesai</option>
                            </select>
                            <input type="text" name="catatan" placeholder="Catatan teknisi..." style="font-size:10px; padding:4px; border-radius:6px; border:1px solid #ccc;" value="<?= htmlspecialchars($row['catatan'] ?? ''); ?>">
                            <button type="submit" class="button button-primary" style="padding:4px 8px; font-size:10px; width:100%;">Update Perbaikan</button>
                          </form>
                        </td>
                      <?php endif; ?>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr>
                    <td colspan="4" style="text-align: center; padding: 20px; color: #999;">Belum ada data laporan.</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
        </section>

        <!-- Panel Notifikasi Live -->
        <aside class="panel notification-panel">
          <div class="panel-heading">
            <div>
              <h2><i class="fa-regular fa-bell"></i> Update Live</h2>
              <p>Notifikasi sistem real-time.</p>
            </div>
          </div>
          <div class="notification-list" id="notif-box">
            <?php if (mysqli_num_rows($q_notif) > 0): ?>
              <?php while ($n = mysqli_fetch_assoc($q_notif)): ?>
                <article class="notification-item">
                  <span class="notification-icon <?= htmlspecialchars($n['tipe']); ?>"><i class="fa-solid fa-bell"></i></span>
                  <div>
                    <p><?= htmlspecialchars($n['pesan']); ?></p>
                    <small><?= date('d M Y · H:i', strtotime($n['created_at'])); ?></small>
                  </div>
                </article>
              <?php endwhile; ?>
            <?php else: ?>
              <p style="font-size:11px; color:#a0a8b7; padding:15px;">Belum ada notifikasi baru.</p>
            <?php endif; ?>
          </div>
          <div class="live-note"><span class="pulse-dot"></span> Terhubung aktif ke MySQL Notifikasi</div>
        </aside>
      </div>
    </div>
  </main>
  <script src="assets/js/main.js"></script>
</body>
</html>
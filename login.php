<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

session_start();
require_once 'config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $sandi = $_POST['sandi'] ?? '';
    $token = strtoupper(trim($_POST['token'] ?? ''));

    $tokens = [
        'SISWA123' => 'Siswa',
        'GURU456' => 'Guru',
        'TEKNISI789' => 'Teknisi'
    ];

    if ($nama === '' || $sandi === '' || !isset($tokens[$token])) {
        $error = 'Nama, kata sandi, atau token akses tidak valid. Periksa kembali token Anda.';
    } else {
        $role = $tokens[$token];

        // Cari user berdasarkan nama dan role di MySQL
        $stmt = mysqli_prepare($conn, "SELECT id, nama, sandi, role FROM users WHERE nama = ? AND role = ?");
        mysqli_stmt_bind_param($stmt, "ss", $nama, $role);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($res)) {
            // User ditemukan, verifikasi kata sandi
            if (password_verify($sandi, $user['sandi'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['nama']    = $user['nama'];
                $_SESSION['role']    = $user['role'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Kata sandi salah untuk akun ' . htmlspecialchars($nama) . ' (' . $role . ').';
            }
        } else {
            // User belum ada, otomatis registrasi akun baru
            $hashed = password_hash($sandi, PASSWORD_DEFAULT);
            $stmt_ins = mysqli_prepare($conn, "INSERT INTO users (nama, sandi, role) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt_ins, "sss", $nama, $hashed, $role);
            if (mysqli_stmt_execute($stmt_ins)) {
                $new_id = mysqli_insert_id($conn);
                $_SESSION['user_id'] = $new_id;
                $_SESSION['nama']    = $nama;
                $_SESSION['role']    = $role;
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Gagal mendaftarkan akun baru ke database.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Masuk — FasilitasKu</title>
  <link rel="preconnect" href="https://fonts.googleapis.com"><link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="auth-page">
  <a class="brand auth-brand" href="index.php"><span class="brand-mark"><i class="fa-solid fa-screwdriver-wrench"></i></span> Fasilitas<span>Ku</span></a>
  <main class="auth-layout">
    <section class="auth-aside"><div class="auth-aside-content"><span class="section-kicker light-kicker">SATU LANGKAH UNTUK SEKOLAH LEBIH BAIK</span><h1>Suaramu membantu sekolah jadi lebih baik.</h1><p>Laporkan fasilitas yang rusak dan ikuti proses perbaikannya secara transparan.</p><div class="aside-illustration"><div class="aside-circle"><i class="fa-solid fa-school"></i></div><span class="aside-bubble bubble-one"><i class="fa-solid fa-check"></i></span><span class="aside-bubble bubble-two"><i class="fa-solid fa-bell"></i></span></div></div></section>
    <section class="auth-main"><div class="auth-card">
      <div class="auth-heading"><span class="section-kicker">SELAMAT DATANG</span><h2>Masuk ke sistem</h2><p>Isi data berikut untuk melanjutkan ke portal.</p></div>
      <?php if ($error !== ''): ?><div class="alert alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><span><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></span></div><?php endif; ?>
      <form method="post" action="login.php" class="form-stack">
        <div class="form-field"><label for="nama">Nama lengkap</label><div class="input-wrap"><i class="fa-regular fa-user"></i><input id="nama" name="nama" type="text" placeholder="Contoh: Andi Pratama" autocomplete="name" required value="<?php echo htmlspecialchars($_POST['nama'] ?? '', ENT_QUOTES, 'UTF-8'); ?>"></div></div>
        <div class="form-field"><label for="sandi">Kata sandi</label><div class="input-wrap"><i class="fa-solid fa-lock"></i><input id="sandi" name="sandi" type="password" placeholder="Masukkan kata sandi" autocomplete="current-password" required><button class="password-toggle" type="button" data-toggle-password="sandi" aria-label="Tampilkan kata sandi"><i class="fa-regular fa-eye"></i></button></div></div>
        <div class="form-field"><label for="token">Token akses</label><div class="input-wrap"><i class="fa-solid fa-key"></i><input id="token" name="token" type="text" placeholder="Masukkan token akses" autocomplete="off" required></div><small class="field-hint">Token menentukan peran akun yang digunakan.</small></div>
        <div class="token-help"><strong><i class="fa-solid fa-circle-info"></i> Petunjuk token demo</strong><p><span>Token Siswa:</span> <code>SISWA123</code></p><p><span>Token Guru:</span> <code>GURU456</code></p><p><span>Token Teknisi:</span> <code>TEKNISI789</code></p></div>
        <button class="button button-primary button-full button-large" type="submit">Masuk sekarang <i class="fa-solid fa-arrow-right"></i></button>
      </form>
      <p class="auth-footnote"><i class="fa-solid fa-shield-halved"></i> Gunakan akun sekolah yang telah diizinkan.</p>
    </div></section>
  </main>
  <script src="assets/js/main.js"></script>
</body></html>

<?php
session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Guru') {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laporan_id = intval($_POST['laporan_id'] ?? 0);
    $status     = $_POST['status'] ?? 'Pending';
    $catatan    = trim($_POST['catatan'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE laporan SET status = ?, catatan = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $status, $catatan, $laporan_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Ambil data laporan & pelapor
        $q = mysqli_query($conn, "SELECT l.kode, l.user_id, u.nama FROM laporan l JOIN users u ON l.user_id = u.id WHERE l.id = $laporan_id");
        if ($lap = mysqli_fetch_assoc($q)) {
            $user_id_pelapor = $lap['user_id'];
            $kode = $lap['kode'];

            // Gunakan prepared statement untuk tabel notifikasi agar aman dari error kutip (')
            $msg_siswa = "Laporan " . $kode . " kamu telah di-update Guru menjadi: " . $status . ".";
            $stmt_notif1 = mysqli_prepare($conn, "INSERT INTO notifikasi (user_id, pesan, tipe) VALUES (?, ?, 'orange')");
            mysqli_stmt_bind_param($stmt_notif1, "is", $user_id_pelapor, $msg_siswa);
            mysqli_stmt_execute($stmt_notif1);

            // Notifikasi ke Teknisi (Jika ACC Diproses/Pending)
            if ($status === 'Diproses' || $status === 'Pending') {
                $msg_teknisi = "Instruksi Perbaikan Baru (" . $kode . "): Status " . $status . ". Segera ditindaklanjuti.";
                $stmt_notif2 = mysqli_prepare($conn, "INSERT INTO notifikasi (role, pesan, tipe) VALUES ('Teknisi', ?, 'blue')");
                mysqli_stmt_bind_param($stmt_notif2, "s", $msg_teknisi);
                mysqli_stmt_execute($stmt_notif2);
            }
        }
    }
}

header('Location: dashboard.php');
exit;
?>
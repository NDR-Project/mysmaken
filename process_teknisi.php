<?php
session_start();
if (empty($_SESSION['role']) || $_SESSION['role'] !== 'Teknisi') {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $laporan_id = intval($_POST['laporan_id'] ?? 0);
    $status     = $_POST['status'] ?? 'Diproses';
    $catatan    = trim($_POST['catatan'] ?? '');

    $stmt = mysqli_prepare($conn, "UPDATE laporan SET status = ?, catatan = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "ssi", $status, $catatan, $laporan_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Ambil data laporan
        $q = mysqli_query($conn, "SELECT l.kode, l.user_id FROM laporan l WHERE l.id = $laporan_id");
        if ($lap = mysqli_fetch_assoc($q)) {
            $user_id_pelapor = $lap['user_id'];
            $kode = $lap['kode'];

            // Notifikasi ke Siswa (Aman menggunakan Prepared Statement)
            $msg_siswa = "Perbaikan laporan " . $kode . " milikmu telah di-update Teknisi menjadi: " . $status . ".";
            $stmt_n1 = mysqli_prepare($conn, "INSERT INTO notifikasi (user_id, pesan, tipe) VALUES (?, ?, 'green')");
            mysqli_stmt_bind_param($stmt_n1, "is", $user_id_pelapor, $msg_siswa);
            mysqli_stmt_execute($stmt_n1);

            // Notifikasi ke Guru
            $msg_guru = "Teknisi memperbarui laporan " . $kode . " menjadi " . $status . ".";
            $stmt_n2 = mysqli_prepare($conn, "INSERT INTO notifikasi (role, pesan, tipe) VALUES ('Guru', ?, 'green')");
            mysqli_stmt_bind_param($stmt_n2, "s", $msg_guru);
            mysqli_stmt_execute($stmt_n2);
        }
    }
}

header('Location: dashboard.php');
exit;
?>
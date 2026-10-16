CREATE DATABASE IF NOT EXISTS db_fasilitasku;
USE db_fasilitasku;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    sandi VARCHAR(255) NOT NULL,
    role ENUM('Siswa', 'Guru', 'Teknisi') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS laporan (
    id INT AUTO_INCREMENT PRIMARY KEY,
    kode VARCHAR(20) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    lokasi VARCHAR(100) NOT NULL,
    jenis VARCHAR(100) NOT NULL,
    deskripsi TEXT NOT NULL,
    foto VARCHAR(255) DEFAULT NULL,
    status ENUM('Menunggu ACC', 'Pending', 'Diproses', 'Selesai', 'Ditolak') DEFAULT 'Menunggu ACC',
    catatan TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS notifikasi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL, -- NULL means for group / role or specific user
    role ENUM('Siswa', 'Guru', 'Teknisi') DEFAULT NULL,
    pesan TEXT NOT NULL,
    tipe ENUM('blue', 'green', 'orange', 'red') DEFAULT 'blue',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

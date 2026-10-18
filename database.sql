CREATE DATABASE IF NOT EXISTS `12rpl1_aprizal_paket3`
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `12rpl1_aprizal_paket3`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `penanganan`;
DROP TABLE IF EXISTS `id_aspirasi`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `admin`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `siswa`;

SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE `siswa` (
    `id_siswa` INT AUTO_INCREMENT PRIMARY KEY,
    `nisn` VARCHAR(20) NOT NULL UNIQUE,
    `nama_siswa` VARCHAR(100) NOT NULL,
    `kelas` VARCHAR(30) NOT NULL,
    `jurusan` VARCHAR(100) NOT NULL,
    `alamat` TEXT NULL,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `kategori` (
    `id_kategori` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_kategori` VARCHAR(100) NOT NULL UNIQUE,
    `keterangan` VARCHAR(255) NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `admin` (
    `id_admin` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `users` (
    `id_user` INT AUTO_INCREMENT PRIMARY KEY,
    `nama_lengkap` VARCHAR(100) NOT NULL,
    `username` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `role` VARCHAR(50) NOT NULL DEFAULT 'Admin',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE `id_aspirasi` (
    `id_aspirasi` INT AUTO_INCREMENT PRIMARY KEY,
    `tanggal` DATETIME NOT NULL,
    `id_siswa` INT NOT NULL,
    `kategori` VARCHAR(100) NOT NULL,
    `isi_aspirasi` TEXT NOT NULL,
    `status` ENUM('Menunggu','Diproses','Selesai','Ditolak') NOT NULL DEFAULT 'Menunggu',
    CONSTRAINT `fk_aspirasi_siswa`
        FOREIGN KEY (`id_siswa`) REFERENCES `siswa`(`id_siswa`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    INDEX (`id_siswa`),
    INDEX (`status`)
) ENGINE=InnoDB;

CREATE TABLE `penanganan` (
    `id_penanganan` INT AUTO_INCREMENT PRIMARY KEY,
    `id_aspirasi` INT NOT NULL,
    `id_user` INT NULL,
    `status` ENUM('Menunggu','Diproses','Selesai','Ditolak') NOT NULL,
    `tanggapan` TEXT NOT NULL,
    `tanggal` DATETIME NOT NULL,
    CONSTRAINT `fk_penanganan_aspirasi`
        FOREIGN KEY (`id_aspirasi`) REFERENCES `id_aspirasi`(`id_aspirasi`)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT `fk_penanganan_user`
        FOREIGN KEY (`id_user`) REFERENCES `users`(`id_user`)
        ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX (`id_aspirasi`),
    INDEX (`id_user`)
) ENGINE=InnoDB;

INSERT INTO `kategori` (`nama_kategori`, `keterangan`) VALUES
('Fasilitas', 'Aspirasi tentang fasilitas sekolah'),
('Kebersihan', 'Aspirasi tentang kebersihan lingkungan'),
('Pembelajaran', 'Aspirasi tentang proses pembelajaran'),
('Keamanan', 'Aspirasi tentang keamanan sekolah'),
('Lainnya', 'Aspirasi lainnya');

-- Akun siswa contoh:
-- NISN: 0012345678
-- Password: siswa123
INSERT INTO `siswa`
(`nisn`, `nama_siswa`, `kelas`, `jurusan`, `alamat`, `password`)
VALUES
(
    '0012345678',
    'Contoh Siswa',
    'XII RPL 1',
    'Rekayasa Perangkat Lunak',
    'Cimahi',
    '$2y$12$5iv4cGS1h/us59tiMM0qXuNG6dSV7tqDrH7GN5x.OmW/2tWN/tGc.'
);

-- Akun admin:
-- Username: admin
-- Password: admin123
INSERT INTO `users`
(`nama_lengkap`, `username`, `password`, `role`)
VALUES
(
    'Administrator',
    'admin',
    '$2y$12$2nWmjFV0WSamdnvG0L.AOu2GgCfPnEeMKnznm8FpM5hlLHiM8veua',
    'Admin'
);

-- Contoh laporan agar halaman monitoring langsung memiliki data:
INSERT INTO `id_aspirasi`
(`tanggal`, `id_siswa`, `kategori`, `isi_aspirasi`, `status`)
VALUES
(
    NOW(),
    1,
    'Fasilitas',
    'Proyektor di kelas XII RPL 1 mengalami kerusakan dan tidak menampilkan gambar.',
    'Menunggu'
);

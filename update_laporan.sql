USE `12rpl1_aprizal_paket3`;

-- Jalankan ini hanya jika database lama belum memiliki kolom/tabel fitur laporan.
-- Jika database sudah dibuat dari database.sql versi terbaru, file ini tidak perlu dijalankan.

ALTER TABLE `siswa`
    ADD COLUMN `password` VARCHAR(255) NULL AFTER `alamat`;

ALTER TABLE `id_aspirasi`
    ADD COLUMN `status` ENUM('Menunggu','Diproses','Selesai','Ditolak')
    NOT NULL DEFAULT 'Menunggu' AFTER `isi_aspirasi`;

CREATE TABLE IF NOT EXISTS `penanganan` (
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
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB;

UPDATE `siswa`
SET `password` = '$2y$12$5iv4cGS1h/us59tiMM0qXuNG6dSV7tqDrH7GN5x.OmW/2tWN/tGc.'
WHERE `nisn` = '0012345678' AND (`password` IS NULL OR `password` = '');

-- Jalankan jika database lama masih menunjuk FK ke tabel tb_siswa.
USE `12rpl1_aprizal_paket3`;
ALTER TABLE `id_aspirasi` DROP FOREIGN KEY `fk_aspirasi_siswa`;
ALTER TABLE `id_aspirasi`
  ADD CONSTRAINT `fk_aspirasi_siswa`
  FOREIGN KEY (`id_siswa`) REFERENCES `siswa` (`id_siswa`)
  ON UPDATE CASCADE ON DELETE CASCADE;

CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL DEFAULT 'Peminjam'
) ENGINE=InnoDB;

INSERT IGNORE INTO users (nama_lengkap, username, password, role)
VALUES ('Administrator', 'Rizal', 'Rizal123', 'Admin');

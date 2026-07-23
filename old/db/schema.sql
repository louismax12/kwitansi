CREATE TABLE IF NOT EXISTS users (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_petugas VARCHAR(100) NOT NULL,
    role ENUM('admin','kasir') DEFAULT 'kasir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(150) NOT NULL,
    harga INT NOT NULL,
    stok INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS kwitansi (
    no_kwitansi VARCHAR(20) PRIMARY KEY,
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    nama_pasien VARCHAR(100) NOT NULL,
    total_bayar INT DEFAULT 0,
    id_user INT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS detail_kwitansi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    no_kwitansi VARCHAR(20),
    id_barang INT,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO users (username, password, nama_petugas, role) VALUES
('admin_rkz', 'd1163c4ec67553d3594f3bf0f0f4e9c66a5a7eb9', 'Suster Maria', 'admin'),
('kasir_rkz', 'c468225a6722f595499e4fc9556d3d2f67d93f6a', 'Ahmad Kasir', 'kasir')
ON DUPLICATE KEY UPDATE username=username;

INSERT INTO barang (nama_barang, harga, stok) VALUES
('Amoxicillin 500mg (Box)', 85000, 50),
('Paracetamol Syrup 60ml', 18000, 100),
('Infus Sanbe NaCl 0.9%', 22000, 30),
('Kasa Steril Onemed (Isi 10)', 12000, 15),
('Masker Medis 3-Ply (Box)', 35000, 0)
ON DUPLICATE KEY UPDATE nama_barang=nama_barang;

# Database Schema & Relations

## 1. Skema Tabel (DDL)
Agent WAJIB menggunakan struktur tabel berikut. Perhatikan bahwa kita menggunakan MyISAM dan tidak menggunakan Foreign Key fisik sama sekali:

```sql
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_petugas VARCHAR(100) NOT NULL,
    role ENUM('admin', 'kasir') DEFAULT 'kasir'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(150) NOT NULL,
    harga INT NOT NULL,
    stok INT DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE kwitansi (
    no_kwitansi VARCHAR(20) PRIMARY KEY,
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    nama_pasien VARCHAR(100) NOT NULL,
    total_bayar INT DEFAULT 0,
    id_user INT,
    INDEX idx_user (id_user) -- Pengganti Foreign Key
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE detail_kwitansi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    no_kwitansi VARCHAR(20),
    id_barang INT,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL,
    INDEX idx_kwitansi (no_kwitansi), -- Pengganti Foreign Key
    INDEX idx_barang (id_barang)      -- Pengganti Foreign Key
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;
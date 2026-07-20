-- schema.sql
-- Database Schema OSPOS-Lite for RKZ Hospital Cashier System
-- PHP 5.4 & MyISAM Guidelines Compliant

CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_petugas VARCHAR(100) NOT NULL,
    role ENUM('admin', 'kasir') DEFAULT 'kasir'
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE pelanggan (
    id_pelanggan INT AUTO_INCREMENT PRIMARY KEY,
    nama_pelanggan VARCHAR(150) NOT NULL,
    no_hp VARCHAR(20) DEFAULT NULL,
    alamat TEXT
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    barcode VARCHAR(50) UNIQUE DEFAULT NULL,
    nama_barang VARCHAR(150) NOT NULL,
    kategori VARCHAR(50) DEFAULT 'Umum',
    harga INT NOT NULL,
    stok INT DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE inventori (
    id_inventori INT AUTO_INCREMENT PRIMARY KEY,
    id_barang INT NOT NULL,
    id_user INT NOT NULL,
    tanggal DATETIME DEFAULT CURRENT_TIMESTAMP,
    jumlah_perubahan INT NOT NULL,
    keterangan VARCHAR(255) DEFAULT '',
    INDEX idx_barang (id_barang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE kwitansi (
    no_kwitansi VARCHAR(20) PRIMARY KEY,
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    id_pelanggan INT DEFAULT NULL,
    nama_pasien VARCHAR(100) NOT NULL,
    total_bayar INT DEFAULT 0,
    id_user INT,
    INDEX idx_user (id_user),
    INDEX idx_pelanggan (id_pelanggan)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

CREATE TABLE detail_kwitansi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    no_kwitansi VARCHAR(20),
    id_barang INT,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL,
    INDEX idx_kwitansi (no_kwitansi),
    INDEX idx_barang (id_barang)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4;

-- Seeders
INSERT INTO user (username, password, nama_petugas, role) VALUES 
('admin_rkz', '5f4dcc3b5aa765d61d8327deb882cf99', 'Suster Maria', 'admin'), -- MD5 of password
('kasir_rkz', '5f4dcc3b5aa765d61d8327deb882cf99', 'Ahmad Kasir', 'kasir');

INSERT INTO pelanggan (nama_pelanggan, no_hp) VALUES
('Tn. Budi Santoso', '08123456789'),
('Ny. Siti Aminah', '08987654321');

INSERT INTO barang (barcode, nama_barang, kategori, harga, stok) VALUES 
('899123456001', 'Amoxicillin 500mg (Box)', 'Obat', 85000, 50),
('899123456002', 'Paracetamol Syrup 60ml', 'Obat', 18000, 100),
('899123456003', 'Infus Sanbe NaCl 0.9%', 'Alkes', 22000, 30),
('899123456004', 'Kasa Steril Onemed (Isi 10)', 'Alkes', 12000, 15),
('899123456005', 'Masker Medis 3-Ply (Box)', 'Alkes', 35000, 0);

INSERT INTO inventori (id_barang, id_user, jumlah_perubahan, keterangan)
SELECT id_barang, 1, stok, 'Stok Awal Sistem' FROM barang WHERE stok > 0;

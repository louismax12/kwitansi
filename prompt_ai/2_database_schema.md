# Database Schema & Relations

## 1. Skema Tabel (DDL)
Agent wajib menggunakan struktur tabel berikut tanpa mengubah nama kolom:

```sql
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    nama_petugas VARCHAR(100) NOT NULL,
    role ENUM('admin', 'kasir') DEFAULT 'kasir'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE barang (
    id_barang INT AUTO_INCREMENT PRIMARY KEY,
    nama_barang VARCHAR(150) NOT NULL,
    harga INT NOT NULL,
    stok INT DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE kwitansi (
    no_kwitansi VARCHAR(20) PRIMARY KEY,
    tanggal_transaksi DATETIME DEFAULT CURRENT_TIMESTAMP,
    nama_pasien VARCHAR(100) NOT NULL,
    total_bayar INT DEFAULT 0,
    id_user INT,
    FOREIGN KEY (id_user) REFERENCES user(id_user)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE detail_kwitansi (
    id_detail INT AUTO_INCREMENT PRIMARY KEY,
    no_kwitansi VARCHAR(20),
    id_barang INT,
    jumlah INT NOT NULL,
    subtotal INT NOT NULL,
    FOREIGN KEY (no_kwitansi) REFERENCES kwitansi(no_kwitansi) ON DELETE CASCADE,
    FOREIGN KEY (id_barang) REFERENCES barang(id_barang)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## 2. Aturan Relasi Bisnis
- Satu kwitansi (`kwitansi`) diinput oleh satu user (`id_user`).
- Satu kwitansi memiliki banyak item di `detail_kwitansi` melalui relasi `no_kwitansi`.
- Penghapusan data kwitansi (`ON DELETE CASCADE`) otomatis menghapus baris detailnya.

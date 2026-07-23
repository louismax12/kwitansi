# Testing Specification & Initial Dummy Data

## 1. SQL Data Awal untuk Pengujian (Seeders)
Agent wajib menyertakan atau mengasumsikan data awal berikut sudah ada di database untuk simulasi:

```sql
-- Insert Dummy Users (Password di bawah adalah simulasi teks biasa / salt)
INSERT INTO user (username, password, nama_petugas, role) VALUES 
('admin_rkz', 'password123', 'Suster Maria', 'admin'),
('kasir_rkz', 'kasir123', 'Ahmad Kasir', 'kasir');

-- Insert Dummy Barang (Obat & Alkes)
INSERT INTO barang (nama_barang, harga, stok) VALUES 
('Amoxicillin 500mg (Box)', 85000, 50),
('Paracetamol Syrup 60ml', 18000, 100),
('Infus Sanbe NaCl 0.9%', 22000, 30),
('Kasa Steril Onemed (Isi 10)', 12000, 15),
('Masker Medis 3-Ply (Box)', 35000, 0); -- Stok kosong untuk uji coba validasi
```

## 2. Skenario Pengujian yang Harus Lolos (Test Cases)
- **Test Case 1 (Login Kasir)**: Input username `kasir_rkz` harus berhasil masuk dan mengarahkan ke halaman transaksi kwitansi.
- **Test Case 2 (Input Multi-Barang)**: Memilih `Amoxicillin` (2) dan `Infus` (1) harus mengalkulasi subtotal secara realtime di layar: (2 * 85.000) + (1 * 22.000) = 192.000.
- **Test Case 3 (Validasi Stok Kosong)**: Jika kasir mencoba menginput `Masker Medis` yang stoknya 0, sistem di frontend atau backend harus memblokir transaksi dan memunculkan alert: "Stok Masker Medis habis!".
- **Test Case 4 (Efek Setelah Simpan)**: Setelah transaksi sukses disimpan, periksa tabel `barang`. Stok `Amoxicillin` harus berkurang dari 50 menjadi 48.

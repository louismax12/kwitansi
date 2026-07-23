# RKZ Invoice System

Sistem invoice atau kwitansi berbasis web untuk RKZ yang dirancang untuk membantu proses transaksi menjadi lebih cepat, rapi, dan terdokumentasi dengan baik.

## ✨ Gambaran Umum
Project ini berfungsi seperti kasir digital yang modern. Saat transaksi berlangsung, sistem membantu pengguna untuk:
- login ke sistem,
- memilih pelanggan,
- memilih barang yang dibeli,
- menghitung total pembayaran secara otomatis,
- dan menyimpan transaksi sebagai kwitansi.

> Bayangkan seperti “mesin kasir pintar” yang bekerja di balik layar untuk memastikan setiap pembayaran tercatat dengan rapi.

## 🎯 Fitur Utama
- Login pengguna dengan peran admin dan kasir
- Pengelolaan data barang
- Pengelolaan data pelanggan
- Pembuatan transaksi kwitansi/invoice
- Perhitungan total pembayaran otomatis
- Penyimpanan riwayat transaksi
- Pendukung pencatatan stok dan inventori

## 🔄 Alur Kerja Sistem
1. Pengguna masuk ke aplikasi melalui halaman login.
2. Kasir atau admin memilih pelanggan yang melakukan transaksi.
3. Barang dipilih dan jumlah barang diinput.
4. Sistem menghitung subtotal dan total pembayaran.
5. Transaksi disimpan sebagai kwitansi.
6. Data transaksi dapat dilihat kembali untuk kebutuhan laporan atau pengecekan.

## 📊 Struktur Database
Berikut tabel utama yang digunakan dalam sistem ini:

| Tabel | Fungsi |
|---|---|
| user | Menyimpan data akun admin dan kasir |
| pelanggan | Menyimpan data pelanggan |
| barang | Menyimpan data barang, harga, dan stok |
| inventori | Mencatat perubahan stok barang |
| kwitansi | Menyimpan transaksi utama berupa kwitansi |
| detail_kwitansi | Menyimpan daftar barang per transaksi |

## 🖼️ Animasi / Visual Pendukung
Untuk membuat README lebih menarik, folder gambar telah disediakan pada folder [img](img). Folder ini bisa dipakai untuk menaruh:
- screenshot tampilan aplikasi,
- mockup UI,
- atau ilustrasi visual pendukung.

## 💡 Kenapa Sistem Ini Penting
Sistem ini membantu mengurangi kesalahan hitung manual, membuat pencatatan transaksi lebih tertib, dan memberikan bukti pembayaran yang lebih rapi.

## 🧱 Struktur Project
Project ini terdiri dari beberapa bagian utama:
- Database untuk menyimpan data transaksi dan master data
- Model untuk logika pemrosesan data
- Controller untuk mengatur alur aplikasi
- View untuk tampilan halaman web
- API atau endpoint pendukung untuk proses data

## 🛠️ Teknologi yang Digunakan
- PHP
- MySQL / MariaDB
- HTML, CSS, dan JavaScript
- Struktur web sederhana untuk sistem kasir dan invoice

## ✅ Kesimpulan
Repo ini adalah sistem kasir digital dan invoice untuk RKZ yang fokus pada pencatatan penjualan, pengelolaan barang, dan pembuatan kwitansi secara terstruktur.

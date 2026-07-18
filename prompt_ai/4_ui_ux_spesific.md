# UI/UX & Print Layout Specification

## 1. Kebutuhan Form Transaksi Kasir
- Elemen Atas: Input text untuk `Nama Pasien`, Input date untuk `Tanggal Transaksi` (default hari ini, disabled/readonly).
- Elemen Tengah (Dinamis): Baris tabel input item yang bisa ditambah secara dinamis menggunakan JavaScript (Tombol "Tambah Item"). Setiap baris berisi:
  * Dropdown/Pencarian Data Barang (menampilkan nama dan harga).
  * Input angka `Jumlah`.
  * Kolom `Subtotal` (Otomatis terisi via JS: harga * jumlah, readonly).
  * Tombol "Hapus Item" di ujung baris.
- Elemen Bawah: Total Bayar (Kalkulasi otomatis dari seluruh subtotal secara realtime menggunakan JS). Tombol "Simpan & Cetak".

## 2. Layout Cetak Kwitansi Resmi (Print-Friendly HTML)
Bila halaman cetak dibuka, otomatis memicu fungsi JavaScript `window.print()`. Komponen yang harus ada pada layout CSS Print:
- Header: Logo/Nama "RKZ SURABAYA HOSPITAL", Alamat, dan Nomor Telepon Rumah Sakit.
- Metadata: Nomor Kwitansi, Tanggal, dan Nama Pasien (Format layout 2 kolom kiri-kanan).
- Tabel Transaksi: Daftar barang, jumlah, harga satuan, dan subtotal. Diakhiri baris Grand Total.
- Footer: Tempat tanda tangan digital/manual bertuliskan "Hormat Kami, [Nama Petugas Kasir]".
- Aturan CSS: Sembunyikan tombol navigasi, navbar, atau tombol cetak saat mode print aktif (`@media print { .no-print { display: none; } }`).

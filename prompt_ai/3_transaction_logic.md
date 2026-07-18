# Transaction & Business Logic Requirements (MyISAM & PHP 5.4 Compatible)

## 1. Mekanisme Generate Nomor Kwitansi
Nomor kwitansi harus digenerate otomatis dengan format: `KW-YYYYMMDD-[4 Digit Urut]`.
Contoh: `KW-20260718-0001`. Agent harus membuat query untuk mengecek counter terakhir pada hari berjalan menggunakan klausa `LIKE`.

## 2. Alur Simpan Transaksi (Manual Rollback Logic untuk MyISAM)
Karena engine MyISAM tidak mendukung `$pdo->beginTransaction()`, proses pengamanan data jika terjadi gagal simpan/stok habis wajib ditangani manual secara prosedural di PHP:

1. **Langkah 1 (Validasi Awal)**: Sebelum melakukan INSERT apa pun, lakukan looping pada item yang dikirim dari frontend. Cek satu per satu ke tabel `barang` apakah `stok >= jumlah_beli`. Jika ada satu saja barang yang stoknya kurang, hentikan proses dan langsung kembalikan respon error ke kasir.
2. **Langkah 2**: Jika semua stok aman, lakukan `INSERT` data utama ke tabel `kwitansi`.
3. **Langkah 3**: Lakukan looping kedua untuk melakukan `INSERT` ke tabel `detail_kwitansi` sekaligus lakukan `UPDATE` ke tabel `barang` untuk memotong stok (`stok = stok - jumlah_beli`).
4. **Catatan Kegagalan**: Karena tidak ada rollback otomatis di MyISAM, pastikan validasi stok di Langkah 1 benar-benar ketat untuk menghindari data menggantung (*corrupted data*).

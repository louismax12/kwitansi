# System Overview: Sistem Kwitansi RKZ Surabaya Hospital

## 1. Konteks Bisnis
Sistem ini digunakan oleh kasir di RKZ Surabaya Hospital untuk mencatat transaksi pembayaran obat, alkes, atau layanan medis pasien, serta mencetak kwitansi resmi.

## 2. Tech Stack Legacy Mandatori (Wajib Dipatuhi)
- Backend: PHP 5.4 (Wajib menggunakan gaya penulisan array lama `array()` bukan `[]`, tidak boleh menggunakan fitur PHP 7/8).
- Database: MySQL Server dengan Storage Engine **MyISAM**.
- Driver Database: Wajib menggunakan **PDO** atau **MySQLi** (Jangan gunakan fungsi `mysql_connect` karena deprecated di PHP 5.4).
- Frontend: Bootstrap 5 (Clean UI, ramah pengguna rumah sakit).
- JavaScript: Vanilla JS / AJAX lama kompatibel dengan browser rumah sakit.

## 3. Aturan Pemrograman (Guidelines)
- Keamanan: Semua query input wajib menggunakan Prepared Statements untuk mencegah SQL Injection.
- Clean Code: Gunakan penamaan variabel bahasa Inggris atau Indonesia yang konsisten (snake_case untuk database, camelCase untuk kode).
- Engine Constraint: Ingat bahwa MyISAM tidak mendukung Foreign Key fisik dan Database Transaction. Validasi integritas data sepenuhnya menjadi tanggung jawab kode PHP.

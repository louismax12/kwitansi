# PHP 5.4 & MyISAM Compatibility Guidelines

## 1. Aturan Sintaks PHP 5.4 (Wajib Dipatuhi)
Agent AI dilarang keras menggunakan fitur modern dari PHP 7.x dan PHP 8.x. Ikuti aturan penulisan berikut:
- **Array**: Wajib menggunakan fungsi `array()` lama. Dilarang menggunakan short array syntax `[]`.
  * *Benar*: `$data = array("status" => "success");`
  * *Salah*: `$data = ["status" => "success"];`
- **Null Coalescing**: Dilarang menggunakan operator `??`. Gunakan `isset()` dan operator ternary klasik.
  * *Benar*: `$page = isset($_GET['p']) ? $_GET['p'] : 'home';`
  * *Salah*: `$page = $_GET['p'] ?? 'home';`
- **Password Hashing**: PHP 5.4 belum memiliki fungsi `password_hash()`. Untuk keamanan tabel `user`, gunakan ekstensi `crypt()` atau fungsi `md5()` / `sha1()` dengan kombinasi *Salt* string manual jika server tidak memiliki pustaka tambahan.
- **Type Hinting**: Dilarang menuliskan tipe data pada argumen fungsi atau properti class (e.g., `public string $name` atau `function simpan(int $id)`).

## 2. Penyesuaian Perilaku MyISAM
- **No Foreign Keys**: Jangan menuliskan sintaks `FOREIGN KEY` atau `REFERENCES` pada script SQL karena MyISAM mengabaikannya dan itu hanya akan membuang memori.
- **Table Level Locking**: Sadari bahwa MyISAM mengunci seluruh tabel (*Table-level lock*) saat terjadi operasi `INSERT` atau `UPDATE`. Pastikan query efisien dan langsung ditutup agar tidak membuat antrean (*bottleneck*) di kasir rumah sakit.

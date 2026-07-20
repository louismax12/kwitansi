#### Revisi File 3: Transaction & Business Logic Requirements
```markdown
# Transaction & Business Logic Requirements (MyISAM & PHP 5.4 Compatible)

## 1. Mekanisme Generate Nomor Kwitansi
Nomor kwitansi harus digenerate otomatis dengan format: `KW-YYYYMMDD-[4 Digit Urut]` (Contoh: `KW-20260718-0001`). 
Agent harus membuat query untuk mencari nilai `MAX(RIGHT(no_kwitansi, 4))` pada hari berjalan menggunakan klausa `LIKE`.

## 2. Alur Simpan Transaksi & Kunci Tabel (Pessimistic Locking)
Karena engine MyISAM tidak mendukung `$pdo->beginTransaction()`, Anda WAJIB menerapkan alur berikut secara kaku di PHP:

1. **Table Locking (Wajib):** Eksekusi perintah SQL `LOCK TABLES barang WRITE, kwitansi WRITE, detail_kwitansi WRITE;` sebelum melakukan apa pun.
2. **Pre-Check Stok:** Lakukan looping untuk mengecek `stok` di tabel `barang`. Jika ada 1 saja stok yang kurang, langsung lompat ke Langkah 5 (Unlock & lempar error).
3. **Eksekusi Insert & Update:** 
   - `INSERT` data ke tabel `kwitansi`.
   - Looping untuk `INSERT` ke `detail_kwitansi` sekaligus `UPDATE` stok `barang` (`stok = stok - jumlah`).
4. **Compensating Transaction (Manual Rollback):** Gunakan blok `try...catch`. Jika terjadi Exception/Error di tengah eksekusi langkah 3, blok `catch` WAJIB berisi eksekusi manual untuk: 
   - Melakukan `UPDATE` penambahan stok kembali (hanya untuk barang yang terlanjur dipotong).
   - Melakukan `DELETE` pada tabel `kwitansi` dan `detail_kwitansi` yang terlanjur masuk.
5. **Release Lock:** Eksekusi `UNLOCK TABLES;` secara mutlak pada akhir blok sukses, MAUPUN di dalam blok `catch` setelah pembersihan kompensasi selesai.
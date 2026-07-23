# API & AJAX Routes Specification (Prosedural PHP 5.4)

Karena sistem tidak menggunakan framework, komunikasi AJAX dilakukan langsung ke file PHP terpisah yang bertindak sebagai endpoint. Semua respon wajib berupa JSON (`header('Content-Type: application/json');`).

## 1. Endpoint: Pencarian Barang (`api_cari_barang.php`)
- **Method**: GET
- **Parameter**: `keyword` (String nama barang)
- **Logika PHP**: Query ke tabel `barang` menggunakan operator `LIKE %keyword%`. Limit hasil maksimal 10 baris demi performa.
- **Format Respon Sukses**:
  ```json
  {
    "status": "success",
    "data": [
      { "id_barang": "5", "nama_barang": "Paracetamol 500mg", "harga": "15000", "stok": "120" }
    ]
  }
  ```

## 2. Endpoint: Simpan Kwitansi (`api_simpan_kwitansi.php`)
- **Method**: POST
- **Payload Data**: Nama pasien dan array data item (ID barang & jumlah).
- **Logika PHP**: Melakukan pengecekan stok manual terlebih dahulu, jika lolos, lakukan `INSERT` ke `kwitansi` lalu looping `INSERT` ke `detail_kwitansi` dan `UPDATE` stok.
- **Format Respon Gagal (Stok Habis / Validasi Gagal)**:
  ```json
  {
    "status": "error",
    "message": "Stok barang 'Amoxicillin' tidak mencukupi. Stok sisa: 2, diminta: 5."
  }
  ```

# Implementation Plan: Sistem Billing RKZ Hospital (Akaunting Clone)

## 📌 Spesifikasi Lingkungan (Environment)
*   **Bahasa:** PHP 5.4 (Native atau Micro-framework legacy seperti CodeIgniter 3)
*   **Database:** MySQL
*   **Engine DB:** MyISAM (Non-Transactional, No Foreign Keys)
*   **Konteks:** Sistem Invoicing & Billing Rumah Sakit

---

## ⚠️ Peringatan Arsitektur (Architecture Warnings)
Karena kita menggunakan engine **MyISAM**, kita kehilangan fitur *Database Transactions* (Commit/Rollback) dan *Foreign Keys*. Oleh karena itu, aturan coding berikut **wajib** diterapkan:
1.  **Validasi Berlapis:** Karena tidak ada *Foreign Keys*, PHP harus memvalidasi ketersediaan `patient_id` atau `doctor_id` sebelum melakukan `INSERT` ke tabel `invoices`.
2.  **Penanganan Error Manual:** Saat membuat *invoice* dengan banyak item (tindakan/obat), jika terjadi *error* di tengah proses penyimpanan item, PHP harus memiliki script untuk menghapus (*rollback* manual) header *invoice* yang sudah terlanjur tersimpan.
3.  **Tidak Ada Sintaks PHP Modern:** Hindari penggunaan *short array syntax* `[]` (jika menggunakan versi PHP 5.4 awal, pastikan kompatibilitas), *null coalescing* `??`, dan *scalar type hints*. Gunakan sintaks standar seperti `array()` dan `isset()`.

---

## 🗄️ Fase 1: Desain Database (MyISAM Edition)

Buat struktur tabel berikut. Karena tidak ada *Foreign Keys*, pastikan Anda menambahkan **INDEX** pada kolom relasi untuk mempercepat pencarian (queries).

### 1. Tabel `patients` (Pasien)
*   `id` (INT, PK, Auto Increment)
*   `medical_record_no` (VARCHAR(20), UNIQUE)
*   `name` (VARCHAR(100))
*   `dob` (DATE)
*   `insurance_type` (ENUM: 'Umum', 'BPJS', 'Asuransi Swasta')
*   `created_at` (DATETIME)

### 2. Tabel `doctors` (Dokter DPJP)
*   `id` (INT, PK, Auto Increment)
*   `name` (VARCHAR(100))
*   `specialization` (VARCHAR(100))

### 3. Tabel `medical_items` (Tindakan, Kamar, & Obat)
*   `id` (INT, PK, Auto Increment)
*   `item_code` (VARCHAR(50), UNIQUE)
*   `name` (VARCHAR(150))
*   `category` (ENUM: 'Kamar', 'Tindakan', 'Obat', 'Jasa Dokter')
*   `price` (DECIMAL(15,2))

### 4. Tabel `invoices` (Header Tagihan)
*   `id` (INT, PK, Auto Increment)
*   `invoice_number` (VARCHAR(50), UNIQUE)
*   `patient_id` (INT, INDEX) -> *Relasi manual ke tabel patients*
*   `doctor_id` (INT, INDEX) -> *Relasi manual ke tabel doctors*
*   `invoice_date` (DATE)
*   `due_date` (DATE)
*   `subtotal` (DECIMAL(15,2))
*   `discount` (DECIMAL(15,2))
*   `total` (DECIMAL(15,2))
*   `status` (ENUM: 'Draft', 'Unpaid', 'Paid', 'Cancelled')

### 5. Tabel `invoice_items` (Detail Tagihan)
*   `id` (INT, PK, Auto Increment)
*   `invoice_id` (INT, INDEX) -> *Relasi manual ke tabel invoices*
*   `item_id` (INT, INDEX) -> *Relasi manual ke tabel medical_items*
*   `quantity` (INT)
*   `price` (DECIMAL(15,2))
*   `total` (DECIMAL(15,2))

---

## 🛠️ Fase 2: Struktur Direktori & Logic Dasar

Gunakan arsitektur MVC (Model-View-Controller) sederhana untuk PHP 5.4:

```text
/rkz-billing
│
├── /config
│   └── database.php (Koneksi mysqli atau PDO)
│
├── /controllers
│   ├── InvoiceController.php
│   ├── PatientController.php
│   └── ItemController.php
│
├── /models
│   ├── InvoiceModel.php (Berisi query MySQL CRUD untuk invoice)
│   └── PatientModel.php
│
├── /views
│   ├── layout.php
│   ├── invoices/
│   │   ├── create.php (Form pembuatan invoice)
│   │   └── view.php (Tampilan detail/PDF invoice)
│
└── index.php (Front Controller / Router sederhana)
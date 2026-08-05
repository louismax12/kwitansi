<?php
require 'config/database.php';

echo "Memulai sinkronisasi stok lama...\n";

// Mengambil semua barang yang terjual sebelum fitur potong stok otomatis ditambahkan
// Yaitu semua invoice sebelum jam 10:48 hari ini
$query = "
    SELECT d.id_barang, SUM(d.jumlah) as total_terpakai
    FROM kwitansi_detail_kwitansi d
    JOIN kwitansi_cetak k ON d.no_kwitansi = k.no_kwitansi
    WHERE k.tanggal_transaksi < '2026-08-05 10:48:00'
    GROUP BY d.id_barang
";

$res = $conn->query($query);

if ($res && $res->num_rows > 0) {
    while($row = $res->fetch_assoc()) {
        $id_barang = (int)$row['id_barang'];
        $jumlah_terpakai = (int)$row['total_terpakai'];
        
        $conn->query("UPDATE kwitansi_kode_barang SET stok = stok - $jumlah_terpakai WHERE id_barang = $id_barang");
        echo "Stok Barang ID $id_barang berhasil dikurangi sebanyak $jumlah_terpakai.\n";
    }
    echo "Sinkronisasi selesai! Semua stok dari invoice lama telah terpotong.";
} else {
    echo "Tidak ada data invoice lama yang perlu disinkronkan, atau terjadi error.";
}

<?php
require 'config/database.php';
$no = 'TEST-01';
$id_barang = null;
$nama_detail = 'TEST DETAIL';
$jumlah = 1;
$subtotal = 50000;

$stmt = $conn->prepare("INSERT INTO kwitansi_detail_kwitansi (no_kwitansi, id_barang, nama_detail, jumlah, subtotal) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sisii", $no, $id_barang, $nama_detail, $jumlah, $subtotal);
if ($stmt->execute()) {
    echo "SUCCESS";
} else {
    echo "ERROR: " . $stmt->error;
}

<?php
require "config/database.php";
$conn->query("RENAME TABLE kode_barang TO kwitansi_kode_barang");
$conn->query("RENAME TABLE detail_kwitansi TO kwitansi_detail_kwitansi");
$conn->query("RENAME TABLE inventori TO kwitansi_inventori");
$conn->query("RENAME TABLE kwitansi TO kwitansi_cetak");
$conn->query("RENAME TABLE pelanggan TO kwitansi_pelanggan");
$conn->query("RENAME TABLE user_profile TO kwitansi_user_profile");
echo "Tables renamed to kwitansi_ prefix.";

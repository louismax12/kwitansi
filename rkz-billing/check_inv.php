<?php
require 'config/database.php';
$res = $conn->query("SELECT no_kwitansi, tanggal_transaksi FROM kwitansi_cetak ORDER BY tanggal_transaksi DESC LIMIT 10");
while($r = $res->fetch_assoc()) {
    echo $r['no_kwitansi'] . " - " . $r['tanggal_transaksi'] . "\n";
}

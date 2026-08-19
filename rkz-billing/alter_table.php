<?php
$conn = new mysqli('192.168.2.12', 'anugrah', 'anugrah', 'keuangan');
$res = $conn->query('ALTER TABLE kwitansi_cetak ADD COLUMN keterangan VARCHAR(255) DEFAULT NULL;');
if($res) {
    echo 'SUCCESS';
} else {
    echo $conn->error;
}

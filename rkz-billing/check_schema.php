<?php
require 'config/database.php';
$res = $conn->query("DESCRIBE kwitansi_kode_barang");
if ($res) {
    while($r = $res->fetch_assoc()) {
        echo $r['Field'] . "\n";
    }
} else {
    echo $conn->error;
}

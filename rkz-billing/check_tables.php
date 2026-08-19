<?php
require 'config/database.php';
$r = $conn->query('ALTER TABLE kwitansi_detail_kwitansi ADD COLUMN nama_detail VARCHAR(255) DEFAULT NULL;');
if ($r) {
    echo "OK";
} else {
    echo $conn->error;
}

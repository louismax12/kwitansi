<?php
require 'config/database.php';
$r = $conn->query('SHOW CREATE TABLE kwitansi_detail_kwitansi');
if ($r) {
    while($row = $r->fetch_assoc()) {
        echo $row['Create Table'] . "\n";
    }
} else {
    echo $conn->error;
}

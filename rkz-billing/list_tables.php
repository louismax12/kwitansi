<?php
require 'config/database.php';
$r = $conn->query('SHOW TABLES');
if ($r) {
    while($row = $r->fetch_row()) {
        echo $row[0] . "\n";
    }
} else {
    echo $conn->error;
}

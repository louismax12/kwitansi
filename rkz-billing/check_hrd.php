<?php
require 'config/database.php';
$r = $conn->query("SELECT * FROM kwitansi_user_profile");
while($row = $r->fetch_assoc()) {
    print_r($row);
}

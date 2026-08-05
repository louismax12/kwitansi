<?php
require "config/database.php";
$conn->query("RENAME TABLE barang TO kode_barang");
$conn->query("RENAME TABLE users TO user_profile");
echo "Tables renamed.";

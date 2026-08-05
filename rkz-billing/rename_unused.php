<?php
require "config/database.php";
$conn->query("RENAME TABLE brg TO kwitansi_brg");
$conn->query("RENAME TABLE user TO kwitansi_user");
echo "Tables brg and user renamed.";

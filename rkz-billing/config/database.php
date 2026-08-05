<?php
// config/database.php
// PHP 5.4 compatible database connection for MyISAM schema

$db_host = '192.168.2.12';
$db_user = 'anugrah';
$db_pass = 'anugrah';
$db_name = 'keuangan'; // Sesuai permintaan, nama database di server adalah keuangan

$conn = @new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error . " (Pastikan IP 192.168.2.12 bisa diakses)");
}

// Set charset to match schema.sql
$conn->set_charset("utf8mb4");

return $conn;

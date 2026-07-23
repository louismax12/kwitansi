<?php
// config/database.php
// PHP 5.4 compatible database connection for MyISAM schema

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '123456789'; // Default XAMPP/WAMP empty password, adjust if necessary
$db_name = 'db_kwitansi'; // The database name, adjust if necessary

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to match schema.sql
$conn->set_charset("utf8mb4");

return $conn;

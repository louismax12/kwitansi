<?php
// koneksi.php
$host = 'localhost';
$db   = 'db_kwitansi';
$user = 'root';
$pass = '123456789'; // Default local password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = array(
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
);

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
     die("Koneksi database gagal: " . $e->getMessage());
}
?>

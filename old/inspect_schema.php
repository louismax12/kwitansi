<?php
require_once __DIR__ . '/config.php';
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$db = new PDO($dsn, DB_USER, DB_PASS, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC));
$tables = array('users','kwitansi','detail_kwitansi','barang');
foreach ($tables as $table) {
    echo "TABLE $table\n";
    $stmt = $db->query('DESCRIBE ' . $table);
    while ($row = $stmt->fetch()) {
        echo $row['Field'] . ' => ' . $row['Type'] . "\n";
    }
    echo "\n";
}

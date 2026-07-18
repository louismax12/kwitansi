<?php
require_once __DIR__ . '/config.php';
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
$options = array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
);
try {
    $db = new PDO($dsn, DB_USER, DB_PASS, $options);
    echo "CONNECTED\n";
    $stmt = $db->query('SHOW TABLES');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        echo $row[0] . "\n";
    }
} catch (PDOException $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}

<?php
require_once __DIR__ . '/config.php';

$db = new PDO('mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
));

$sql = file_get_contents(__DIR__ . '/db/schema.sql');
$statements = array_filter(array_map('trim', preg_split('/;\s*/', $sql)));

foreach ($statements as $statement) {
    if ($statement !== '') {
        $db->exec($statement);
    }
}

echo 'Database siap.';

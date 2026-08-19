<?php
try {
    $db = new PDO('mysql:host=localhost;port=3306', 'root', '');
    $sql = file_get_contents('kwitansi.sql');
    $db->exec($sql);
    echo "Database db_kwitansi created and seeded successfully.\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

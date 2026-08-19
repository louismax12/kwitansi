<?php
try {
    $db = new PDO('mysql:host=192.168.2.12;port=3306', 'anugrah', 'anugrah');
    $sql = file_get_contents('kwitansi.sql');
    $db->exec($sql);
    echo "Database db_kwitansi created and seeded successfully on remote server.\n";
} catch(Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

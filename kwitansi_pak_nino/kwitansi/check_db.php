<?php
$db = new PDO('mysql:host=192.168.2.12;port=3306', 'anugrah', 'anugrah');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $db->query('SHOW DATABASES');
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

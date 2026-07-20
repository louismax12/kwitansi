<?php
require_once __DIR__ . '/app_common.php';

header('Content-Type: application/json');

$db = connectDb();
$keyword = trim(isset($_GET['keyword']) ? $_GET['keyword'] : '');

if ($keyword === '') {
    $stmt = $db->prepare('SELECT id_barang, nama_barang, harga, stok FROM barang ORDER BY nama_barang ASC LIMIT 10');
    $stmt->execute();
    $rows = $stmt->fetchAll();
} else {
    $like = '%' . $keyword . '%';
    $stmt = $db->prepare('SELECT id_barang, nama_barang, harga, stok FROM barang WHERE nama_barang LIKE ? ORDER BY nama_barang ASC LIMIT 10');
    $stmt->execute(array($like));
    $rows = $stmt->fetchAll();
}

echo json_encode(array('status' => 'success', 'data' => $rows));

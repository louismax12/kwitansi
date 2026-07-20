<?php
// api_cari_barang.php
header('Content-Type: application/json');
require_once 'koneksi.php';

if (isset($_GET['keyword'])) {
    $keyword = '%' . $_GET['keyword'] . '%';
    
    try {
        $stmt = $pdo->prepare("SELECT * FROM barang WHERE nama_barang LIKE :keyword LIMIT 10");
        $stmt->execute(array(':keyword' => $keyword));
        $data = $stmt->fetchAll();
        
        echo json_encode(array(
            "status" => "success",
            "data" => $data
        ));
    } catch (PDOException $e) {
        echo json_encode(array(
            "status" => "error",
            "message" => "Database error: " . $e->getMessage()
        ));
    }
} else {
    echo json_encode(array("status" => "error", "message" => "Parameter keyword tidak ada"));
}
?>

<?php
// controllers/BarangController.php

require_once __DIR__ . '/../models/BarangModel.php';

class BarangController {
    private $conn;
    private $barangModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->barangModel = new BarangModel($conn);
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama_barang'];
            $kategori = $_POST['kategori'];
            $harga = $_POST['harga'];
            $stok = $_POST['stok'];
            $barcode = empty($_POST['barcode']) ? null : $_POST['barcode'];

            $stmt = $this->conn->prepare("INSERT INTO barang (barcode, nama_barang, kategori, harga, stok) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssii", $barcode, $nama, $kategori, $harga, $stok);
            if ($stmt->execute()) {
                header("Location: index.php?c=barang");
                exit;
            }
        }

        $barangList = $this->barangModel->getAll();
        $content = __DIR__ . '/../views/barang/index.php';
        require_once __DIR__ . '/../views/layout.php';
    }
}

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

            $stmt = $this->conn->prepare("INSERT INTO kwitansi_kode_barang (barcode, nama_barang, kategori, harga, stok) VALUES (?, ?, ?, ?, ?)");
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

    public function addStock() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_barang']) && isset($_POST['stok_masuk'])) {
            $id_barang = intval($_POST['id_barang']);
            $stok_masuk = intval($_POST['stok_masuk']);
            
            $stmt = $this->conn->prepare("UPDATE kwitansi_kode_barang SET stok = stok + ? WHERE id_barang = ?");
            $stmt->bind_param("ii", $stok_masuk, $id_barang);
            $stmt->execute();
        }
        header("Location: index.php?c=barang");
        exit;
    }
}

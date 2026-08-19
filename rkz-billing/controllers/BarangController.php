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

            if (!empty($nama)) {
                $stmt = $this->conn->prepare("INSERT INTO kwitansi_brg (nm_brg) VALUES (?)");
                $stmt->bind_param("s", $nama);
                if ($stmt->execute()) {
                    header("Location: index.php?c=barang");
                    exit;
                }
            }
        }

        $barangList = $this->barangModel->getAll();
        $content = __DIR__ . '/../views/barang/index.php';
        require_once __DIR__ . '/../views/layout.php';
    }
}

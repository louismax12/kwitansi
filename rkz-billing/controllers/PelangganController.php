<?php
// controllers/PelangganController.php

require_once __DIR__ . '/../models/PelangganModel.php';

class PelangganController {
    private $conn;
    private $pelangganModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->pelangganModel = new PelangganModel($conn);
    }

    public function index() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama_pelanggan'];
            $no_hp = $_POST['no_hp'];
            $alamat = $_POST['alamat'];

            $stmt = $this->conn->prepare("INSERT INTO kwitansi_pelanggan (nama_pelanggan, no_hp, alamat) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $nama, $no_hp, $alamat);
            if ($stmt->execute()) {
                header("Location: index.php?c=pelanggan");
                exit;
            }
        }

        $pelangganList = $this->pelangganModel->getAll();
        $content = __DIR__ . '/../views/pelanggan/index.php';
        require_once __DIR__ . '/../views/layout.php';
    }

    public function edit() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nama = $_POST['nama_pelanggan'];
            $no_hp = $_POST['no_hp'];
            $alamat = $_POST['alamat'];
            
            if ($this->pelangganModel->update($id, $nama, $no_hp, $alamat)) {
                header("Location: index.php?c=pelanggan");
                exit;
            }
        }
        
        $p = $this->pelangganModel->getById($id);
        if (!$p) {
            die("Pelanggan tidak ditemukan.");
        }
        
        $content = __DIR__ . '/../views/pelanggan/edit.php';
        require_once __DIR__ . '/../views/layout.php';
    }

    public function delete() {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
        if ($id > 0) {
            $this->pelangganModel->delete($id);
        }
        header("Location: index.php?c=pelanggan");
        exit;
    }
}

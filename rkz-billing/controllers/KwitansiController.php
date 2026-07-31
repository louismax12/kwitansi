<?php
// controllers/KwitansiController.php

require_once __DIR__ . '/../models/KwitansiModel.php';
require_once __DIR__ . '/../models/PelangganModel.php';
require_once __DIR__ . '/../models/BarangModel.php';

class KwitansiController {
    private $conn;
    private $kwitansiModel;
    private $pelangganModel;
    private $barangModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->kwitansiModel = new KwitansiModel($conn);
        $this->pelangganModel = new PelangganModel($conn);
        $this->barangModel = new BarangModel($conn);
    }

    public function index() {
        // Obsoleted redirect, replace with actual history method if called as index
        $this->history();
    }

    public function history() {
        $stmt = $this->conn->query("SELECT * FROM kwitansi ORDER BY tanggal_transaksi DESC");
        $history = array();
        if ($stmt) {
            while ($row = $stmt->fetch_assoc()) {
                $history[] = $row;
            }
        }
        $content = __DIR__ . '/../views/kwitansi/index.php';
        require_once __DIR__ . '/../views/layout.php';
    }

    public function create() {
        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array(
                'id_pelanggan' => isset($_POST['id_pelanggan']) && !empty($_POST['id_pelanggan']) ? intval($_POST['id_pelanggan']) : null,
                'nama_pasien' => isset($_POST['nama_pasien']) ? $_POST['nama_pasien'] : '',
                'total_bayar' => 0,
                'id_user' => 1, // Mock user id
            );

            $items = array();
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    if (!empty($item['id_barang']) && !empty($item['jumlah'])) {
                        $barang = $this->barangModel->getById($item['id_barang']);
                        if ($barang) {
                            $subtotal = $barang['harga'] * intval($item['jumlah']);
                            $data['total_bayar'] += $subtotal;
                            
                            $items[] = array(
                                'id_barang' => $item['id_barang'],
                                'jumlah' => $item['jumlah'],
                                'subtotal' => $subtotal
                            );
                        }
                    }
                }
            }

            if (empty($data['nama_pasien']) || empty($items)) {
                $error = "Nama pasien dan minimal 1 item barang harus diisi.";
            } else {
                $no_kwitansi = $this->kwitansiModel->createInvoice($data, $items);
                if ($no_kwitansi) {
                    header("Location: index.php?c=kwitansi&a=view&id=" . urlencode($no_kwitansi));
                    exit;
                } else {
                    $error = "Terjadi kesalahan saat menyimpan data kwitansi.";
                }
            }
        }

        $pelangganList = $this->pelangganModel->getAll();
        $barangList = $this->barangModel->getAll();

        $content = __DIR__ . '/../views/kwitansi/create.php';
        require_once __DIR__ . '/../views/layout.php';
    }

    public function view() {
        $no_kwitansi = isset($_GET['id']) ? $_GET['id'] : '';
        if (empty($no_kwitansi)) {
            die("ID Kwitansi tidak valid.");
        }

        $no_kwitansi_safe = $this->conn->real_escape_string($no_kwitansi);
        
        $res = $this->conn->query("SELECT * FROM kwitansi WHERE no_kwitansi = '$no_kwitansi_safe'");
        $kwitansi = $res ? $res->fetch_assoc() : null;

        if (!$kwitansi) {
            die("Kwitansi tidak ditemukan.");
        }

        $resItems = $this->conn->query("SELECT d.*, b.nama_barang, b.harga FROM detail_kwitansi d JOIN barang b ON d.id_barang = b.id_barang WHERE d.no_kwitansi = '$no_kwitansi_safe'");
        $items = array();
        if ($resItems) {
            while ($row = $resItems->fetch_assoc()) {
                $items[] = $row;
            }
        }

        // Fetch User
        $id_user = isset($kwitansi['id_user']) ? intval($kwitansi['id_user']) : 1;
        $resUser = $this->conn->query("SELECT username FROM users LIMIT 1");
        $kwitansi['nama_user'] = 'Admin';
        if ($resUser && $rowUser = $resUser->fetch_assoc()) {
            $kwitansi['nama_user'] = $rowUser['username'];
        }

        $content = __DIR__ . '/../views/kwitansi/view.php';
        require_once __DIR__ . '/../views/layout.php';
    }
}

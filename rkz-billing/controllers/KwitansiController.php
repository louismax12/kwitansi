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
        $stmt = $this->conn->query("SELECT * FROM kwitansi_cetak ORDER BY tanggal_transaksi DESC");
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
            $id_pel_post = isset($_POST['id_pelanggan']) ? $_POST['id_pelanggan'] : '';
            $id_pelanggan = null;
            if (!empty($id_pel_post)) {
                if (is_numeric($id_pel_post)) {
                    $id_pelanggan = intval($id_pel_post);
                } else {
                    // New patient tag from Select2
                    $nama_baru = $id_pel_post;
                    $stmt_pel = $this->conn->prepare("INSERT INTO kwitansi_pelanggan (nama_pelanggan, alamat, no_hp) VALUES (?, '', '')");
                    $stmt_pel->bind_param("s", $nama_baru);
                    if ($stmt_pel->execute()) {
                        $id_pelanggan = $stmt_pel->insert_id;
                    }
                }
            }

            $data = array(
                'id_pelanggan' => $id_pelanggan,
                'nama_pasien' => isset($_POST['nama_pasien']) ? $_POST['nama_pasien'] : '',
                'total_bayar' => 0,
                'id_user' => isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin',
            );

            $items = array();
            if (isset($_POST['items']) && is_array($_POST['items'])) {
                foreach ($_POST['items'] as $item) {
                    if (!empty($item['id_barang']) && !empty($item['jumlah'])) {
                        if (!is_numeric($item['id_barang'])) {
                            $nama = $item['id_barang'];
                            $harga = isset($item['harga_baru']) ? intval($item['harga_baru']) : 0;
                            
                            $stmt = $this->conn->prepare("INSERT INTO kwitansi_kode_barang (nama_barang, harga, stok) VALUES (?, ?, 0)");
                            $stmt->bind_param("si", $nama, $harga);
                            if ($stmt->execute()) {
                                $new_id = $stmt->insert_id;
                                $subtotal = $harga * intval($item['jumlah']);
                                $data['total_bayar'] += $subtotal;
                                
                                $items[] = array(
                                    'id_barang' => $new_id,
                                    'jumlah' => $item['jumlah'],
                                    'subtotal' => $subtotal
                                );
                            }
                        } else {
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
        
        $res = $this->conn->query("SELECT * FROM kwitansi_cetak WHERE no_kwitansi = '$no_kwitansi_safe'");
        $kwitansi = $res ? $res->fetch_assoc() : null;

        if (!$kwitansi) {
            die("Kwitansi tidak ditemukan.");
        }

        $resItems = $this->conn->query("SELECT d.*, b.nama_barang, b.harga FROM kwitansi_detail_kwitansi d JOIN kwitansi_kode_barang b ON d.id_barang = b.id_barang WHERE d.no_kwitansi = '$no_kwitansi_safe'");
        $items = array();
        if ($resItems) {
            while ($row = $resItems->fetch_assoc()) {
                $items[] = $row;
            }
        }

        // Fetch User
        $id_user = isset($kwitansi['id_user']) ? $kwitansi['id_user'] : 'Admin';
        $kwitansi['nama_user'] = ($id_user == '1') ? 'ADMIN' : strtoupper($id_user);

        $content = __DIR__ . '/../views/kwitansi/view.php';
        require_once __DIR__ . '/../views/layout.php';
    }
}

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
            $nama_pasien = isset($_POST['nama_pasien']) ? trim($_POST['nama_pasien']) : '';
            $id_pelanggan = null;

            // Try to find if this patient exists, else create new
            if (!empty($nama_pasien)) {
                $stmt = $this->conn->prepare("SELECT id_pelanggan FROM kwitansi_pelanggan WHERE nama_pelanggan = ? LIMIT 1");
                $stmt->bind_param("s", $nama_pasien);
                $stmt->execute();
                $res = $stmt->get_result();
                if ($res && $res->num_rows > 0) {
                    $row = $res->fetch_assoc();
                    $id_pelanggan = $row['id_pelanggan'];
                } else {
                    $stmt_pel = $this->conn->prepare("INSERT INTO kwitansi_pelanggan (nama_pelanggan, alamat, no_hp) VALUES (?, '', '')");
                    $stmt_pel->bind_param("s", $nama_pasien);
                    if ($stmt_pel->execute()) {
                        $id_pelanggan = $stmt_pel->insert_id;
                    }
                }
            }

            $data = array(
                'id_pelanggan' => $id_pelanggan,
                'nama_pasien' => $nama_pasien,
                'total_bayar' => 0,
                'id_user' => isset($_SESSION['username']) ? $_SESSION['username'] : 'Admin',
                'no_faktur' => isset($_POST['no_faktur']) ? strtoupper(trim($_POST['no_faktur'])) : '',
                'untuk_pembayaran' => isset($_POST['untuk_pembayaran']) ? strtoupper(trim($_POST['untuk_pembayaran'])) : '',
                'keterangan' => isset($_POST['keterangan']) ? trim($_POST['keterangan']) : '',
            );

            $items = array();
            if (isset($_POST['det_kd']) && is_array($_POST['det_kd'])) {
                $kds = $_POST['det_kd'];
                $namas = isset($_POST['det_nama']) ? $_POST['det_nama'] : [];
                $jmls = isset($_POST['det_jml']) ? $_POST['det_jml'] : [];
                
                foreach ($kds as $idx => $kd) {
                    $nama = isset($namas[$idx]) ? trim($namas[$idx]) : '';
                    $jmlRaw = isset($jmls[$idx]) ? trim($jmls[$idx]) : '0';
                    $jml = intval(str_replace(['.', ','], ['', '.'], $jmlRaw));
                    
                    if (!empty($nama) && $jml > 0) {
                        $id_barang = null;
                        
                        if (!empty($kd) && is_numeric($kd)) {
                            $id_barang = intval($kd);
                        }
                        
                        $data['total_bayar'] += $jml;
                        $items[] = array(
                            'id_barang' => $id_barang,
                            'nama_detail' => $nama,
                            'jumlah' => 1,
                            'subtotal' => $jml
                        );
                    }
                }
            }

            // Fallback for non-detail mode
            if (empty($items)) {
                $rawJumlah = isset($_POST['jumlah']) ? floatval($_POST['jumlah']) : 0;
                $rawUntuk = isset($_POST['untuk_pembayaran']) ? trim($_POST['untuk_pembayaran']) : 'UMUM';
                
                if ($rawJumlah > 0) {
                    $data['total_bayar'] = $rawJumlah;
                    $items[] = array(
                        'id_barang' => null,
                        'nama_detail' => $rawUntuk,
                        'jumlah' => 1,
                        'subtotal' => $rawJumlah
                    );
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

        $resItems = $this->conn->query("SELECT d.*, COALESCE(d.nama_detail, b.nm_brg) AS nama_barang, 0 AS harga FROM kwitansi_detail_kwitansi d LEFT JOIN kwitansi_brg b ON d.id_barang = b.kd_brg WHERE d.no_kwitansi = '$no_kwitansi_safe'");
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

    public function edit() {
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

        $resItems = $this->conn->query("SELECT d.*, COALESCE(d.nama_detail, b.nm_brg) AS nama_barang FROM kwitansi_detail_kwitansi d LEFT JOIN kwitansi_brg b ON d.id_barang = b.kd_brg WHERE d.no_kwitansi = '$no_kwitansi_safe'");
        $existingItems = array();
        if ($resItems) {
            while ($row = $resItems->fetch_assoc()) {
                $existingItems[] = $row;
            }
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = array(
                'id_pelanggan' => null, // Optional if we had it
                'nama_pasien' => isset($_POST['nama_pasien']) ? trim($_POST['nama_pasien']) : '',
                'total_bayar' => 0,
                'no_faktur' => isset($_POST['no_faktur']) ? trim($_POST['no_faktur']) : '',
                'untuk_pembayaran' => isset($_POST['untuk_pembayaran']) ? trim($_POST['untuk_pembayaran']) : '',
                'keterangan' => isset($_POST['keterangan']) ? trim($_POST['keterangan']) : ''
            );

            $items = array();
            if (isset($_POST['det_kd']) && is_array($_POST['det_kd'])) {
                $kds = $_POST['det_kd'];
                $namas = isset($_POST['det_nama']) ? $_POST['det_nama'] : [];
                $jmls = isset($_POST['det_jml']) ? $_POST['det_jml'] : [];
                
                foreach ($kds as $idx => $kd) {
                    $nama = isset($namas[$idx]) ? trim($namas[$idx]) : '';
                    $jmlRaw = isset($jmls[$idx]) ? trim($jmls[$idx]) : '0';
                    $jml = intval(str_replace(['.', ','], ['', '.'], $jmlRaw));
                    
                    if (!empty($nama) && $jml > 0) {
                        $id_barang = null;
                        if (!empty($kd) && is_numeric($kd)) {
                            $id_barang = intval($kd);
                        }
                        $data['total_bayar'] += $jml;
                        $items[] = array(
                            'id_barang' => $id_barang,
                            'nama_detail' => $nama,
                            'jumlah' => 1,
                            'subtotal' => $jml
                        );
                    }
                }
            }

            // Fallback for non-detail mode
            if (empty($items)) {
                $rawJumlah = isset($_POST['jumlah']) ? floatval($_POST['jumlah']) : 0;
                $rawUntuk = isset($_POST['untuk_pembayaran']) ? trim($_POST['untuk_pembayaran']) : 'UMUM';
                
                if ($rawJumlah > 0) {
                    $data['total_bayar'] = $rawJumlah;
                    $items[] = array(
                        'id_barang' => null,
                        'nama_detail' => $rawUntuk,
                        'jumlah' => 1,
                        'subtotal' => $rawJumlah
                    );
                }
            }

            if (empty($data['nama_pasien']) || empty($items)) {
                $error = "Nama pasien dan minimal 1 item barang harus diisi.";
            } else {
                $success = $this->kwitansiModel->updateInvoice($no_kwitansi, $data, $items);
                if ($success) {
                    header("Location: index.php?c=kwitansi&a=view&id=" . urlencode($no_kwitansi));
                    exit;
                } else {
                    $error = "Terjadi kesalahan saat memperbarui data kwitansi.";
                }
            }
        }

        $pelangganList = $this->pelangganModel->getAll();
        $barangList = $this->barangModel->getAll();

        $content = __DIR__ . '/../views/kwitansi/edit.php';
        require_once __DIR__ . '/../views/layout.php';
    }
}

<?php
// controllers/Riwayat.php
require_once 'models/Database.php';

class Riwayat {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index() {
        $search = isset($_GET['q']) ? $_GET['q'] : '';
        
        $sql = "SELECT k.*, u.nama_petugas as kasir 
                FROM kwitansi k 
                LEFT JOIN user u ON k.id_user = u.id_user";
        
        if ($search !== '') {
            $sql .= " WHERE k.no_kwitansi LIKE :q OR k.nama_pasien LIKE :q";
        }
        $sql .= " ORDER BY k.tanggal_transaksi DESC LIMIT 50";
        
        $stmt = $this->db->prepare($sql);
        if ($search !== '') {
            $stmt->execute(array(':q' => "%$search%"));
        } else {
            $stmt->execute();
        }
        
        $riwayat = $stmt->fetchAll();
        require_once 'views/history/index.php';
    }

    public function detail() {
        header('Content-Type: application/json');
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            
            $stmt = $this->db->prepare("SELECT k.*, u.nama_petugas as kasir FROM kwitansi k LEFT JOIN user u ON k.id_user = u.id_user WHERE k.no_kwitansi = :id");
            $stmt->execute(array(':id' => $id));
            $kwitansi = $stmt->fetch();
            
            if ($kwitansi) {
                $stmt2 = $this->db->prepare("SELECT d.*, b.nama_barang FROM detail_kwitansi d LEFT JOIN barang b ON d.id_barang = b.id_barang WHERE d.no_kwitansi = :id");
                $stmt2->execute(array(':id' => $id));
                $kwitansi['items'] = $stmt2->fetchAll();
                
                echo json_encode(array("status" => "success", "data" => $kwitansi));
            } else {
                echo json_encode(array("status" => "error", "message" => "Not found"));
            }
        }
    }
}
?>

<?php
// controllers/Dashboard.php
require_once 'models/Database.php';

class Dashboard {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index() {
        $hari_ini = date('Y-m-d');
        
        // Total Kwitansi Hari Ini
        $stmt1 = $this->db->prepare("SELECT COUNT(*) as total_transaksi, SUM(total_bayar) as pendapatan_hari_ini FROM kwitansi WHERE DATE(tanggal_transaksi) = :tgl");
        $stmt1->execute(array(':tgl' => $hari_ini));
        $stat = $stmt1->fetch();

        $total_transaksi = $stat['total_transaksi'] ? $stat['total_transaksi'] : 0;
        $pendapatan = $stat['pendapatan_hari_ini'] ? $stat['pendapatan_hari_ini'] : 0;

        require_once 'views/dashboard/index.php';
    }
}
?>

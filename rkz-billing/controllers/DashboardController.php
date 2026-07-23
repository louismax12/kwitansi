<?php
// controllers/DashboardController.php

class DashboardController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function index() {
        // Get total tagihan bulan ini
        $month = date('Y-m');
        $stmt1 = $this->conn->prepare("SELECT SUM(total_bayar) as total FROM kwitansi WHERE tanggal_transaksi LIKE ?");
        $likeMonth = $month . '%';
        $stmt1->bind_param("s", $likeMonth);
        $stmt1->execute();
        $res1 = $stmt1->get_result()->fetch_assoc();
        $totalPendapatan = $res1['total'] ? $res1['total'] : 0;

        // Get total pelanggan
        $res2 = $this->conn->query("SELECT COUNT(*) as count FROM pelanggan");
        $totalPelanggan = $res2->fetch_assoc()['count'];

        // Get total kwitansi
        $res3 = $this->conn->query("SELECT COUNT(*) as count FROM kwitansi");
        $totalKwitansi = $res3->fetch_assoc()['count'];

        $content = __DIR__ . '/../views/dashboard/index.php';
        require_once __DIR__ . '/../views/layout.php';
    }
}

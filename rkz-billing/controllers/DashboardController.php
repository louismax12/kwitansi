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
        $monthEscaped = $this->conn->real_escape_string($month) . '%';
        $res1 = $this->conn->query("SELECT SUM(total_bayar) as total FROM kwitansi WHERE tanggal_transaksi LIKE '$monthEscaped'");
        $row1 = $res1 ? $res1->fetch_assoc() : null;
        $totalPendapatan = ($row1 && $row1['total']) ? $row1['total'] : 0;

        // Get total pelanggan
        $res2 = $this->conn->query("SELECT COUNT(*) as count FROM pelanggan");
        $row2 = $res2 ? $res2->fetch_assoc() : null;
        $totalPelanggan = $row2 ? $row2['count'] : 0;

        // Get total kwitansi
        $res3 = $this->conn->query("SELECT COUNT(*) as count FROM kwitansi");
        $row3 = $res3 ? $res3->fetch_assoc() : null;
        $totalKwitansi = $row3 ? $row3['count'] : 0;

        $content = __DIR__ . '/../views/dashboard/index.php';
        require_once __DIR__ . '/../views/layout.php';
    }
}

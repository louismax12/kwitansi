<?php
// controllers/Laporan.php
require_once 'models/Database.php';

class Laporan {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index() {
        $start = isset($_GET['start']) ? $_GET['start'] : date('Y-m-01'); // Default awal bulan
        $end = isset($_GET['end']) ? $_GET['end'] : date('Y-m-d'); // Default hari ini

        $stmt = $this->db->prepare("SELECT k.no_kwitansi, k.tanggal_transaksi, k.nama_pasien, k.total_bayar, u.nama_petugas 
                                    FROM kwitansi k 
                                    LEFT JOIN user u ON k.id_user = u.id_user 
                                    WHERE DATE(k.tanggal_transaksi) >= :start AND DATE(k.tanggal_transaksi) <= :end 
                                    ORDER BY k.tanggal_transaksi ASC");
        $stmt->execute(array(':start' => $start, ':end' => $end));
        $data = $stmt->fetchAll();

        if (isset($_GET['export']) && $_GET['export'] == 'csv') {
            $this->exportCSV($data, $start, $end);
            return;
        }

        require_once 'views/laporan/index.php';
    }

    private function exportCSV($data, $start, $end) {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=Laporan_Pendapatan_' . $start . '_to_' . $end . '.csv');
        $output = fopen('php://output', 'w');
        
        fputcsv($output, array('No Kwitansi', 'Tanggal', 'Nama Pasien', 'Kasir', 'Total Bayar'));
        $total = 0;
        foreach ($data as $row) {
            fputcsv($output, array($row['no_kwitansi'], $row['tanggal_transaksi'], $row['nama_pasien'], $row['nama_petugas'], $row['total_bayar']));
            $total += $row['total_bayar'];
        }
        fputcsv($output, array('', '', '', 'GRAND TOTAL', $total));
        fclose($output);
    }
}
?>

<?php
// models/KwitansiModel.php

class KwitansiModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    // Manual Transaction implementation for MyISAM
    public function createInvoice($data, $items) {
        // Since MyISAM does not support transactions, we simulate it
        // by keeping track of the inserted ID and rolling back manually on error.
        
        $no_kwitansi = $this->generateInvoiceNumber();
        $tanggal = date('Y-m-d H:i:s');
        $id_pelanggan = isset($data['id_pelanggan']) ? $data['id_pelanggan'] : null;
        $nama_pasien = $data['nama_pasien'];
        $total_bayar = $data['total_bayar'];
        $id_user = $data['id_user'];

        // 1. Insert Header
        $stmt = $this->conn->prepare("INSERT INTO kwitansi (no_kwitansi, tanggal_transaksi, id_pelanggan, nama_pasien, total_bayar, id_user) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssisii", $no_kwitansi, $tanggal, $id_pelanggan, $nama_pasien, $total_bayar, $id_user);
        
        if (!$stmt->execute()) {
            return false;
        }
        
        // 2. Insert Items
        $hasError = false;
        $insertedItems = array();
        
        foreach ($items as $item) {
            $stmt_item = $this->conn->prepare("INSERT INTO detail_kwitansi (no_kwitansi, id_barang, jumlah, subtotal) VALUES (?, ?, ?, ?)");
            $stmt_item->bind_param("siii", $no_kwitansi, $item['id_barang'], $item['jumlah'], $item['subtotal']);
            
            if ($stmt_item->execute()) {
                $insertedItems[] = $stmt_item->insert_id;
            } else {
                $hasError = true;
                break;
            }
        }
        
        // 3. Manual Rollback if Error
        if ($hasError) {
            // Rollback details
            foreach ($insertedItems as $id_detail) {
                $this->conn->query("DELETE FROM detail_kwitansi WHERE id_detail = " . intval($id_detail));
            }
            // Rollback header
            $stmt_del = $this->conn->prepare("DELETE FROM kwitansi WHERE no_kwitansi = ?");
            $stmt_del->bind_param("s", $no_kwitansi);
            $stmt_del->execute();
            
            return false;
        }
        
        return $no_kwitansi;
    }

    private function generateInvoiceNumber() {
        // Simple generation logic: INV-YYYYMMDD-Random
        return 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
    }
}

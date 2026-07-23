<?php
// models/KwitansiModel.php

class KwitansiModel {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function searchBarang($keyword) {
        $stmt = $this->db->prepare("SELECT * FROM barang WHERE nama_barang LIKE :keyword OR barcode LIKE :keyword LIMIT 10");
        $stmt->execute(array(':keyword' => '%' . $keyword . '%'));
        return $stmt->fetchAll();
    }

    public function simpanTransaksi($nama_pasien, $no_faktur, $untuk_pembayaran, $id_pelanggan, $items, $id_user) {
        $items_terpotong = array();
        $no_kwitansi = "";
        
        try {
            // 1. Table Locking (Pessimistic Locking untuk MyISAM)
            $this->db->exec("LOCK TABLES barang WRITE, kwitansi WRITE, detail_kwitansi WRITE, inventori WRITE");
            
            $total_bayar = 0;
            
            // 2. Pre-Check Stok
            foreach ($items as $item) {
                $id_barang = $item['id_barang'];
                $jumlah = $item['jumlah'];
                
                $stmt = $this->db->prepare("SELECT nama_barang, stok, harga FROM barang WHERE id_barang = :id");
                $stmt->execute(array(':id' => $id_barang));
                $row = $stmt->fetch();
                
                if (!$row) {
                    throw new Exception("Barang tidak ditemukan.");
                }
                if ($row['stok'] < $jumlah) {
                    throw new Exception("Stok barang '" . $row['nama_barang'] . "' habis. Sisa: " . $row['stok']);
                }
                $total_bayar += ($row['harga'] * $jumlah);
            }
            
            // Generate ID KW-YYYYMMDD-XXXX
            $date_prefix = "KW-" . date("Ymd") . "-";
            $stmt_id = $this->db->prepare("SELECT MAX(RIGHT(no_kwitansi, 4)) as last_id FROM kwitansi WHERE no_kwitansi LIKE :prefix");
            $stmt_id->execute(array(':prefix' => $date_prefix . '%'));
            $result_id = $stmt_id->fetch();
            
            $next_number = ($result_id && $result_id['last_id']) ? intval($result_id['last_id']) + 1 : 1;
            $no_kwitansi = $date_prefix . str_pad($next_number, 4, "0", STR_PAD_LEFT);
            
            // 3. Eksekusi
            $stmt_kwitansi = $this->db->prepare("INSERT INTO kwitansi (no_kwitansi, no_faktur, nama_pasien, id_pelanggan, total_bayar, untuk_pembayaran, id_user) VALUES (:no, :faktur, :nama, :id_pel, :total, :untuk, :id_user)");
            $stmt_kwitansi->execute(array(
                ':no' => $no_kwitansi,
                ':faktur' => $no_faktur,
                ':nama' => $nama_pasien,
                ':id_pel' => $id_pelanggan ? $id_pelanggan : null,
                ':total' => $total_bayar,
                ':untuk' => $untuk_pembayaran,
                ':id_user' => $id_user
            ));
            
            foreach ($items as $item) {
                $id_barang = $item['id_barang'];
                $jumlah = $item['jumlah'];
                $harga = $item['harga']; 
                $subtotal = $harga * $jumlah;
                
                $stmt_detail = $this->db->prepare("INSERT INTO detail_kwitansi (no_kwitansi, id_barang, jumlah, subtotal) VALUES (:no, :id, :jumlah, :subtotal)");
                $stmt_detail->execute(array(
                    ':no' => $no_kwitansi,
                    ':id' => $id_barang,
                    ':jumlah' => $jumlah,
                    ':subtotal' => $subtotal
                ));
                
                $stmt_update = $this->db->prepare("UPDATE barang SET stok = stok - :jumlah WHERE id_barang = :id");
                $stmt_update->execute(array(':jumlah' => $jumlah, ':id' => $id_barang));
                
                // OSPOS Feature: Track Inventory Log
                $stmt_inv = $this->db->prepare("INSERT INTO inventori (id_barang, id_user, jumlah_perubahan, keterangan) VALUES (:id_brg, :id_usr, :jml, :ket)");
                $stmt_inv->execute(array(
                    ':id_brg' => $id_barang,
                    ':id_usr' => $id_user,
                    ':jml' => -$jumlah,
                    ':ket' => 'Penjualan Nota ' . $no_kwitansi
                ));

                $items_terpotong[] = array('id_barang' => $id_barang, 'jumlah' => $jumlah);
            }
            
            $this->db->exec("UNLOCK TABLES");
            return array("status" => "success", "no_kwitansi" => $no_kwitansi);
            
        } catch (Exception $e) {
            // 4. Manual Rollback
            if (!empty($items_terpotong)) {
                $stmt_revert = $this->db->prepare("UPDATE barang SET stok = stok + :jumlah WHERE id_barang = :id");
                $stmt_del_inv = $this->db->prepare("DELETE FROM inventori WHERE id_barang = :id AND keterangan = :ket");
                
                foreach ($items_terpotong as $item_rollback) {
                    $stmt_revert->execute(array(':jumlah' => $item_rollback['jumlah'], ':id' => $item_rollback['id_barang']));
                    $stmt_del_inv->execute(array(':id' => $item_rollback['id_barang'], ':ket' => 'Penjualan Nota ' . $no_kwitansi));
                }
            }
            
            if ($no_kwitansi !== "") {
                $this->db->prepare("DELETE FROM detail_kwitansi WHERE no_kwitansi = ?")->execute(array($no_kwitansi));
                $this->db->prepare("DELETE FROM kwitansi WHERE no_kwitansi = ?")->execute(array($no_kwitansi));
            }

            $this->db->exec("UNLOCK TABLES");
            return array("status" => "error", "message" => $e->getMessage());
        }
    }
}
?>

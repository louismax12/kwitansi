<?php
// api_simpan_kwitansi.php
header('Content-Type: application/json');
require_once 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Membaca raw JSON input dari frontend
    $json = file_get_contents('php://input');
    $payload = json_decode($json, true);
    
    if (!$payload || !isset($payload['nama_pasien']) || !isset($payload['items'])) {
        echo json_encode(array("status" => "error", "message" => "Invalid payload"));
        exit;
    }
    
    $nama_pasien = $payload['nama_pasien'];
    $items = $payload['items'];
    $id_user = 2; // ID kasir_rkz
    
    // Track items yang sudah dipotong stoknya untuk keperluan compensating transaction
    $items_terpotong = array();
    $no_kwitansi = ""; // Akan diisi saat eksekusi
    
    try {
        // 1. Table Locking (Wajib)
        $pdo->exec("LOCK TABLES barang WRITE, kwitansi WRITE, detail_kwitansi WRITE");
        
        $total_bayar = 0;
        
        // 2. Pre-Check Stok
        foreach ($items as $item) {
            $id_barang = $item['id_barang'];
            $jumlah = $item['jumlah'];
            
            $stmt = $pdo->prepare("SELECT nama_barang, stok, harga FROM barang WHERE id_barang = :id");
            $stmt->execute(array(':id' => $id_barang));
            $row = $stmt->fetch();
            
            if (!$row) {
                throw new Exception("Barang dengan ID {$id_barang} tidak ditemukan.");
            }
            
            if ($row['stok'] < $jumlah) {
                throw new Exception("Stok barang '" . $row['nama_barang'] . "' tidak mencukupi. Stok sisa: " . $row['stok'] . ", diminta: " . $jumlah . ".");
            }
            
            $total_bayar += ($row['harga'] * $jumlah);
        }
        
        // GENERATOR ID KWITANSI: KW-YYYYMMDD-XXXX
        $date_prefix = "KW-" . date("Ymd") . "-";
        $stmt_id = $pdo->prepare("SELECT MAX(RIGHT(no_kwitansi, 4)) as last_id FROM kwitansi WHERE no_kwitansi LIKE :prefix");
        $stmt_id->execute(array(':prefix' => $date_prefix . '%'));
        $result_id = $stmt_id->fetch();
        
        $next_number = 1;
        if ($result_id && $result_id['last_id']) {
            $next_number = intval($result_id['last_id']) + 1;
        }
        $no_kwitansi = $date_prefix . str_pad($next_number, 4, "0", STR_PAD_LEFT);
        
        // 3. Eksekusi Insert & Update
        $stmt_kwitansi = $pdo->prepare("INSERT INTO kwitansi (no_kwitansi, nama_pasien, total_bayar, id_user) VALUES (:no, :nama, :total, :id_user)");
        $stmt_kwitansi->execute(array(
            ':no' => $no_kwitansi,
            ':nama' => $nama_pasien,
            ':total' => $total_bayar,
            ':id_user' => $id_user
        ));
        
        foreach ($items as $item) {
            $id_barang = $item['id_barang'];
            $jumlah = $item['jumlah'];
            $harga = $item['harga']; 
            $subtotal = $harga * $jumlah;
            
            $stmt_detail = $pdo->prepare("INSERT INTO detail_kwitansi (no_kwitansi, id_barang, jumlah, subtotal) VALUES (:no, :id, :jumlah, :subtotal)");
            $stmt_detail->execute(array(
                ':no' => $no_kwitansi,
                ':id' => $id_barang,
                ':jumlah' => $jumlah,
                ':subtotal' => $subtotal
            ));
            
            $stmt_update = $pdo->prepare("UPDATE barang SET stok = stok - :jumlah WHERE id_barang = :id");
            $stmt_update->execute(array(
                ':jumlah' => $jumlah,
                ':id' => $id_barang
            ));
            
            // Mencatat bahwa item ini berhasil dipotong stoknya, penting jika looping gagal di tengah jalan
            $items_terpotong[] = array('id_barang' => $id_barang, 'jumlah' => $jumlah);
        }
        
        // 5. Release Lock (Sukses)
        $pdo->exec("UNLOCK TABLES");
        
        echo json_encode(array(
            "status" => "success",
            "message" => "Transaksi berhasil disimpan.",
            "data" => array(
                "no_kwitansi" => $no_kwitansi
            )
        ));
        
    } catch (Exception $e) {
        // 4. Compensating Transaction (Manual Rollback)
        
        // Kembalikan stok untuk barang-barang yang terlanjur terpotong
        if (!empty($items_terpotong)) {
            $stmt_revert_stok = $pdo->prepare("UPDATE barang SET stok = stok + :jumlah WHERE id_barang = :id");
            foreach ($items_terpotong as $item_rollback) {
                $stmt_revert_stok->execute(array(
                    ':jumlah' => $item_rollback['jumlah'],
                    ':id' => $item_rollback['id_barang']
                ));
            }
        }
        
        // Hapus dari detail_kwitansi dan kwitansi jika sudah ter-insert
        if ($no_kwitansi !== "") {
            $stmt_delete_detail = $pdo->prepare("DELETE FROM detail_kwitansi WHERE no_kwitansi = :no");
            $stmt_delete_detail->execute(array(':no' => $no_kwitansi));
            
            $stmt_delete_kwitansi = $pdo->prepare("DELETE FROM kwitansi WHERE no_kwitansi = :no");
            $stmt_delete_kwitansi->execute(array(':no' => $no_kwitansi));
        }

        // 5. Release Lock (Gagal)
        $pdo->exec("UNLOCK TABLES");
        
        echo json_encode(array(
            "status" => "error",
            "message" => $e->getMessage()
        ));
    }
}
?>

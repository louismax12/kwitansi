<?php
require_once __DIR__ . '/config.php';

function connectDb()
{
    static $db = null;
    if ($db !== null) {
        return $db;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $options = array(
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    );

    try {
        $db = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (PDOException $e) {
        throw $e;
    }

    return $db;
}

function hashPassword($password)
{
    return sha1('rkz-hospital-salt:' . $password);
}

function getBarangList($db)
{
    try {
        $stmt = $db->prepare('SELECT id_barang, nama_barang, harga, stok FROM barang ORDER BY nama_barang ASC');
        $stmt->execute();
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return array();
    }
}

function buildReceiptNumber($db)
{
    $prefix = 'KW-' . date('Ymd') . '-';
    $stmt = $db->prepare('SELECT no_kwitansi FROM kwitansi WHERE no_kwitansi LIKE ? ORDER BY no_kwitansi DESC LIMIT 1');
    $stmt->execute(array($prefix . '%'));
    $row = $stmt->fetch();

    $lastNumber = 0;
    if ($row) {
        $lastText = substr($row['no_kwitansi'], strlen($prefix));
        $lastNumber = (int) $lastText;
    }

    $next = $lastNumber + 1;
    return $prefix . sprintf('%04d', $next);
}

function saveKwitansi($db, $data)
{
    $namaPasien = trim(isset($data['nama_pasien']) ? $data['nama_pasien'] : '');
    $tanggalTransaksi = trim(isset($data['tanggal_transaksi']) ? $data['tanggal_transaksi'] : '');
    $itemIds = isset($data['item_id']) ? $data['item_id'] : array();
    $quantities = isset($data['qty']) ? $data['qty'] : array();

    if ($namaPasien === '') {
        return array('status' => 'error', 'message' => 'Nama pasien wajib diisi.');
    }

    if (empty($itemIds)) {
        return array('status' => 'error', 'message' => 'Minimal satu item transaksi wajib dipilih.');
    }

    $items = array();
    $totalBayar = 0;

    foreach ($itemIds as $index => $itemId) {
        $itemId = (int) $itemId;
        $qty = (int) (isset($quantities[$index]) ? $quantities[$index] : 0);

        if ($itemId <= 0 || $qty <= 0) {
            continue;
        }

        $stmt = $db->prepare('SELECT id_barang, nama_barang, harga, stok FROM barang WHERE id_barang = ?');
        $stmt->execute(array($itemId));
        $barang = $stmt->fetch();

        if (!$barang) {
            return array('status' => 'error', 'message' => 'Barang tidak ditemukan.');
        }

        if ((int) $barang['stok'] < $qty) {
            return array('status' => 'error', 'message' => 'Stok ' . $barang['nama_barang'] . ' tidak mencukupi.');
        }

        $subtotal = (int) $barang['harga'] * $qty;
        $totalBayar += $subtotal;
        $items[] = array(
            'id_barang' => $itemId,
            'nama_barang' => $barang['nama_barang'],
            'harga' => (int) $barang['harga'],
            'qty' => $qty,
            'subtotal' => $subtotal
        );
    }

    if (empty($items)) {
        return array('status' => 'error', 'message' => 'Tidak ada item valid yang bisa disimpan.');
    }

    $noKwitansi = buildReceiptNumber($db);
    if ($tanggalTransaksi === '') {
        $tanggalTransaksi = date('Y-m-d H:i:s');
    }

    $username = isset($_SESSION['user']) ? $_SESSION['user'] : '';
    $stmtUser = $db->prepare('SELECT id_user FROM users WHERE username = ?');
    $stmtUser->execute(array($username));
    $userRow = $stmtUser->fetch();

    $idUser = $userRow ? (int) $userRow['id_user'] : 0;

    $stmt = $db->prepare('INSERT INTO kwitansi (no_kwitansi, tanggal_transaksi, nama_pasien, total_bayar, id_user) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute(array($noKwitansi, $tanggalTransaksi, $namaPasien, $totalBayar, $idUser));

    foreach ($items as $item) {
        $detailStmt = $db->prepare('INSERT INTO detail_kwitansi (no_kwitansi, id_barang, jumlah, subtotal) VALUES (?, ?, ?, ?)');
        $detailStmt->execute(array($noKwitansi, $item['id_barang'], $item['qty'], $item['subtotal']));

        $updateStmt = $db->prepare('UPDATE barang SET stok = stok - ? WHERE id_barang = ?');
        $updateStmt->execute(array($item['qty'], $item['id_barang']));
    }

    return array('status' => 'success', 'message' => 'Kwitansi berhasil disimpan.', 'no_kwitansi' => $noKwitansi);
}

function escapeHtml($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

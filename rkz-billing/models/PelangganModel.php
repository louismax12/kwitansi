<?php
// models/PelangganModel.php

class PelangganModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $result = $this->conn->query("SELECT * FROM kwitansi_pelanggan ORDER BY nama_pelanggan ASC");
        $data = array();
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
            }
        }
        return $data;
    }

    public function getById($id) {
        $id_safe = intval($id);
        $result = $this->conn->query("SELECT * FROM kwitansi_pelanggan WHERE id_pelanggan = $id_safe");
        return $result ? $result->fetch_assoc() : null;
    }
}

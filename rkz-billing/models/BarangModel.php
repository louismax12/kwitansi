<?php
// models/BarangModel.php

class BarangModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $result = $this->conn->query("SELECT * FROM barang ORDER BY nama_barang ASC");
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
        $result = $this->conn->query("SELECT * FROM barang WHERE id_barang = $id_safe");
        return $result ? $result->fetch_assoc() : null;
    }
}

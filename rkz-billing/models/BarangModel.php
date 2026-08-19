<?php
// models/BarangModel.php

class BarangModel {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $result = $this->conn->query("SELECT kd_brg AS id_barang, nm_brg AS nama_barang FROM kwitansi_brg ORDER BY nm_brg ASC");
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
        $result = $this->conn->query("SELECT kd_brg AS id_barang, nm_brg AS nama_barang FROM kwitansi_brg WHERE kd_brg = $id_safe");
        return $result ? $result->fetch_assoc() : null;
    }
}

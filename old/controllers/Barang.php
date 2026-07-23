<?php
// controllers/Barang.php
require_once 'models/Database.php';

class Barang {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index() {
        $stmt = $this->db->prepare("SELECT * FROM barang ORDER BY nama_barang ASC");
        $stmt->execute();
        $barang = $stmt->fetchAll();
        require_once 'views/barang/index.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_barang'];
            $barcode = $_POST['barcode'] !== '' ? $_POST['barcode'] : null;
            $nama = $_POST['nama_barang'];
            $kategori = $_POST['kategori'];
            $harga = $_POST['harga'];
            $stok = $_POST['stok'];

            if ($id) {
                // Update
                $stmt = $this->db->prepare("UPDATE barang SET barcode=:b, nama_barang=:n, kategori=:k, harga=:h, stok=:s WHERE id_barang=:id");
                $stmt->execute(array(':b'=>$barcode, ':n'=>$nama, ':k'=>$kategori, ':h'=>$harga, ':s'=>$stok, ':id'=>$id));
            } else {
                // Insert
                $stmt = $this->db->prepare("INSERT INTO barang (barcode, nama_barang, kategori, harga, stok) VALUES (:b, :n, :k, :h, :s)");
                $stmt->execute(array(':b'=>$barcode, ':n'=>$nama, ':k'=>$kategori, ':h'=>$harga, ':s'=>$stok));
            }
            header('Location: index.php?c=barang&a=index');
        }
    }
}
?>

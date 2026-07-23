<?php
// controllers/User.php
require_once 'models/Database.php';

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function index() {
        $stmt = $this->db->prepare("SELECT * FROM user ORDER BY id_user ASC");
        $stmt->execute();
        $users = $stmt->fetchAll();
        require_once 'views/user/index.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = $_POST['id_user'];
            $username = $_POST['username'];
            $nama = $_POST['nama_petugas'];
            $role = $_POST['role'];
            $password = $_POST['password'];

            if ($id) {
                if ($password !== '') {
                    $stmt = $this->db->prepare("UPDATE user SET username=:u, nama_petugas=:n, role=:r, password=:p WHERE id_user=:id");
                    $stmt->execute(array(':u'=>$username, ':n'=>$nama, ':r'=>$role, ':p'=>md5($password), ':id'=>$id));
                } else {
                    $stmt = $this->db->prepare("UPDATE user SET username=:u, nama_petugas=:n, role=:r WHERE id_user=:id");
                    $stmt->execute(array(':u'=>$username, ':n'=>$nama, ':r'=>$role, ':id'=>$id));
                }
            } else {
                $stmt = $this->db->prepare("INSERT INTO user (username, password, nama_petugas, role) VALUES (:u, :p, :n, :r)");
                $stmt->execute(array(':u'=>$username, ':p'=>md5($password), ':n'=>$nama, ':r'=>$role));
            }
            header('Location: index.php?c=user&a=index');
        }
    }
}
?>

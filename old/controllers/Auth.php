<?php
// controllers/Auth.php

class Auth {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'];
            $password = md5($_POST['password']); // Hashing MD5 sesuai standar legacy

            $stmt = $this->db->prepare("SELECT * FROM user WHERE username = :u AND password = :p");
            $stmt->execute(array(':u' => $username, ':p' => $password));
            $user = $stmt->fetch();

            if ($user) {
                $_SESSION['id_user'] = $user['id_user'];
                $_SESSION['nama_petugas'] = $user['nama_petugas'];
                $_SESSION['role'] = $user['role'];
                
                header('Location: index.php?c=dashboard&a=index');
                exit;
            } else {
                $error = "Username atau Password salah!";
                require_once 'views/auth/login.php';
                return;
            }
        }
        require_once 'views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?c=auth&a=login');
        exit;
    }
}
?>

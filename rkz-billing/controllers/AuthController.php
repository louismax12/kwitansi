<?php
// controllers/AuthController.php

class AuthController {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function login() {
        // If already logged in, redirect
        if (isset($_SESSION['username'])) {
            header('Location: index.php?c=dashboard');
            exit;
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $this->conn->real_escape_string($_POST['username']);
            $password = $_POST['password']; // In plain text

            // Query ke database HRD (tabel datadasar)
            // Asumsi: Username yang diinput adalah NIP
            $res = $this->conn->query("SELECT * FROM hrd.datadasar WHERE NIP = '$username'");
            
            if ($res && $res->num_rows > 0) {
                $user = $res->fetch_assoc();
                
                // Cek password. Kita cek encrypt_pass (MD5) atau password plaintext
                if ($user['encrypt_pass'] === md5($password) || $user['password'] === $password) {
                    $_SESSION['username'] = $user['Nama']; // Simpan nama asli sebagai nama user yang tampil
                    $_SESSION['nip'] = $user['NIP'];
                    
                    // Berikan akses penuh secara default karena tidak ada m1/m2 di hrd
                    $_SESSION['m1'] = 1;
                    $_SESSION['m2'] = 1;
                    
                    header('Location: index.php?c=dashboard');
                    exit;
                } else {
                    $error = 'Username atau Password salah.';
                }
            } else {
                $error = 'Username atau Password salah.';
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    public function logout() {
        session_destroy();
        header('Location: index.php?c=auth&a=login');
        exit;
    }
}

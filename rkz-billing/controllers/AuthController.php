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

            $res = $this->conn->query("SELECT * FROM kwitansi_user_profile WHERE username = '$username'");
            if ($res && $res->num_rows > 0) {
                $user = $res->fetch_assoc();
                if ($user['password'] === $password) {
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['m1'] = $user['m1'];
                    $_SESSION['m2'] = $user['m2'];
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

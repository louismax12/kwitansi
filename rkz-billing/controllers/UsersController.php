<?php
// controllers/UsersController.php
require_once __DIR__ . '/../models/UserModel.php';

class UsersController {
    private $conn;
    private $userModel;

    public function __construct($conn) {
        $this->conn = $conn;
        $this->userModel = new UserModel($conn);
        
        // Authorization check: Only Admin (m1 = 1) can access users management
        if (!isset($_SESSION['m1']) || $_SESSION['m1'] != 1) {
            die("Akses ditolak. Anda tidak memiliki izin untuk melihat halaman ini.");
        }
    }

    public function index() {
        $users = $this->userModel->getAllUsers();
        $content = __DIR__ . '/../views/users/index.php';
        require_once __DIR__ . '/../views/layout.php';
    }

    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'm1' => 0, // Default no admin access
                'm2' => 0  // Default no additional access
            ];
            
            // Check if username exists
            if ($this->userModel->getUserByUsername($data['username'])) {
                $error = "Username sudah ada.";
            } else {
                if ($this->userModel->createUser($data)) {
                    header('Location: index.php?c=users');
                    exit;
                } else {
                    $error = "Gagal menyimpan data user.";
                }
            }
        }
        
        $content = __DIR__ . '/../views/users/create.php';
        require_once __DIR__ . '/../views/layout.php';
    }

    public function edit() {
        $old_username = isset($_GET['id']) ? $_GET['id'] : '';
        $user = $this->userModel->getUserByUsername($old_username);
        
        if (!$user) {
            die("User tidak ditemukan.");
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'username' => $_POST['username'],
                'password' => $_POST['password'],
                'm1' => isset($_POST['m1']) ? 1 : 0,
                'm2' => isset($_POST['m2']) ? 1 : 0
            ];
            
            if ($data['username'] !== $old_username && $this->userModel->getUserByUsername($data['username'])) {
                $error = "Username sudah digunakan oleh user lain.";
            } else {
                if ($this->userModel->updateUser($old_username, $data)) {
                    // if logged-in user changed their own username, update session
                    if ($_SESSION['username'] === $old_username) {
                        $_SESSION['username'] = $data['username'];
                        $_SESSION['m1'] = $data['m1'];
                        $_SESSION['m2'] = $data['m2'];
                    }
                    header('Location: index.php?c=users');
                    exit;
                } else {
                    $error = "Gagal mengupdate data user.";
                }
            }
        }
        
        $content = __DIR__ . '/../views/users/edit.php';
        require_once __DIR__ . '/../views/layout.php';
    }

    public function delete() {
        $username = isset($_GET['id']) ? $_GET['id'] : '';
        if ($username === $_SESSION['username']) {
            die("Tidak dapat menghapus akun Anda sendiri yang sedang login.");
        }
        if ($this->userModel->deleteUser($username)) {
            header('Location: index.php?c=users');
            exit;
        } else {
            die("Gagal menghapus user.");
        }
    }
}

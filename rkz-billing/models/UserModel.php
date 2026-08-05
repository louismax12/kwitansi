<?php
// models/UserModel.php

class UserModel {
    private $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function getAllUsers() {
        $res = $this->conn->query("SELECT * FROM kwitansi_user_profile");
        $users = [];
        if ($res) {
            while ($row = $res->fetch_assoc()) {
                $users[] = $row;
            }
        }
        return $users;
    }

    public function getUserByUsername($username) {
        $username = $this->conn->real_escape_string($username);
        $res = $this->conn->query("SELECT * FROM kwitansi_user_profile WHERE username = '$username'");
        return $res ? $res->fetch_assoc() : null;
    }

    public function createUser($data) {
        $username = $this->conn->real_escape_string($data['username']);
        $password = $this->conn->real_escape_string($data['password']);
        $m1 = isset($data['m1']) ? intval($data['m1']) : 0;
        $m2 = isset($data['m2']) ? intval($data['m2']) : 0;
        
        $sql = "INSERT INTO kwitansi_user_profile (username, password, m1, m2) VALUES ('$username', '$password', $m1, $m2)";
        return $this->conn->query($sql);
    }

    public function updateUser($old_username, $data) {
        $old_username = $this->conn->real_escape_string($old_username);
        $username = $this->conn->real_escape_string($data['username']);
        $password = $this->conn->real_escape_string($data['password']);
        $m1 = isset($data['m1']) ? intval($data['m1']) : 0;
        $m2 = isset($data['m2']) ? intval($data['m2']) : 0;

        $sql = "UPDATE kwitansi_user_profile SET username = '$username', password = '$password', m1 = $m1, m2 = $m2 WHERE username = '$old_username'";
        return $this->conn->query($sql);
    }

    public function deleteUser($username) {
        $username = $this->conn->real_escape_string($username);
        return $this->conn->query("DELETE FROM kwitansi_user_profile WHERE username = '$username'");
    }
}

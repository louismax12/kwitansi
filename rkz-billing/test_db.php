<?php require "config/database.php"; $res=$conn->query("SELECT * FROM users LIMIT 1"); print_r($res->fetch_assoc());

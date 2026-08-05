<?php 
require "config/database.php"; 
$conn->query("ALTER TABLE kwitansi MODIFY id_user VARCHAR(30)");
echo "Modified id_user column";

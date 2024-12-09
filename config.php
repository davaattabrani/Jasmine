<?php
$host = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbname = "db_jasmine";

// Membuat koneksi
$conn = new mysqli($host, $dbUsername, $dbPassword, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Menghapus fungsi query yang duplikat untuk mengatasi error
// Fungsi query tidak didefinisikan ulang karena telah didefinisikan sebelumnya
?>

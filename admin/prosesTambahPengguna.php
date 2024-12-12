<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama_pengguna = $_POST['nama_pengguna'];
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // mengenkripsi password
    $jabatan = $_POST['jabatan'];

    // Cek koneksi database
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    // Tambahkan pengecekan apakah username sudah ada
    $check_query = "SELECT * FROM pengguna WHERE username = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>window.alert('Username sudah ada.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO pengguna (nama_pengguna, username, password, jabatan) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $nama_pengguna, $username, $password, $jabatan);
    
    // Debugging: Tampilkan data yang akan dimasukkan
    var_dump($nama_pengguna, $username, $password, $jabatan);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error; // Menampilkan kesalahan jika ada
    } else {
        if ($stmt->affected_rows > 0) {
            echo "Pengguna baru berhasil ditambahkan!";
            echo "<script>window.alert('Data berhasil ditambah.');</script>"; // Menampilkan alert
            echo "<script>window.location.href = 'pengguna.php';</script>"; // Redirect ke pengguna.php
            exit();
        } else {
            echo "Gagal menambahkan pengguna baru.";
        }
    }
} else {
    echo "Form tidak disubmit.";
}
?>

<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama_supplier = $_POST['nama_supplier'];

    // Cek koneksi database
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    // Tambahkan pengecekan apakah bulan dan tahun sudah ada
    $check_query = "SELECT * FROM supplier WHERE nama_supplier = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $nama_supplier);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>window.alert('Supplier ini sudah ada.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO supplier (nama_supplier) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nama_supplier);
    

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error; // Menampilkan kesalahan jika ada
    } else {
        if ($stmt->affected_rows > 0) {
            echo "Jenis baru berhasil ditambahkan!";
            echo "<script>window.alert('Data berhasil ditambah.');</script>"; // Menampilkan alert
            echo "<script>window.location.href = 'supplier.php';</script>"; // Redirect ke periode.php
            exit();
        } else {
            echo "Gagal menambahkan supplier baru.";
        }
    }
} else {
    echo "Form tidak disubmit.";
}
?>

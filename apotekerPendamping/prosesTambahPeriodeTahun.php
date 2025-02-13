<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config.php';

if (isset($_POST['submit'])) {
    $tahun = $_POST['tahun'];

    // Cek koneksi database
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    // Tambahkan pengecekan apakah bulan dan tahun sudah ada
    $check_query = "SELECT * FROM periode_tahun WHERE tahun = ? ";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $tahun);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>window.alert('Periode untuk tahun ini sudah ada.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO periode_tahun (tahun) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $tahun);
    
    // Debugging: Tampilkan data yang akan dimasukkan
    var_dump($tahun);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error; // Menampilkan kesalahan jika ada
    } else {
        if ($stmt->affected_rows > 0) {
            echo "Periode baru berhasil ditambahkan!";
            echo "<script>window.alert('Data berhasil ditambah.');</script>"; // Menampilkan alert
            echo "<script>window.location.href = 'periodeTahun.php';</script>"; // Redirect ke periode.php
            exit();
        } else {
            echo "Gagal menambahkan periode baru.";
        }
    }
} else {
    echo "Form tidak disubmit.";
}
?>

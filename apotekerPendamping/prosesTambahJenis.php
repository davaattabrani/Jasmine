<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama_jenis = $_POST['nama_jenis'];

    // Cek koneksi database
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    // Tambahkan pengecekan apakah bulan dan tahun sudah ada
    $check_query = "SELECT * FROM jenis WHERE nama_jenis = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $nama_jenis);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>window.alert('Jenis ini sudah ada.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO jenis (nama_jenis) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nama_jenis);
    
    // Debugging: Tampilkan data yang akan dimasukkan
    var_dump($nama_jenis);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error; // Menampilkan kesalahan jika ada
    } else {
        if ($stmt->affected_rows > 0) {
            echo "Jenis baru berhasil ditambahkan!";
            echo "<script>window.alert('Data berhasil ditambah.');</script>"; // Menampilkan alert
            echo "<script>window.location.href = 'jenis.php';</script>"; // Redirect ke periode.php
            exit();
        } else {
            echo "Gagal menambahkan jenis baru.";
        }
    }
} else {
    echo "Form tidak disubmit.";
}
?>

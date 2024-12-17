<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../config.php';

if (isset($_POST['submit'])) {
    $nama_satuan = $_POST['nama_satuan'];

    // Cek koneksi database
    if (!$conn) {
        die("Koneksi database gagal: " . mysqli_connect_error());
    }

    // Tambahkan pengecekan apakah bulan dan tahun sudah ada
    $check_query = "SELECT * FROM satuan WHERE nama_satuan = ?";
    $check_stmt = $conn->prepare($check_query);
    $check_stmt->bind_param("s", $nama_satuan);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>window.alert('Satuan ini sudah ada.'); window.history.back();</script>";
        exit();
    }

    $query = "INSERT INTO satuan (nama_satuan) VALUES (?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $nama_satuan);
    
    // Debugging: Tampilkan data yang akan dimasukkan
    var_dump($nama_satuan);

    if (!$stmt->execute()) {
        echo "Error: " . $stmt->error; // Menampilkan kesalahan jika ada
    } else {
        if ($stmt->affected_rows > 0) {
            echo "Jenis baru berhasil ditambahkan!";
            echo "<script>window.alert('Data berhasil ditambah.');</script>"; // Menampilkan alert
            echo "<script>window.location.href = 'satuan.php';</script>"; // Redirect ke periode.php
            exit();
        } else {
            echo "Gagal menambahkan satuan baru.";
        }
    }
} else {
    echo "Form tidak disubmit.";
}
?>

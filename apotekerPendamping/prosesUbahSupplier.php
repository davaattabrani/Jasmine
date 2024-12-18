<?php
require '../config.php';

// Cek koneksi
if (!$conn) {
    die("Koneksi ke basis data gagal: " . mysqli_connect_error());
}

// Cek apakah form di-submit
if (isset($_POST['submit'])) {
    // Debugging: Tampilkan data yang diterima
    var_dump($_POST);

    // Ambil data dari form
    $id_supplier = $_POST['id_supplier'];
    $nama_supplier = $_POST['nama_supplier'];

    // Sanitasi input
    $id_supplier = mysqli_real_escape_string($conn, $id_supplier);
    $nama_supplier = mysqli_real_escape_string($conn, $nama_supplier);

    // Cek apakah ID pengguna ada di database
    $result = mysqli_query($conn, "SELECT * FROM supplier WHERE id_supplier='$id_supplier'");
    if (mysqli_num_rows($result) == 0) {
        die("ID supplier tidak ditemukan.");
    }

    // Jika password tidak kosong, update password
    if (!empty($nama_supplier)) {
        $nama_supplier = mysqli_real_escape_string($conn, $nama_supplier);
        $query = "UPDATE supplier SET nama_supplier='$nama_supplier' WHERE id_supplier='$id_supplier'";
    } else {
        // Jika password kosong, jangan update password
        $query = "UPDATE supplier SET nama_supplier='$nama_supplier' WHERE id_supplier='$id_supplier'";
    }

    // Debugging: Tampilkan query yang akan dijalankan
    echo "Query: " . $query . "<br>";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Tambahkan alert sebelum pengalihan
        echo "<script>alert('Data supplier berhasil diubah!');</script>";
        // Tambahkan delay sebelum redirect
        echo "<script>window.location.href = 'supplier.php';</script>";
        exit();
    } else {
        // Tampilkan pesan error
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Tutup koneksi
mysqli_close($conn);
?> 
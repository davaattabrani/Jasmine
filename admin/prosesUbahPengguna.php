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
    $id_pengguna = $_POST['id_pengguna'];
    $nama_pengguna = $_POST['nama_pengguna'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $jabatan = $_POST['jabatan'];

    // Sanitasi input
    $id_pengguna = mysqli_real_escape_string($conn, $id_pengguna);
    $nama_pengguna = mysqli_real_escape_string($conn, $nama_pengguna);
    $username = mysqli_real_escape_string($conn, $username);
    $jabatan = mysqli_real_escape_string($conn, $jabatan);

    // Cek apakah ID pengguna ada di database
    $result = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna='$id_pengguna'");
    if (mysqli_num_rows($result) == 0) {
        die("ID pengguna tidak ditemukan.");
    }

    // Jika password tidak kosong, update password
    if (!empty($password)) {
        $password = mysqli_real_escape_string($conn, $password);
        $query = "UPDATE pengguna SET nama_pengguna='$nama_pengguna', username='$username', password='$password', jabatan='$jabatan' WHERE id_pengguna='$id_pengguna'";
    } else {
        // Jika password kosong, jangan update password
        $query = "UPDATE pengguna SET nama_pengguna='$nama_pengguna', username='$username', jabatan='$jabatan' WHERE id_pengguna='$id_pengguna'";
    }

    // Debugging: Tampilkan query yang akan dijalankan
    echo "Query: " . $query . "<br>";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Tambahkan alert sebelum pengalihan
        echo "<script>alert('Data pengguna berhasil diubah!');</script>";
        // Tambahkan delay sebelum redirect
        echo "<script>window.location.href = 'pengguna.php';</script>";
        exit();
    } else {
        // Tampilkan pesan error
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Tutup koneksi
mysqli_close($conn);
?> 
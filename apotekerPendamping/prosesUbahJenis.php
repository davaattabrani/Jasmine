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
    $id_jenis = $_POST['id_jenis'];
    $nama_jenis = $_POST['nama_jenis'];

    // Sanitasi input
    $id_jenis = mysqli_real_escape_string($conn, $id_jenis);
    $nama_jenis = mysqli_real_escape_string($conn, $nama_jenis);

    // Cek apakah ID pengguna ada di database
    $result = mysqli_query($conn, "SELECT * FROM jenis WHERE id_jenis='$id_jenis'");
    if (mysqli_num_rows($result) == 0) {
        die("ID jenis tidak ditemukan.");
    }

    // Jika password tidak kosong, update password
    if (!empty($nama_jenis)) {
        $nama_jenis = mysqli_real_escape_string($conn, $nama_jenis);
        $query = "UPDATE jenis SET nama_jenis='$nama_jenis' WHERE id_jenis='$id_jenis'";
    } else {
        // Jika password kosong, jangan update password
        $query = "UPDATE jenis SET nama_jenis='$nama_jenis' WHERE id_jenis='$id_jenis'";
    }

    // Debugging: Tampilkan query yang akan dijalankan
    echo "Query: " . $query . "<br>";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Tambahkan alert sebelum pengalihan
        echo "<script>alert('Data jenis berhasil diubah!');</script>";
        // Tambahkan delay sebelum redirect
        echo "<script>window.location.href = 'jenis.php';</script>";
        exit();
    } else {
        // Tampilkan pesan error
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Tutup koneksi
mysqli_close($conn);
?> 
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
    $id_satuan = $_POST['id_satuan'];
    $nama_satuan = $_POST['nama_satuan'];

    // Sanitasi input
    $id_satuan = mysqli_real_escape_string($conn, $id_satuan);
    $nama_satuan = mysqli_real_escape_string($conn, $nama_satuan);

    // Cek apakah ID pengguna ada di database
    $result = mysqli_query($conn, "SELECT * FROM satuan WHERE id_satuan='$id_satuan'");
    if (mysqli_num_rows($result) == 0) {
        die("ID satuan tidak ditemukan.");
    }

    // Jika password tidak kosong, update password
    if (!empty($nama_satuan)) {
        $nama_satuan = mysqli_real_escape_string($conn, $nama_satuan);
        $query = "UPDATE satuan SET nama_satuan='$nama_satuan' WHERE id_satuan='$id_satuan'";
    } else {
        // Jika password kosong, jangan update password
        $query = "UPDATE satuan SET nama_satuan='$nama_satuan' WHERE id_satuan='$id_satuan'";
    }

    // Debugging: Tampilkan query yang akan dijalankan
    echo "Query: " . $query . "<br>";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Tambahkan alert sebelum pengalihan
        echo "<script>alert('Data satuan berhasil diubah!');</script>";
        // Tambahkan delay sebelum redirect
        echo "<script>window.location.href = 'satuan.php';</script>";
        exit();
    } else {
        // Tampilkan pesan error
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Tutup koneksi
mysqli_close($conn);
?> 
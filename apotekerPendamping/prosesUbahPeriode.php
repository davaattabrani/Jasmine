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
    $id_periode = $_POST['id_periode'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];

    // Sanitasi input
    $id_periode = mysqli_real_escape_string($conn, $id_periode);
    $bulan = mysqli_real_escape_string($conn, $bulan);
    $tahun = mysqli_real_escape_string($conn, $tahun);

    // Cek apakah ID pengguna ada di database
    $result = mysqli_query($conn, "SELECT * FROM periode WHERE id_periode='$id_periode'");
    if (mysqli_num_rows($result) == 0) {
        die("ID periode tidak ditemukan.");
    }

    // Tambahkan kondisi pengecekan jika ada data dengan bulan dan tahun yang sama
    $checkQuery = "SELECT * FROM periode WHERE bulan='$bulan' AND tahun='$tahun' AND id_periode != '$id_periode'";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Data dengan bulan dan tahun yang sama sudah ada!');</script>";
        echo "<script>window.location.href = 'periode.php';</script>";
        exit();
    }

    // Hapus kondisi pengecekan bulan dan tahun jika kosong
    $query = "UPDATE periode SET bulan='$bulan', tahun='$tahun' WHERE id_periode='$id_periode'";

    // Debugging: Tampilkan query yang akan dijalankan
    echo "Query: " . $query . "<br>";

    // Eksekusi query
    if (mysqli_query($conn, $query)) {
        // Tambahkan alert sebelum pengalihan
        echo "<script>alert('Data periode berhasil diubah!');</script>";
        // Tambahkan delay sebelum redirect
        echo "<script>window.location.href = 'periode.php';</script>";
        exit();
    } else {
        // Tampilkan pesan error
        echo "Error updating record: " . mysqli_error($conn);
    }
}

// Tutup koneksi
mysqli_close($conn);
?> 
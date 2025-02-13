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
    $id_periode_bulan = $_POST['id_periode_bulan'];
    $bulan = $_POST['bulan'];

    // Sanitasi input
    $id_periode_bulan = mysqli_real_escape_string($conn, $id_periode_bulan);
    $bulan = mysqli_real_escape_string($conn, $bulan);

    // Cek apakah ID pengguna ada di database
    $result = mysqli_query($conn, "SELECT * FROM periode_bulan WHERE id_periode_bulan='$id_periode_bulan'");
    if (mysqli_num_rows($result) == 0) {
        die("ID periode tidak ditemukan.");
    }

    // Tambahkan kondisi pengecekan jika ada data dengan bulan dan tahun yang sama
    $checkQuery = "SELECT * FROM periode_bulan WHERE bulan='$bulan' AND id_periode_bulan != '$id_periode_bulan'";
    $checkResult = mysqli_query($conn, $checkQuery);
    if (mysqli_num_rows($checkResult) > 0) {
        echo "<script>alert('Data dengan bulan yang sama sudah ada!');</script>";
        echo "<script>window.location.href = 'periodeBulan.php';</script>";
        exit();
    }

    // Hapus kondisi pengecekan bulan dan tahun jika kosong
    $query = "UPDATE periode_bulan SET bulan='$bulan' WHERE id_periode_bulan='$id_periode_bulan'";

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
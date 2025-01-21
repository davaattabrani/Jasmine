<?php

// Include koneksi database
include('../config.php');

// Validasi input
$id_obat_masuk = $_POST['id_obat_masuk'] ?? null;
$id_obat = $_POST['id_obat'] ?? null; // Pastikan ini ada
$jumlah_masuk = $_POST['jumlah_masuk'] ?? null;

if ($id_obat_masuk === null || $id_obat === null || $jumlah_masuk === null) {
    die("Data tidak lengkap. Pastikan semua field terisi.");
}

// Query update data ke dalam database berdasarkan ID
$query = "UPDATE obat_masuk SET id_obat = '$id_obat', jumlah_masuk = '$jumlah_masuk' WHERE id_obat_masuk = '$id_obat_masuk'";

// Eksekusi query
if (mysqli_query($conn, $query)) {
    // Pesan berhasil update data
    echo "<script>alert('Data telah diubah!'); window.location.href='obatMasuk.php';</script>";
} else {
    // Pesan error gagal update data
    echo "Data Gagal Diupdate: " . mysqli_error($conn);
}

// Tutup koneksi
mysqli_close($conn);
?>
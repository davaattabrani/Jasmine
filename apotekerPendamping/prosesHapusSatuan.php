<?php
require '../config.php';

// Gantikan pemanggilan query() dengan mysqli_query()
$conn = mysqli_connect("localhost", "root", "", "db_jasmine");
if (!$conn) {
    die("Koneksi ke basis data gagal: " . mysqli_connect_error());
}

// Periksa apakah id_pengguna ada dalam parameter GET
if (isset($_GET['id_satuan'])) {
    $id_satuan = $_GET['id_satuan'];

    // Siapkan dan jalankan query untuk menghapus pengguna
    $query = "DELETE FROM satuan WHERE id_satuan = '$id_satuan'";
    if (mysqli_query($conn, $query)) {
        // Redirect atau tampilkan pesan sukses
        header("Location: satuan.php?message=Satuan berhasil dihapus");
        exit();
    } else {
        die("Error deleting record: " . mysqli_error($conn));
    }
} else {
    die("ID satuan tidak ditemukan.");
}

// Tutup koneksi
mysqli_close($conn);
?>
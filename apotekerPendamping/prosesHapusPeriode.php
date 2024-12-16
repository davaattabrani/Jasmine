<?php
require '../config.php';

// Gantikan pemanggilan query() dengan mysqli_query()
$conn = mysqli_connect("localhost", "root", "", "db_jasmine");
if (!$conn) {
    die("Koneksi ke basis data gagal: " . mysqli_connect_error());
}

// Periksa apakah id_pengguna ada dalam parameter GET
if (isset($_GET['id_periode'])) {
    $id_periode = $_GET['id_periode'];

    // Siapkan dan jalankan query untuk menghapus pengguna
    $query = "DELETE FROM periode WHERE id_periode = '$id_periode'";
    if (mysqli_query($conn, $query)) {
        // Redirect atau tampilkan pesan sukses
        header("Location: periode.php?message=Periode berhasil dihapus");
        exit();
    } else {
        die("Error deleting record: " . mysqli_error($conn));
    }
} else {
    die("ID periode tidak ditemukan.");
}

// Tutup koneksi
mysqli_close($conn);
?>
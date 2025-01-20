<?php
require_once '../config.php';

if (isset($_GET['id_obat_keluar'])) {
    $id_obat_keluar = $_GET['id_obat_keluar'];

    // Tambahkan pengecekan apakah $config sudah diinisialisasi sebelum digunakan
    if (isset($conn)) {
        $query = "DELETE FROM obat_keluar WHERE id_obat_keluar = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_obat);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Obat Keluar berhasil dihapus!";
            echo "<script>window.alert('Data berhasil dihapus');</script>"; // Menampilkan alert bahwa data berhasil dihapus
            header("Location: obatKeluar.php"); // Redirect to pengguna.php after successful deletion
            exit();
        } else {
            echo "Gagal menghapus Obat Keluar.";
        }
    } else {
        echo "Koneksi database gagal.";
    }
}
<?php
require_once '../config.php';

if (isset($_GET['id_obat_masuk'])) {
    $id_obat_masuk = $_GET['id_obat_masuk'];

    // Tambahkan pengecekan apakah $config sudah diinisialisasi sebelum digunakan
    if (isset($conn)) {
        $query = "DELETE FROM obat_masuk WHERE id_obat_masuk = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_obat);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Obat Masuk berhasil dihapus!";
            echo "<script>window.alert('Data berhasil dihapus');</script>"; // Menampilkan alert bahwa data berhasil dihapus
            header("Location: obatMasuk.php"); // Redirect to pengguna.php after successful deletion
            exit();
        } else {
            echo "Gagal menghapus Obat Masuk.";
        }
    } else {
        echo "Koneksi database gagal.";
    }
}
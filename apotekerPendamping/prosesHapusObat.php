<?php
require_once '../config.php';

if (isset($_GET['id_obat'])) {
    $id_obat = $_GET['id_obat'];

    // Tambahkan pengecekan apakah $config sudah diinisialisasi sebelum digunakan
    if (isset($conn)) {
        $query = "DELETE FROM obat WHERE id_obat = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $id_obat);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "Supplier berhasil dihapus!";
            echo "<script>window.alert('Data berhasil dihapus');</script>"; // Menampilkan alert bahwa data berhasil dihapus
            header("Location: obat.php"); // Redirect to pengguna.php after successful deletion
            exit();
        } else {
            echo "Gagal menghapus Supplier.";
        }
    } else {
        echo "Koneksi database gagal.";
    }
}
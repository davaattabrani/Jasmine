<?php

//include koneksi database
include('../config.php');

$id_obat_keluar = $_POST['id_obat_keluar'];
$id_obat = $_POST['id_obat'];
$jumlah_keluar = $_POST['jumlah_keluar'];
$kadaluarsa = $_POST['kadaluarsa'];

//query update data ke dalam database berdasarkan ID
$query = "UPDATE obat_keluar SET id_obat_keluar = '$id_obat_keluar', id_obat = '$id_obat', jumlah_keluar = '$jumlah_keluar', kadaluarsa = '$kadaluarsa' WHERE id_obat_keluar = '$id_obat_keluar'";

//kondisi pengecekan apakah data berhasil diupdate atau tidak
if($conn->query($query)) {
    //pesan berhasil update data
    echo "<script>alert('Data telah diubah!'); window.location.href='obatKeluar.php';</script>";
} else {
    //pesan error gagal update data
    echo "Data Gagal Diupate!";
}

?>
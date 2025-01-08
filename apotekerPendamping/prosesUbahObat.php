<?php

//include koneksi database
include('../config.php');

$id_obat = $_POST['id_obat'];
$nama_obat = $_POST['nama_obat'];
$id_jenis = $_POST['id_jenis'];
$id_satuan = $_POST['id_satuan'];
$id_supplier = $_POST['id_supplier'];

//query update data ke dalam database berdasarkan ID
$query = "UPDATE obat SET id_obat = '$id_obat', nama_obat = '$nama_obat', id_jenis = '$id_jenis', id_satuan = '$id_satuan', id_supplier = '$id_supplier' WHERE id_obat = '$id_obat'";

//kondisi pengecekan apakah data berhasil diupdate atau tidak
if($conn->query($query)) {
    //pesan berhasil update data
    echo "<script>alert('Data telah diubah!'); window.location.href='obat.php';</script>";
} else {
    //pesan error gagal update data
    echo "Data Gagal Diupate!";
}

?>
<?php
require '../config.php';


// Pastikan pengguna memiliki hak akses
include '../checkRole.php';
checkRole(['Apoteker Pendamping']);

// Ambil data dari form
$nama_obat = $_POST['nama_obat'] ?? null;
$jumlah_keluar = $_POST['jumlah_keluar'] ?? null;
$kadaluarsa = $_POST['kadaluarsa'] ?? null;
$id_periode_bulan = $_POST['id_periode_bulan'] ?? null;
$id_periode_tahun = $_POST['id_periode_tahun'] ?? null;

// Validasi input
if ($nama_obat && $jumlah_keluar && $kadaluarsa && $id_periode_bulan && $id_periode_tahun) {
    // Koneksi ke database
    $conn = mysqli_connect("localhost", "root", "", "db_jasmine");
    if (!$conn) {
        die("Koneksi ke basis data gagal: " . mysqli_connect_error());
    }

    // Siapkan query untuk menambahkan data
    $stmt = $conn->prepare("INSERT INTO obat_keluar (id_obat, jumlah_keluar, kadaluarsa, id_periode_bulan, id_periode_tahun) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $nama_obat, $jumlah_keluar, $kadaluarsa, $id_periode_bulan, $id_periode_tahun);

    // Eksekusi pernyataan
    if ($stmt->execute()) {
        echo "Obat baru berhasil ditambahkan!";
        echo "<script>window.alert('Data berhasil ditambah.'); window.location.href = 'obatKeluar.php';</script>"; // Menampilkan alert bahwa data berhasil ditambah dan redirect ke halaman pengguna.php
        exit();
    } else {
        echo "Gagal menambahkan obat baru.";
    }

} else {
    // Jika data tidak lengkap, redirect atau tampilkan pesan error
    echo "Gagal menambahkan obat baru.";
}
?> 
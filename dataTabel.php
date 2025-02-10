<?php
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "db_jasmine");

if (isset($_POST['import'])) {
    $file = $_FILES['file_excel']['tmp_name'];
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();
    $data = $sheet->toArray();

    foreach ($data as $index => $row) {
        if ($index == 0) continue; // Lewati baris header

        $nama_obat = $row[0];
        $nama_jenis = $row[1];
        $nama_satuan = $row[2];
        $nama_supplier = $row[3];

        // Cek atau masukkan ke tabel jenis
        $query = "SELECT id_jenis FROM jenis WHERE nama_jenis = '$nama_jenis'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $id_jenis = $result->fetch_assoc()['id_jenis'];
        } else {
            $conn->query("INSERT INTO jenis (nama_jenis) VALUES ('$nama_jenis')");
            $id_jenis = $conn->insert_id;
        }

        // Cek atau masukkan ke tabel satuan
        $query = "SELECT id_satuan FROM satuan WHERE nama_satuan = '$nama_satuan'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $id_satuan = $result->fetch_assoc()['id_satuan'];
        } else {
            $conn->query("INSERT INTO satuan (nama_satuan) VALUES ('$nama_satuan')");
            $id_satuan = $conn->insert_id;
        }

        // Cek atau masukkan ke tabel supplier
        $query = "SELECT id_supplier FROM supplier WHERE nama_supplier = '$nama_supplier'";
        $result = $conn->query($query);
        if ($result->num_rows > 0) {
            $id_supplier = $result->fetch_assoc()['id_supplier'];
        } else {
            $conn->query("INSERT INTO supplier (nama_supplier) VALUES ('$nama_supplier')");
            $id_supplier = $conn->insert_id;
        }

        // Masukkan data ke tabel obat
        $query = "INSERT INTO obat (nama_obat, id_jenis, id_satuan, id_supplier)
                  VALUES ('$nama_obat', '$id_jenis', '$id_satuan', '$id_supplier')";
        $conn->query($query);
    }

    echo "Data berhasil diimport!";
}
?>

<form method="POST" enctype="multipart/form-data">
    <input type="file" name="file_excel" required>
    <button type="submit" name="import">Import</button>
</form>

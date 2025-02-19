<?php
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

require '../config.php';
// ... (include file checkRole.php jika diperlukan)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $periode_bulan = $data['periode_bulan'];
    $action = $data['action'] ?? null;

    if ($action === 'hitung_simpan') {
        try {
            $conn->begin_transaction();

            // Query untuk data stok kurang dari atau sama dengan 5 berdasarkan periode bulan
            $query = "
            SELECT 
                o.id_obat,
                o.nama_obat, 
                s.stok AS jumlah_stok,
                s.id_supplier
            FROM stok s
            JOIN obat o ON s.id_obat = o.id_obat
            WHERE s.stok <= 5
            AND s.id_obat IN (
                SELECT id_obat 
                FROM obat_keluar 
                WHERE id_periode_bulan = ?
            )
            ";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $periode_bulan);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_all(MYSQLI_ASSOC);

            // Perhitungan peramalan untuk masing-masing obat
            foreach ($data as $index => $row) {
                $id_obat = $row['id_obat'];

                // Ambil data jumlah keluar untuk obat ini selama periode tertentu (untuk perhitungan forecast)
                $stokQuery = "
                SELECT jumlah_keluar 
                FROM obat_keluar 
                WHERE id_obat = ? 
                    AND id_periode_bulan <= ? 
                ORDER BY id_periode_bulan DESC
                ";
                $stokStmt = $conn->prepare($stokQuery);
                $stokStmt->bind_param("ii", $id_obat, $periode_bulan);
                $stokStmt->execute();
                $stokResult = $stokStmt->get_result();
                $stokValues = array_column($stokResult->fetch_all(MYSQLI_ASSOC), 'jumlah_keluar');

                // Inisialisasi variabel untuk mencari alpha terbaik
                $bestAlpha = null;
                $bestForecast = null;
                $minMAPE = PHP_INT_MAX;

                // Looping untuk mencoba berbagai nilai alpha (0.9 hingga 0.1)
                for ($alpha = 0.9; $alpha >= 0.1; $alpha -= 0.1) {
                    $forecast = singleExponentialSmoothing($stokValues, $alpha);
                    $mape = calculateMAPE($stokValues, $forecast);

                    if ($mape < $minMAPE) {
                        $minMAPE = $mape;
                        $bestAlpha = $alpha;
                        $bestForecast = $forecast;
                    }
                }
                // Validasi jika stokValues kosong
                if (empty($stokValues)) {
                    $data[$index]['forecast'] = 0;
                    $data[$index]['safety_stock'] = 0;
                    $data[$index]['total_pengadaan'] = max(0, 0 - $row['jumlah_stok']);
                    continue;
                }

                // Hitung nilai maksimum dan rata-rata dari stokValues
                $maxKeluar = max($stokValues);
                $avgKeluar = array_sum($stokValues) / count($stokValues);

                // Tambahkan validasi agar $maxKeluar tidak lebih kecil dari $avgKeluar
                if ($maxKeluar < $avgKeluar) {
                    $maxKeluar = $avgKeluar; // Pastikan tidak ada nilai negatif
                }

                // Rumus perhitungan safety stock
                $safetyStock = ($maxKeluar - $avgKeluar) * 3;

                // Pastikan safety stock tidak negatif
                if ($safetyStock < 0) {
                    $safetyStock = 0; // Berikan nilai default 0 jika hasil negatif
                }

                // Masukkan hasil safety stock ke dalam data
                $data[$index]['safety_stock'] = $safetyStock;

                // Forecast dan perhitungan lainnya
                $forecast = singleExponentialSmoothing($stokValues, $alpha);
                $data[$index]['forecast'] = end($forecast); // Forecast terakhir
                $data[$index]['total_pengadaan'] = max(0, $data[$index]['forecast'] + $safetyStock - $row['jumlah_stok']);

                // Cek apakah sudah ada data di periode ini
                $cekPeriode = "
                SELECT id_periode_tahun FROM penentuan 
                WHERE id_obat = ? AND id_periode_bulan = ?
                ORDER BY id_periode_tahun DESC LIMIT 1
                ";
                $stmtCekPeriode = $conn->prepare($cekPeriode);
                $stmtCekPeriode->bind_param("ii", $id_obat, $periode_bulan);
                $stmtCekPeriode->execute();
                $resultCekPeriode = $stmtCekPeriode->get_result();

                if ($resultCekPeriode->num_rows > 0) {
                    $rowPeriode = $resultCekPeriode->fetch_assoc();
                    $id_periode_tahun = $rowPeriode['id_periode_tahun'] + 1; // Tambah 1 tahun jika data sudah ada
                } else {
                    // Jika belum ada, gunakan tahun yang dikirim dari form
                    $id_periode_tahun = date("Y"); // Tahun sekarang
                }

                // Simpan data ke tabel penentuan
                $queryPenentuan = "
                INSERT INTO penentuan (id_obat, id_periode_bulan, id_periode_tahun, hasil_peramalan, safety_stock)
                VALUES (?, ?, ?, ?, ?)
                ";
                $stmtPenentuan = $conn->prepare($queryPenentuan);
                $stmtPenentuan->bind_param("iiiid", $id_obat, $periode_bulan, $id_periode_tahun, $hasil_peramalan, $safety_stock);
                $stmtPenentuan->execute();

                // Simpan data ke tabel obat_masuk
                $queryObatMasuk = "
                INSERT INTO obat_masuk (id_obat, id_periode_bulan, id_periode_tahun, jumlah_masuk)
                VALUES (?, ?, ?, ?)
                ";
                $stmtObatMasuk = $conn->prepare($queryObatMasuk);
                $stmtObatMasuk->bind_param("iiii", $id_obat, $periode_bulan, $id_periode_tahun, $total_pengadaan);
                $stmtObatMasuk->execute();
            }

            $conn->commit();

            // Ambil data obat dari database untuk ditampilkan di tabel
            $queryTabel = "SELECT o.id_obat, o.nama_obat, p.hasil_peramalan, om.jumlah_masuk AS total_pengadaan, s.nama_supplier FROM obat o JOIN penentuan p ON o.id_obat = p.id_obat JOIN obat_masuk om ON o.id_obat = om.id_obat JOIN supplier s ON o.id_supplier = s.id_supplier WHERE p.id_periode_bulan = ? ORDER BY o.nama_obat"; // Sesuaikan query
            $stmtTabel = $conn->prepare($queryTabel);
            $stmtTabel->bind_param("i", $periode_bulan);
            $stmtTabel->execute();
            $resultTabel = $stmtTabel->get_result();
            $dataTabel = $resultTabel->fetch_all(MYSQLI_ASSOC);

            echo json_encode(['success' => true, 'data' => $dataTabel]);
        } catch (Exception $e) {
            $conn->rollBack();
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    } else {
        // ... (logika awal Anda)
    }
}
ob_end_flush();
?>
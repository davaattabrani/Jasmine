<!DOCTYPE html>
<?php
require '../config.php';
include '../checkRole.php';
checkRole(['Penanggung Jawab Farmasi']);

// Ambil ID pengguna dari sesi atau sumber lain
$id_pengguna = $_SESSION['id_pengguna'] ?? null; // Pastikan Anda menyimpan ID pengguna saat login

if ($id_pengguna) {
    if (isset($conn)) {
        $stmt = $conn->prepare("SELECT nama_pengguna, jabatan FROM pengguna WHERE id_pengguna = ?");
        $stmt->bind_param("i", $id_pengguna);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $nama_pengguna = $user['nama_pengguna'] ?? 'Guest';
        $jabatan = $user['jabatan'] ?? 'Guest';
    } else {
        $nama_pengguna = 'Guest';
    }
} else {
    $nama_pengguna = 'Guest';
}

// Query untuk tabel jenis
$queryJenis = "SELECT id_jenis, nama_jenis FROM jenis ORDER BY nama_jenis";
$resJenis = mysqli_query($conn, $queryJenis);
if (!$resJenis) {
    die("Query failed: " . mysqli_error($conn));
}

// Query untuk tabel satuan
$querySatuan = "SELECT id_satuan, nama_satuan FROM satuan ORDER BY nama_satuan";
$resSatuan = mysqli_query($conn, $querySatuan);
if (!$resSatuan) {
    die("Query failed: " . mysqli_error($conn));
}

// Query untuk tabel supplier
$querySupplier = "SELECT id_supplier, nama_supplier FROM supplier ORDER BY nama_supplier";
$resSupplier = mysqli_query($conn, $querySupplier);
if (!$resSupplier) {
    die("Query failed: " . mysqli_error($conn));
}

// Ambil daftar bulan
$bulanQuery = "SELECT id_periode_bulan, bulan FROM periode_bulan";
$bulanResult = $conn->query($bulanQuery);

// Ambil daftar bulan
$tahunQuery = "SELECT id_periode_tahun, tahun FROM periode_tahun";
$tahunResult = $conn->query($tahunQuery);

// Fungsi untuk menghitung MAPE
function calculateMAPE($actual, $forecast) {
    $n = count($actual);
    $errorSum = 0;

    for ($i = 0; $i < $n; $i++) {
        if ($actual[$i] != 0) { // Hindari pembagian dengan nol
            $errorSum += abs(($actual[$i] - $forecast[$i]) / $actual[$i]);
        }
    }

    return ($n > 0) ? ($errorSum / $n) * 100 : null; // Mengembalikan nilai persentase
}

// Fungsi untuk perhitungan Single Exponential Smoothing
function singleExponentialSmoothing($data, $alpha) {
    $forecast = [];
    $n = count($data);

    if ($n == 0) {
        return $forecast; // Kembalikan array kosong jika data kosong
    }

    // Inisialisasi: Forecast pertama sama dengan data aktual pertama
    $forecast[0] = round($data[0]); // Dibulatkan ke bilangan bulat

    // Hitung forecast untuk data berikutnya
    for ($t = 1; $t < $n; $t++) {
        $forecast[$t] = round($alpha * $data[$t - 1] + (1 - $alpha) * $forecast[$t - 1]);
    }

    return $forecast;
}

// Default: Tampilkan stok obat kurang dari atau sama dengan 5
if (!isset($_POST['hitung'])) {

    $query = "
    SELECT 
        o.id_obat,
        o.nama_obat, 
        s.stok AS jumlah_stok
    FROM stok s
    JOIN obat o ON s.id_obat = o.id_obat
    WHERE s.stok <= 5
    ";
    $result = $conn->query($query);
    $data = $result->fetch_all(MYSQLI_ASSOC);
} else {
    // Jika tombol "Hitung" ditekan
    $periode_bulan = $_POST['id_periode_bulan'];
    $periode_tahun = $_POST['id_periode_tahun'];

    // Validasi input
    if (!is_numeric($periode_bulan)) {
        die("Periode bulan tidak valid!");
    }

    // Query untuk data stok kurang dari atau sama dengan 5 berdasarkan periode bulan
    $query = "
    SELECT 
        o.id_obat,
        o.nama_obat, 
        s.stok AS jumlah_stok
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

                }
                echo "<form method='post' action=''>";
                echo "<input type='hidden' name='periode_bulan' value='" . $periode_bulan . "'>"; // Simpan periode bulan
                echo "<input type='hidden' name='periode_tahun' value='" . $periode_tahun . "'>"; // Simpan periode bulan
                
            
                foreach ($data as $row) {
                    echo "<input type='hidden' name='data[" . $row['id_obat'] . "][id_obat]' value='" . $row['id_obat'] . "'>";
                    echo "<input type='hidden' name='data[" . $row['id_obat'] . "][nama_obat]' value='" . $row['nama_obat'] . "'>";
                    echo "<input type='hidden' name='data[" . $row['id_obat'] . "][jumlah_stok]' value='" . $row['jumlah_stok'] . "'>";
                    echo "<input type='hidden' name='data[" . $row['id_obat'] . "][forecast]' value='" . $row['forecast'] . "'>";
                    echo "<input type='hidden' name='data[" . $row['id_obat'] . "][safety_stock]' value='" . $row['safety_stock'] . "'>";
                    echo "<input type='hidden' name='data[" . $row['id_obat'] . "][total_pengadaan]' value='" . $row['total_pengadaan'] . "'>";
            
                    // Tambahkan input hidden untuk supplier (jika ada)
                    if (isset($_POST['hitung'])) {
                        $supplierQuery = "SELECT s.id_supplier, s.nama_supplier 
                                        FROM supplier s
                                        JOIN obat o ON s.id_supplier = o.id_supplier
                                        WHERE o.id_obat = ?
                                    ";
                        $supplierStmt = $conn->prepare($supplierQuery);
                        $supplierStmt->bind_param("i", $row['id_obat']);
                        $supplierStmt->execute();
                        $supplierResult = $supplierStmt->get_result();
            
                        while ($supplier = $supplierResult->fetch_assoc()) { ?>
                            <input type='hidden' name='data[" . $row['id_obat'] . "][id_supplier]' value='" . $supplier['id_supplier'] . "'>";
                        <?php } 
                    }
                }    
}

if (isset($_POST['simpan'])) {
    if (!isset($_POST['data']) || empty($_POST['data'])) {
        die("Data tidak ditemukan.");
    }

    $periode_bulan = $_POST['periode_bulan'];
    $periode_tahun = $_POST['periode_tahun'];

    foreach ($_POST['data'] as $id_obat => $row) {
        $hasil_peramalan = $row['forecast'];
        $safety_stock = $row['safety_stock'];
        $total_pengadaan = $row['total_pengadaan'];

        // Validasi data sebelum menyimpan
        if (!is_numeric($hasil_peramalan) || !is_numeric($safety_stock) || !is_numeric($total_pengadaan)) {
            die("Data tidak valid untuk obat dengan ID: $id_obat");
        }

        // Cek apakah data untuk periode ini sudah ada
        $cekPeriode = "
            SELECT id_penentuan 
            FROM penentuan 
            WHERE id_obat = ? 
              AND id_periode_bulan = ? 
              AND id_periode_tahun = ?
        ";
        $stmt = $conn->prepare($cekPeriode);
        $stmt->bind_param("iii", $id_obat, $periode_bulan, $periode_tahun);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            continue; // Jika sudah ada, lewati
        }

        // Simpan ke tabel `penentuan`
        $queryPenentuan = "
            INSERT INTO penentuan (id_obat, id_periode_bulan, id_periode_tahun, hasil_peramalan, safety_stock)
            VALUES (?, ?, ?, ?, ?)
        ";
        $stmtPenentuan = $conn->prepare($queryPenentuan);
        $stmtPenentuan->bind_param("iiiid", $id_obat, $periode_bulan, $periode_tahun, $hasil_peramalan, $safety_stock);
        $stmtPenentuan->execute();

        // Simpan ke tabel `obat_masuk`
        $queryObatMasuk = "
            INSERT INTO obat_masuk (id_obat, id_periode_bulan, id_periode_tahun, jumlah_masuk)
            VALUES (?, ?, ?, ?)
        ";
        $stmtObatMasuk = $conn->prepare($queryObatMasuk);
        $stmtObatMasuk->bind_param("iiii", $id_obat, $periode_bulan, $periode_tahun, $total_pengadaan);
        $stmtObatMasuk->execute();
    }

    echo "<script>alert('Data berhasil disimpan!'); window.location.href='obatMasuk.php';</script>";
}


?>





<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free">
  <head>
  <?php include('head.php'); ?>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->
        <?php include('sideBar.php');?>
        <!-- / Menu -->

        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
           <?php include('navBar.php');?>
          <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->

            <div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Beranda /</span> Pengadaan
    </h4>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Data Stok</h5>
        </div>
        <div class="card-body">
            <form method="post" action="">
                <div class="form-group row">
                    <label for="bulan" class="col-sm-2 col-form-label">Peramalan Untuk Bulan :</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="bulan" name="id_periode_bulan">
                            <?php if (isset($bulanResult)) { ?>
                                <?php while ($bulan = $bulanResult->fetch_assoc()) { ?>
                                    <option value="<?= htmlspecialchars($bulan['id_periode_bulan']) ?>">
                                        <?= htmlspecialchars($bulan['bulan']) ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <label for="tahun" class="col-sm-2 col-form-label">Peramalan Untuk Tahun :</label>
                    <div class="col-sm-2">
                        <select class="form-control" id="tahun" name="id_periode_tahun">
                            <?php if (isset($tahunResult)) { ?>
                                <?php while ($tahun = $tahunResult->fetch_assoc()) { ?>
                                    <option value="<?= htmlspecialchars($tahun['id_periode_tahun']) ?>">
                                        <?= htmlspecialchars($tahun['tahun']) ?>
                                    </option>
                                <?php } ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <button type="submit" name="hitung" class="btn btn-success">Hitung</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Tabel -->
        <div class="table-responsive text-nowrap">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>
                            <?= !isset($_POST['hitung']) ? 'Stok' : 'Hasil Peramalan' ?>
                        </th>
                        <?php if (isset($_POST['hitung'])) { ?>
                            <th>Total Pengadaan</th>
                            <th>Supplier</th>
                        <?php } ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($data)) { ?>
                        <?php $i = 1; ?>
                        <?php foreach ($data as $row) { ?>
                            <tr>
                                <td><strong><?= htmlspecialchars($i++) ?></strong></td>
                                <td data-id="<?= htmlspecialchars($row['id_obat']) ?>">
                                    <?= htmlspecialchars($row['nama_obat']) ?>
                                </td>
                                <td>
                                    <?= htmlspecialchars(isset($_POST['hitung']) ? $row['forecast'] : $row['jumlah_stok']) ?>
                                </td>
                                <?php if (isset($_POST['hitung'])) { ?>
                                    <td><?= htmlspecialchars($row['total_pengadaan']) ?></td>
                                    <td>
                                        <select class="form-control" name="supplier_obat[<?= htmlspecialchars($row['id_obat']) ?>]">
                                            <?php 
                                            // Query supplier berdasarkan id_obat
                                            $supplierQuery = "
                                                SELECT s.id_supplier, s.nama_supplier 
                                                FROM supplier s
                                                JOIN obat o ON s.id_supplier = o.id_supplier
                                                WHERE o.id_obat = ?
                                            ";
                                            $supplierStmt = $conn->prepare($supplierQuery);
                                            $supplierStmt->bind_param("i", $row['id_obat']);
                                            $supplierStmt->execute();
                                            $supplierResult = $supplierStmt->get_result();

                                            while ($supplier = $supplierResult->fetch_assoc()) { ?>
                                                <option value="<?= htmlspecialchars($supplier['id_supplier']) ?>">
                                                    <?= htmlspecialchars($supplier['nama_supplier']) ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td colspan="<?= isset($_POST['hitung']) ? 5 : 3 ?>" class="text-center">Tidak ada data</td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <!-- Tombol Tambah Obat Masuk -->
        <?php if (!empty($data)) { ?>
            <div class="card-body">
                <button type="submit" name="simpan" class="btn btn-primary me-2">Tambah Obat Masuk</button>
            </div>
        <?php } ?>
    </div>
</div>

            <!-- / Content -->

            <!-- Footer -->
            <?php include('footer.php'); ?>
            <!-- / Footer -->

            <div class="content-backdrop fade"></div>
          </div>
          <!-- Content wrapper -->
        </div>
        <!-- / Layout page -->
      </div>

      <!-- Overlay -->
      <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

</script>
  </body>
</html>

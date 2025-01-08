<!DOCTYPE html>
<?php
require '../config.php';
include '../checkRole.php';
checkRole(['Penanggung Jawab Farmasi']);

// Ambil ID pengguna dari sesi atau sumber lain
$id_pengguna = $_SESSION['id_pengguna'] ?? null; // Pastikan Anda menyimpan ID pengguna saat login

if ($id_pengguna) {
    // Pastikan $conn terdefinisi
    if (isset($conn)) {
        // Query untuk mendapatkan nama pengguna berdasarkan ID
        $stmt = $conn->prepare("SELECT nama_pengguna, jabatan FROM pengguna WHERE id_pengguna = ?");
        $stmt->bind_param("i", $id_pengguna); // Mengikat parameter dengan tipe integer
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        // Pastikan nama_pengguna ada
        $nama_pengguna = $user['nama_pengguna'] ?? 'Guest';
        $jabatan = $user['jabatan'] ?? 'Guest';
    } else {
        $nama_pengguna = 'Guest'; // Jika koneksi gagal, gunakan nilai default
    }
} else {
    $nama_pengguna = 'Guest'; // Jika tidak ada ID pengguna, gunakan nilai default
}

// Gantikan pemanggilan query() dengan mysqli_query()
$conn = mysqli_connect("localhost", "root", "", "db_jasmine");
if (!$conn) {
    die("Koneksi ke basis data gagal: " . mysqli_connect_error());
}

$obat_result = mysqli_query($conn, "SELECT o.*, jen.nama_jenis, sat.nama_satuan, sup.nama_supplier 
                                        FROM obat o
                                        JOIN jenis jen ON o.id_jenis = jen.id_jenis
                                        JOIN satuan sat ON o.id_satuan = sat.id_satuan
                                        JOIN supplier sup ON o.id_supplier = sup.id_supplier");

// Periksa apakah kueri berhasil sebelum melanjutkan
if (!$obat_result) {
    die("Query error: " . mysqli_error($conn));
}

if ($obat_result) {
    $obat = mysqli_fetch_all($obat_result, MYSQLI_ASSOC);
}

// Tambahkan logika untuk mendapatkan data pengguna berdasarkan ID
$row = []; // Inisialisasi $row sebagai array kosong
if (isset($_GET['id_obat'])) {
    $id_obat = $_GET['id_obat'];
    $query = mysqli_query($conn, "SELECT o.*, jen.nama_jenis, sat.nama_satuan, sup.nama_supplier 
                                    FROM obat o
                                    JOIN jenis jen ON o.id_jenis = jen.id_jenis
                                    JOIN satuan sat ON o.id_satuan = sat.id_satuan
                                    JOIN supplier sup ON o.id_supplier = sup.id_supplier
                                    WHERE o.id_obat = '$id_obat'");
    
    // Periksa apakah query berhasil dan ada hasil
    if ($query) {
        $row = mysqli_fetch_assoc($query);
        // Cek apakah data ditemukan
        if (!$row) {
            die("Data tidak ditemukan untuk ID obat: " . htmlspecialchars($id_obat));
        }
    } else {
        die("Query error: " . mysqli_error($conn));
    }
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

$datajenis = "SELECT * FROM jenis";
$resjenis = mysqli_query($conn, $datajenis);
$datasatuan = "SELECT * FROM satuan";
$ressatuan = mysqli_query($conn, $datasatuan);
$datasupplier = "SELECT * FROM supplier";
$ressupplier = mysqli_query($conn, $datasupplier);

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
                    <form method="post" action="generate.php">
                        <div class="form-group row">
                            <label for="tahun" class="col-sm-2 col-form-label">Ambil Data Dari Tahun:</label>
                            <div class="col-sm-2">
                                <select class="form-control" id="tahun" name="tahun">
                                    <?php
                                    for ($i = date('Y'); $i >= 2022; $i--) {
                                        echo "<option value='$i'>$i</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="bulan" class="col-sm-2 col-form-label">Ambil Data Dari Bulan:</label>
                            <div class="col-sm-2">
                                <select class="form-control" id="bulan" name="bulan">
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $bulan = date('F', mktime(0, 0, 0, $i, 10));
                                        echo "<option value='$i'>$bulan</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <label for="bulan" class="col-sm-2 col-form-label">Peramalan Untuk Bulan :</label>
                            <div class="col-sm-2">
                            <select class="form-control" id="bulan" name="bulan">
                                    <?php
                                    for ($i = 1; $i <= 12; $i++) {
                                        $bulan = date('F', mktime(0, 0, 0, $i, 10));
                                        echo "<option value='$i'>$bulan</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="submit" class="btn btn-success">Hitung</button>
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
                        <th>Jenis</th>
                        <th>Satuan</th>
                        <th>Sisa Stok</th>
                        <th>Hasil Peramalan</th>
                        <th>Safety Stock</th>
                        <th>Total Pengadaan</th>
                        <th>Supplier</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php $i = 1; ?>
                      <?php foreach ($obat as $row) { ?>
                    <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($i++); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_obat']);?></td>
                            <td><?php echo htmlspecialchars($row['nama_jenis']);?></td>
                            <td><?php echo htmlspecialchars($row['nama_satuan']);?></td>
                            <td>5</td>
                            <td>34</td>
                            <td>5</td>
                            <td>44</td>
                            <td>
                            <select class="form-control" id="bulan" name="bulan">
                                <option value="PT Jaya Farmasi">PT Jaya Farmasi</option>
                                <option value="PT Ramdan Cahaya">PT Ramdan Cahaya</option>
                                <option value="CV Kimia Farma">CV Kimia Farma</option>
                            </select>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                  </table>
                </div>
                <!-- Tambahkan tombol di bawah tabel -->
                <div class="card-body">
                        <button class="btn btn-primary me-2" onclick="window.print()">Cetak</button>
                        <a href="tambahObat.php" class="btn btn-primary">Tambah Obat Masuk</a>
                </div>
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

    <script>
        // Menangani event saat modal dibuka
        $('#modalUbahObat').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Tombol yang memicu modal
    var id = button.data('id'); // Ambil data-id
    var nama = button.data('nama'); // Ambil data-nama
    var jenis = button.data('jenis'); // Ambil data-jenis
    var satuan = button.data('satuan'); // Ambil data-satuan
    var supplier = button.data('supplier'); // Ambil data-supplier

    var modal = $(this);
    modal.find('input[name="id_obat"]').val(id); // Isi ID
    modal.find('input[name="nama_obat"]').val(nama); // Isi Nama

    // Isi select Jenis
    modal.find('select[name="id_jenis"] option').each(function () {
        if ($(this).val() == jenis) {
            $(this).prop('selected', true); // Setel sebagai selected
        }
    });

    // Isi select Satuan
    modal.find('select[name="id_satuan"] option').each(function () {
        if ($(this).val() == satuan) {
            $(this).prop('selected', true); // Setel sebagai selected
        }
    });

    // Isi select Supplier
    modal.find('select[name="id_supplier"] option').each(function () {
        if ($(this).val() == supplier) {
            $(this).prop('selected', true); // Setel sebagai selected
        }
    });
});

    </script>

    <!-- Script untuk konfirmasi sebelum menghapus data -->
    <script>
        function konfirmasiHapus(id) {
            if (confirm('Yakin Ingin Menghapus Data?')) {
                window.location.href = 'prosesHapusSupplier.php?id_supplier=' + id;
            }
        }
    </script>

  </body>
</html>

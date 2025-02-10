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

$obat_keluar_result = mysqli_query($conn, "SELECT 
    o.nama_obat, 
    j.nama_jenis AS jenis, 
    s.nama_satuan AS satuan, 
    sp.nama_supplier,
    ok.kadaluarsa,
    CONCAT(pb.bulan, ' ', pt.tahun) AS periode
FROM obat_keluar ok
JOIN obat o ON ok.id_obat = o.id_obat
JOIN jenis j ON o.id_jenis = j.id_jenis
JOIN satuan s ON o.id_satuan = s.id_satuan
JOIN supplier sp ON o.id_supplier = sp.id_supplier
JOIN periode_bulan pb ON ok.id_periode_bulan = pb.id_periode_bulan
JOIN periode_tahun pt ON ok.id_periode_tahun = pt.id_periode_tahun
WHERE (pt.tahun, pb.id_periode_bulan) = (
    SELECT MAX(pt2.tahun), MAX(pb2.id_periode_bulan)
    FROM obat_keluar ok2
    JOIN periode_bulan pb2 ON ok2.id_periode_bulan = pb2.id_periode_bulan
    JOIN periode_tahun pt2 ON ok2.id_periode_tahun = pt2.id_periode_tahun
)
AND ok.kadaluarsa > 0;
");



if (!$obat_keluar_result) {
    die("Query error: " . mysqli_error($conn));
}


if ($obat_keluar_result) {
    $obat_keluar = mysqli_fetch_all($obat_keluar_result, MYSQLI_ASSOC);
}

 // Query untuk tabel obat
 $queryObat = "SELECT id_obat, nama_obat FROM obat ORDER BY nama_obat";
 $resObat = mysqli_query($conn, $queryObat);
 if (!$resObat) {
     die("Query failed: " . mysqli_error($conn));
 } 
 
 // Query untuk tabel obat
 $queryObatKeluar = "SELECT id_obat_keluar, jumlah_keluar FROM obat_keluar ORDER BY jumlah_keluar";
 $resObatKeluar = mysqli_query($conn, $queryObatKeluar);
 if (!$resObatKeluar) {
     die("Query failed: " . mysqli_error($conn));
 }
 
 // Query untuk tabel obat
 $queryObat = "SELECT id_obat, nama_obat FROM obat ORDER BY nama_obat";
 $resObat = mysqli_query($conn, $queryObat);
 if (!$resObat) {
     die("Query failed: " . mysqli_error($conn));
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

 
$dataperiodebulan = "SELECT * FROM periode_bulan";
$resperiodebulan = mysqli_query($conn, $dataperiodebulan);

$dataperiodetahun = "SELECT * FROM periode_tahun";
$resperiodetahun = mysqli_query($conn, $dataperiodetahun);

$datajenis = "SELECT * FROM jenis";
$resjenis = mysqli_query($conn, $datajenis);

$datasatuan = "SELECT * FROM satuan";
$ressatuan = mysqli_query($conn, $datasatuan);

$datasupplier = "SELECT * FROM supplier";
$ressupplier = mysqli_query($conn, $datasupplier);

$dataobatkeluar = "SELECT * FROM obat_keluar";
$resObatKeluar = mysqli_query($conn, $dataobatkeluar);

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
                <span class="text-muted fw-light">Beranda /</span> Kadaluarsa
              </h4>

              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Obat Keluar</h5>
                    <div class="col-sm-2">
                      <select class="form-control" id="tahun" name="tahun">
                          <?php
                          for ($i = date('Y'); $i >= 2022; $i--) {
                              echo "<option value='$i'>$i</option>";
                          }
                          ?>
                      </select>
                    </div>
                    <div class="col-sm-2">
                      <select class="form-control" id="tahun" name="tahun">
                          <?php
                          for ($i = date('Y'); $i >= 2022; $i--) {
                              echo "<option value='$i'>$i</option>";
                          }
                          ?>
                      </select>
                    </div>
                    <form class="d-flex">
                      <div class="input-group">
                        <span class="input-group-text"><i class="tf-icons bx bx-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search..." />
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
                        <th>Satuan</th>
                        <th>Kadaluarsa</th>
                        <th>Periode</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php $i = 1; ?>
                      <?php foreach ($obat_keluar as $row) { ?>
                    <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($i++); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_obat']);?></td>
                            <td><?php echo htmlspecialchars($row['satuan']);?></td>
                            <td><?php echo htmlspecialchars($row['kadaluarsa']);?></td>
                            <td><?php echo htmlspecialchars($row['periode']);?></td>
                        </tr>
                        <?php } ?>
                    </tbody>
                  </table>
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

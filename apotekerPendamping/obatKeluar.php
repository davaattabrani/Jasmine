<!DOCTYPE html>
<?php
require '../config.php';
include '../checkRole.php';
checkRole(['Apoteker Pendamping']);

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
    ok.jumlah_keluar,
    ok.kadaluarsa,
    CONCAT(pb.bulan, ' ', pt.tahun) AS periode
    FROM obat_keluar ok
    JOIN obat o ON ok.id_obat = o.id_obat
    JOIN jenis j ON o.id_jenis = j.id_jenis
    JOIN satuan s ON o.id_satuan = s.id_satuan
    JOIN periode_bulan pb ON ok.id_periode_bulan = pb.id_periode_bulan
    JOIN periode_tahun pt ON ok.id_periode_tahun = pt.id_periode_tahun
    ORDER BY pt.tahun, pb.bulan");

if ($obat_keluar_result) {
    $obat_keluar = mysqli_fetch_all($obat_keluar_result, MYSQLI_ASSOC);
}

 // Query untuk tabel obat
 $queryObat = "SELECT * FROM obat ORDER BY nama_obat";
 $resObat = mysqli_query($conn, $queryObat);
 if (!$resObat) {
     die("Query failed: " . mysqli_error($conn));
 } 
 
 // Query untuk tabel obat
 $queryObatKeluar = "SELECT * FROM obat_keluar ORDER BY jumlah_keluar";
 $resObatKeluar = mysqli_query($conn, $queryObatKeluar);
 if (!$resObatKeluar) {
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

$dataobat = "SELECT * FROM obat";
$resobat = mysqli_query($conn, $dataobat);

$datasatuan = "SELECT * FROM satuan";
$ressatuan = mysqli_query($conn, $datasatuan);

$datasupplier = "SELECT * FROM supplier";
$ressupplier = mysqli_query($conn, $datasupplier);

$dataobatkeluar = "SELECT * FROM obat_keluar";
$resobatkeluar = mysqli_query($conn, $dataobatkeluar);


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
                <span class="text-muted fw-light">Beranda /</span> Obat Keluar
              </h4>

              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Obat Keluar</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahObatKeluar">Tambah Obat Keluar</button>
                </div>

                <!-- Modal Tambah Obat -->
                <form action="prosesTambahObatKeluar.php" method="post">
                <div class="modal fade" id="modalTambahObatKeluar" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Tambah Obat Keluar</h5>
                        <button
                                  type="button"
                                  class="btn-close"
                                  data-bs-dismiss="modal"
                                  aria-label="Close"
                                ></button>
                              </div>
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="nama" class="form-label">Nama Obat</label>
                                    <select
                                      name="nama_obat"
                                      class="form-select"
                                      aria-label="Default select example">
                                      <option selected>Pilih Obat</option>
                                      <?php
                                        while ($rowObat = mysqli_fetch_assoc($resObat)) {
                                            echo "<option value='" . $rowObat['id_obat'] . "'>" . $rowObat['nama_obat'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="row g-2">
                                  <div class="col mb-0">
                                  <label for="obat_keluar" class="form-label">Jumlah Keluar</label>
                                    <input
                                      type="number"
                                      name="jumlah_keluar"
                                      class="form-control"
                                      placeholder="Masukkan Jumlah Keluar"
                                    />
                                  </div>
                                  <div class="col mb-0">
                                  <label for="kadaluarsa" class="form-label">Jumlah Kadaluarsa</label>
                                    <input
                                      type="number"
                                      name="kadaluarsa"
                                      class="form-control"
                                      placeholder="Masukkan Jumlah Kadaluarsa"
                                    />
                                  </div>
                                </div>
                                <div class="row g-2">
                                  <div class="col mb-0">
                                  <label for="periode_bulan" class="form-label">Periode Bulan</label>
                                  <select name="id_periode_bulan" class="form-select">
                                      <option value="">Pilih Periode Bulan</option>
                                      <?php
                                      while ($arrayperiodebulan = mysqli_fetch_array($resperiodebulan)) {
                                        $selected = isset($row['id_periode_bulan']) && $arrayperiodebulan['id_periode_bulan'] == $row['id_periode_bulan'] ? 'selected' : '';
                                        echo "<option $selected value='" . $arrayperiodebulan['id_periode_bulan'] . "'>" . $arrayperiodebulan['bulan'] . "</option>";
                                      }
                                      ?>
                                  </select>
                                  </div>
                                  <div class="col mb-0">
                                  <label for="periode_tahun" class="form-label">Periode Tahun</label>
                                  <select name="id_periode_tahun" class="form-select">
                                      <option value="">Pilih Periode Tahun</option>
                                      <?php
                                      while ($arrayperiodetahun = mysqli_fetch_array($resperiodetahun)) {
                                        $selected = isset($row['id_periode_tahun']) && $arrayperiodetahun['id_periode_tahun'] == $row['id_periode_tahun'] ? 'selected' : '';
                                        echo "<option $selected value='" . $arrayperiodetahun['id_periode_tahun'] . "'>" . $arrayperiodetahun['tahun'] . "</option>";
                                      }
                                      ?>
                                  </select>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                  Kembali
                                </button>
                                <button type="submit" name="submit" class="btn btn-primary">Tambah Data</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        </form>

                        <form action="prosesUbahObatKeluar.php" method="post">
    <div class="modal fade" id="modalUbahObatKeluar" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalCenterTitle">Ubah Obat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col mb-3">
                            <label for="id" class="form-label">ID</label>
                            <input type="text" name="id_obat_keluar" class="form-control" placeholder="Masukkan ID Obat Keluar" value="<?php echo isset($row['id_obat_keluar']) ? htmlspecialchars($row['id_obat_keluar']) : ''; ?>" readonly />
                        </div>
                    </div>
                    <div class="row">
                        <div class="col mb-3">
                            <label for="nama" class="form-label">Nama Obat</label>
                            <select name="id_obat" class="form-select">
                                <option value="">Pilih Obat</option>
                                <?php
                                // Mengisi dropdown dengan obat
                                while ($arrayobat = mysqli_fetch_array($resobat)) {
                                    $selected = ''; // Reset selected
                                    if (isset($row['id_obat']) && $arrayobat['id_obat'] == $row['id_obat']) {
                                        $selected = 'selected'; // Set selected jika ID obat cocok
                                    }
                                    echo "<option $selected value='" . $arrayobat['id_obat'] . "'>" . $arrayobat['nama_obat'] . "</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2">
                        <div class="col mb-0">
                            <label for="jumlah_keluar" class="form-label">Jumlah Keluar</label>
                            <input type="text" name="jumlah_keluar" class="form-control" placeholder="Masukkan Jumlah Keluar" value="" />
                        </div>
                        <div class="col mb-2">
                            <label for="kadaluarsa" class="form-label">Jumlah Kadaluarsa</label>
                            <input type="text" name="kadaluarsa" class="form-control" placeholder="Masukkan Jumlah Kadaluarsa" value="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Kembali</button>
                    <button type="submit" name="submit" class="btn btn-primary">Ubah Data</button>
                </div>
            </div>
        </div>
    </div>
</form>

                 <!-- Tabel -->                           
                 <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Jenis</th>
                        <th>Satuan</th>
                        <th>Jumlah Keluar</th>
                        <th>Kadaluarsa</th>
                        <th>Periode</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php $i = 1; ?>
                      <?php foreach ($obat_keluar as $row) { ?>
                    <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($i++); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_obat'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['jenis'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['satuan'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($row['jumlah_keluar'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars($row['kadaluarsa'] ?? '0'); ?></td>
                            <td><?php echo htmlspecialchars($row['periode'] ?? ''); ?></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalUbahObatKeluar"
                                      data-id="<?php echo htmlspecialchars($row['id_obat_keluar'] ?? ''); ?>" 
                                      data-nama="<?php echo htmlspecialchars($row['id_obat'] ?? ''); ?>" 
                                      data-keluar="<?php echo htmlspecialchars($row['jumlah_keluar'] ?? '0'); ?>"
                                      data-kadaluarsa="<?php echo htmlspecialchars($row['kadaluarsa'] ?? '0'); ?>">
                                        <i class="bx bx-edit-alt me-1"></i> Edit
                                    </a>
                                        <a class="dropdown-item" href="#" onclick="konfirmasiHapus(<?php echo $row['id_obat_keluar'] ?? '0'; ?>)"><i class="bx bx-trash me-1"></i> Delete</a>
                                    </div>
                                </div>
                            </td>
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
        // Menangani event saat modal dibuka untuk mengubah obat keluar
        $('#modalUbahObatKeluar').on('show.bs.modal', function (event) {
    var button = $(event.relatedTarget); // Tombol yang diklik
    var id = button.data('id'); // Ambil ID obat keluar
    var nama = button.data('nama'); // Ambil ID obat
    var keluar = button.data('keluar'); // Ambil jumlah keluar
    var kadaluarsa = button.data('kadaluarsa'); // Ambil kadaluarsa

    var modal = $(this);
    modal.find('input[name="id_obat_keluar"]').val(id); // Isi ID
    modal.find('select[name="id_obat"] option').each(function () {
        if ($(this).val() == nama) { // Pastikan ini menggunakan ID obat
            $(this).prop('selected', true);
        }
    });
    modal.find('input[name="jumlah_keluar"]').val(keluar); // Isi Jumlah Keluar
    modal.find('input[name="kadaluarsa"]').val(kadaluarsa); // Isi Kadalu
        });
    </script>

    <!-- Script untuk konfirmasi sebelum menghapus data -->
    <script>
        function konfirmasiHapus(id) {
            if (confirm('Yakin Ingin Menghapus Data?')) {
                window.location.href = 'prosesHapusObatKeluar.php?id_obat_keluar=' + id;
            }
        }
    </script>

  </body>
</html>

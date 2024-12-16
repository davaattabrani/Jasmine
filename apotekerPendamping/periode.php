<!DOCTYPE html>
<?php
require '../config.php';

// Gantikan pemanggilan query() dengan mysqli_query()
$conn = mysqli_connect("localhost", "root", "", "db_jasmine");
if (!$conn) {
    die("Koneksi ke basis data gagal: " . mysqli_connect_error());
}

$periode_result = mysqli_query($conn, "SELECT * FROM periode");

// Periksa apakah kueri berhasil sebelum melanjutkan
if (!$periode_result) {
    die("Query error: " . mysqli_error($conn));
}

if ($periode_result) {
    $periode = mysqli_fetch_all($periode_result, MYSQLI_ASSOC);
}

// Tambahkan logika untuk mendapatkan data pengguna berdasarkan ID
if (isset($_GET['id_periode'])) {
    $id_periode = $_GET['id_periode'];
    $query = mysqli_query($conn, "SELECT * FROM periode WHERE id_periode = '$id_periode'");
    $row = mysqli_fetch_assoc($query);
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
                <span class="text-muted fw-light">Dashboard /</span> Periode
              </h4>

              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Periode</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPeriode">Tambah Periode</button>
                </div>

                <!-- Modal Tambah Periode -->
                <form action="prosesTambahPeriode.php" method="post">
                <div class="modal fade" id="modalTambahPeriode" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Tambah Periode</h5>
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
                                    <label for="bulan" class="form-label">Bulan</label>
                                    <select
                                      name="bulan"
                                      class="form-select"
                                      aria-label="Default select example">
                                      <option selected>- Pilih Bulan -</option>
                                      <option value="Januari">Januari</option>
                                      <option value="Februari">Februari</option>
                                      <option value="Maret">Maret</option>
                                      <option value="April">April</option>
                                      <option value="Mei">Mei</option>
                                      <option value="Juni">Juni</option>
                                      <option value="Juli">Juli</option>
                                      <option value="Agustus">Agustus</option>
                                      <option value="September">September</option>
                                      <option value="Oktober">Oktober</option>
                                      <option value="November">November</option>
                                      <option value="Desember">Desember</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <select
                                      name="tahun"
                                      class="form-select"
                                      aria-label="Default select example">
                                      <option selected>- Pilih Tahun -</option>
                                      <?php for ($i = date('Y'); $i >= 2019; $i--) : ?>
                                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                      <?php endfor; ?>
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

                        <form action="prosesUbahPeriode.php" method="post">
                        <div class="modal fade" id="modalUbahPeriode" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="modalCenterTitle">Ubah Periode</h5>
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
                                    <label for="id" class="form-label">ID</label>
                                    <input
                                      type="text"
                                      name="id_periode"
                                      class="form-control"
                                      placeholder="Masukkan Nama"
                                      value="<?php echo isset($row) ? htmlspecialchars($row['id_periode']) : ''; ?>"
                                      readonly
                                    />
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="bulan" class="form-label">Bulan</label>
                                    <select name="bulan" class="form-select" aria-label="Default select example">
                                        <option value="" disabled>- Pilih Bulan -</option>
                                        <option value="Januari" <?php echo (isset($row) && $row['bulan'] == 'Januari') ? 'selected' : ''; ?>>Januari</option>
                                        <option value="Februari" <?php echo (isset($row) && $row['bulan'] == 'Februari') ? 'selected' : ''; ?>>Februari</option>
                                        <option value="Maret" <?php echo (isset($row) && $row['bulan'] == 'Maret') ? 'selected' : ''; ?>>Maret</option>
                                        <option value="April" <?php echo (isset($row) && $row['bulan'] == 'April') ? 'selected' : ''; ?>>April</option>
                                        <option value="Mei" <?php echo (isset($row) && $row['bulan'] == 'Mei') ? 'selected' : ''; ?>>Mei</option>
                                        <option value="Juni" <?php echo (isset($row) && $row['bulan'] == 'Juni') ? 'selected' : ''; ?>>Juni</option>
                                        <option value="Juli" <?php echo (isset($row) && $row['bulan'] == 'Juli') ? 'selected' : ''; ?>>Juli</option>
                                        <option value="Agustus" <?php echo (isset($row) && $row['bulan'] == 'Agustus') ? 'selected' : ''; ?>>Agustus</option>
                                        <option value="September" <?php echo (isset($row) && $row['bulan'] == 'September') ? 'selected' : ''; ?>>September</option>
                                        <option value="Oktober" <?php echo (isset($row) && $row['bulan'] == 'Oktober') ? 'selected' : ''; ?>>Oktober</option>
                                        <option value="November" <?php echo (isset($row) && $row['bulan'] == 'November') ? 'selected' : ''; ?>>November</option>
                                        <option value="Desember" <?php echo (isset($row) && $row['bulan'] == 'Desember') ? 'selected' : ''; ?>>Desember</option>
                                    </select>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <select
                                      name="tahun"
                                      class="form-select"
                                      aria-label="Default select example">
                                      <option selected>- Pilih Tahun -</option>
                                      <?php for ($i = date('Y'); $i >= 2019; $i--) : ?>
                                        <option value="<?php echo $i; ?>" <?php echo (isset($row) && $row['tahun'] == $i) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                                      <?php endfor; ?>
                                    </select>
                                  </div>
                                </div>
                              </div>
                              <div class="modal-footer">
                                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                                  Kembali
                                </button>
                                <button type="submit" name="submit" class="btn btn-primary">Ubah Data</button>
                              </div>
                            </div>
                          </div>
                        </div>
                        </form>


                <div class="table-responsive text-nowrap">
                  <table class="table table-striped">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Bulan</th>
                        <th>Tahun</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php $i = 1; ?>
                      <?php foreach ($periode as $row) { ?>
                    <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($i++); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['bulan']);?></td>
                            <td><?php echo htmlspecialchars($row['tahun']);?></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalUbahPeriode"
                                           data-id="<?php echo htmlspecialchars($row['id_periode']); ?>" 
                                           data-bulan="<?php echo htmlspecialchars($row['bulan']); ?>" 
                                           data-tahun="<?php echo htmlspecialchars($row['tahun']); ?>">
                                           <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="konfirmasiHapus(<?php echo $row['id_periode']; ?>)"><i class="bx bx-trash me-1"></i> Delete</a>
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
        // Menangani event saat modal dibuka
        $('#modalUbahPeriode').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var id = button.data('id'); // Ambil data-id
            var bulan = button.data('bulan'); // Ambil data-bulan
            var tahun = button.data('tahun'); // Ambil data-tahun

            // Debugging: Cek nilai yang diambil
            console.log("ID: " + id);
            console.log("Bulan: " + bulan);
            console.log("Tahun: " + tahun);

            // Isi input dengan data yang diambil
            var modal = $(this);
            modal.find('select[name="bulan"]').val(bulan);
            modal.find('select[name="tahun"]').val(tahun); // Setel nilai dropdown tahun
            modal.find('input[name="id_periode"]').val(id); // Tambahkan input untuk ID periode
        });
    </script>

    <!-- Script untuk konfirmasi sebelum menghapus data -->
    <script>
        function konfirmasiHapus(id) {
            if (confirm('Yakin Ingin Menghapus Data?')) {
                window.location.href = 'prosesHapusPeriode.php?id_periode=' + id;
            }
        }
    </script>

  </body>
</html>

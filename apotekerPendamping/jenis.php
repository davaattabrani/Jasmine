<!DOCTYPE html>
<?php
require '../config.php';
include '../checkRole.php';
checkRole(['Apoteker Pendamping']);

// Gantikan pemanggilan query() dengan mysqli_query()
$conn = mysqli_connect("localhost", "root", "", "db_jasmine");
if (!$conn) {
    die("Koneksi ke basis data gagal: " . mysqli_connect_error());
}

$jenis_result = mysqli_query($conn, "SELECT * FROM jenis");

// Periksa apakah kueri berhasil sebelum melanjutkan
if (!$jenis_result) {
    die("Query error: " . mysqli_error($conn));
}

if ($jenis_result) {
    $jenis = mysqli_fetch_all($jenis_result, MYSQLI_ASSOC);
}

// Tambahkan logika untuk mendapatkan data pengguna berdasarkan ID
if (isset($_GET['id_jenis'])) {
    $id_jenis = $_GET['id_jenis'];
    $query = mysqli_query($conn, "SELECT * FROM jenis WHERE id_jenis = '$id_jenis'");
    $row = mysqli_fetch_assoc($query);
}

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
                <span class="text-muted fw-light">Beranda /</span> Jenis
              </h4>

              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Jenis</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahJenis">Tambah Jenis</button>
                </div>

                <!-- Modal Tambah Periode -->
                <form action="prosesTambahJenis.php" method="post">
                <div class="modal fade" id="modalTambahJenis" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Tambah Jenis</h5>
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
                                    <label for="nama" class="form-label">Nama Jenis</label>
                                    <input
                                      type="text"
                                      name="nama_jenis"
                                      class="form-control"
                                      placeholder="Masukkan Nama Jenis"
                                    />
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

                        <form action="prosesUbahJenis.php" method="post">
                        <div class="modal fade" id="modalUbahJenis" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="modalCenterTitle">Ubah Jenis</h5>
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
                                      name="id_jenis"
                                      class="form-control"
                                      placeholder="Masukkan Nama Jenis"
                                      value="<?php echo isset($row) ? htmlspecialchars($row['id_jenis']) : ''; ?>"
                                      readonly
                                    />
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                  <label for="nama" class="form-label">Nama Jenis</label>
                                    <input
                                      type="text"
                                      name="nama_jenis"
                                      class="form-control"
                                      placeholder="Masukkan Nama Jenis"
                                      value="<?php echo isset($row) ? htmlspecialchars($row['nama_jenis']) : ''; ?>"
                                    />
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
                        <th>Nama Jenis</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php $i = 1; ?>
                      <?php foreach ($jenis as $row) { ?>
                    <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($i++); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_jenis']);?></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalUbahJenis"
                                           data-id="<?php echo htmlspecialchars($row['id_jenis']); ?>" 
                                           data-nama="<?php echo htmlspecialchars($row['nama_jenis']); ?>" >
                                           <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="konfirmasiHapus(<?php echo $row['id_jenis']; ?>)"><i class="bx bx-trash me-1"></i> Delete</a>
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
        $('#modalUbahJenis').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var id = button.data('id'); // Ambil data-id
            var nama = button.data('nama'); // Ambil data-tahun

            // Debugging: Cek nilai yang diambil
            console.log("ID: " + id);
            console.log("Nama: " + nama);

            // Isi input dengan data yang diambil
            var modal = $(this);
            modal.find('input[name="nama_jenis"]').val(nama);
            modal.find('input[name="id_jenis"]').val(id); // Tambahkan input untuk ID periode
        });
    </script>

    <!-- Script untuk konfirmasi sebelum menghapus data -->
    <script>
        function konfirmasiHapus(id) {
            if (confirm('Yakin Ingin Menghapus Data?')) {
                window.location.href = 'prosesHapusJenis.php?id_jenis=' + id;
            }
        }
    </script>

  </body>
</html>

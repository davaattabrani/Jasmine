<!DOCTYPE html>
<?php
require '../config.php';

// Gantikan pemanggilan query() dengan mysqli_query()
$conn = mysqli_connect("localhost", "root", "", "db_jasmine");
if (!$conn) {
    die("Koneksi ke basis data gagal: " . mysqli_connect_error());
}

$pengguna_result = mysqli_query($conn, "SELECT * FROM pengguna");

// Periksa apakah kueri berhasil sebelum melanjutkan
if (!$pengguna_result) {
    die("Query error: " . mysqli_error($conn));
}

if ($pengguna_result) {
    $pengguna = mysqli_fetch_all($pengguna_result, MYSQLI_ASSOC);
}

// Tambahkan logika untuk mendapatkan data pengguna berdasarkan ID
if (isset($_GET['id_pengguna'])) {
    $id_pengguna = $_GET['id_pengguna'];
    $query = mysqli_query($conn, "SELECT * FROM pengguna WHERE id_pengguna = '$id_pengguna'");
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
                <span class="text-muted fw-light">Dashboard /</span> Pengadaan
              </h4>

              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Pengadaan</h5>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#modalTambahPengguna">Tambah Pengguna</button>
                </div>

                <!-- Modal Tambah Pengguna -->
                <form action="prosesTambahPengguna.php" method="post">
                <div class="modal fade" id="modalTambahPengguna" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Tambah Pengguna</h5>
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
                                    <label for="nama" class="form-label">Nama</label>
                                    <input
                                      type="text"
                                      name="nama_pengguna"
                                      class="form-control"
                                      placeholder="Masukkan Nama"
                                    />
                                  </div>
                                </div>
                                <div class="row g-2">
                                  <div class="col mb-0">
                                    <label for="username" class="form-label">Username</label>
                                    <input
                                      type="text"
                                      name="username"
                                      class="form-control"
                                      placeholder="Masukkan Username"
                                    />
                                  </div>
                                  <div class="col mb-2">
                                    <label for="password" class="form-label">Password</label>
                                    <input
                                      type="password"
                                      name="password"
                                      class="form-control"
                                      placeholder="Masukkan Password"
                                    />
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="jabatan" class="form-label">Jabatan</label>
                                    <select
                                      name="jabatan"
                                      class="form-select"
                                      aria-label="Default select example">
                                      <option selected>Pilih Jabatan</option>
                                      <option value="Penanggung Jawab Farmasi">Penanggung Jawab Farmasi</option>
                                      <option value="Apoteker Pendamping">Apoteker Pendamping</option>
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

                        <form action="prosesUbahPengguna.php" method="post">
                        <div class="modal fade" id="modalUbahPengguna" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="modalCenterTitle">Ubah Pengguna</h5>
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
                                      name="id_pengguna"
                                      class="form-control"
                                      placeholder="Masukkan Nama"
                                      value="<?php echo isset($row) ? htmlspecialchars($row['id_pengguna']) : ''; ?>"
                                      readonly
                                    />
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="nama" class="form-label">Nama</label>
                                    <input
                                      type="text"
                                      name="nama_pengguna"
                                      class="form-control"
                                      placeholder="Masukkan Nama"
                                      value="<?php echo isset($row) ? htmlspecialchars($row['nama_pengguna']) : ''; ?>"
                                    />
                                  </div>
                                </div>
                                <div class="row g-2">
                                  <div class="col mb-0">
                                    <label for="username" class="form-label">Username</label>
                                    <input
                                      type="text"
                                      name="username"
                                      class="form-control"
                                      placeholder="Masukkan Username"
                                      value="<?php echo isset($row) ? htmlspecialchars($row['username']) : ''; ?>"
                                    />
                                  </div>
                                  <div class="col mb-2">
                                    <label for="password" class="form-label">Password</label>
                                    <input
                                      type="text"
                                      name="password"
                                      class="form-control"
                                      placeholder="Masukkan Password"
                                      value="<?php echo isset($row) ? htmlspecialchars($row['password']) : ''; ?>"
                                    />
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="jabatan" class="form-label">Jabatan</label>
                                    <select name="jabatan" class="form-select" aria-label="Default select example">
                                        <option value="" disabled>Pilih Jabatan</option>
                                        <option value="Penanggung Jawab Farmasi" <?php echo (isset($row) && $row['jabatan'] == 'Penanggung Jawab Farmasi') ? 'selected' : ''; ?>>Penanggung Jawab Farmasi</option>
                                        <option value="Apoteker Pendamping" <?php echo (isset($row) && $row['jabatan'] == 'Apoteker Pendamping') ? 'selected' : ''; ?>>Apoteker Pendamping</option>
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
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Username</th>
                        <th>Password</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php $i = 1; ?>
                      <?php foreach ($pengguna as $row) { ?>
                    <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($i++); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_pengguna']);?></td>
                            <td><?php echo htmlspecialchars($row['jabatan']);?></td>
                            <td><?php echo htmlspecialchars($row['username']);?></td>
                            <td><?php echo htmlspecialchars($row['password']);?></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalUbahPengguna" 
                                           data-id="<?php echo htmlspecialchars($row['id_pengguna']); ?>" 
                                           data-nama="<?php echo htmlspecialchars($row['nama_pengguna']); ?>" 
                                           data-username="<?php echo htmlspecialchars($row['username']); ?>"
                                           data-password="<?php echo htmlspecialchars($row['password']); ?>" 
                                           data-jabatan="<?php echo htmlspecialchars($row['jabatan']); ?>">
                                           <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="konfirmasiHapus(<?php echo $row['id_pengguna']; ?>)"><i class="bx bx-trash me-1"></i> Delete</a>
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
        $('#modalUbahPengguna').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var id = button.data('id'); // Ambil data-id
            var nama = button.data('nama'); // Ambil data-nama
            var username = button.data('username'); // Ambil data-username
            var password = button.data('password'); // Ambil data-username
            var jabatan = button.data('jabatan'); // Ambil data-jabatan

            // Debugging: Cek nilai yang diambil
            console.log("ID: " + id);
            console.log("Nama: " + nama);
            console.log("Username: " + username);
            console.log("Password: " + password);
            console.log("Jabatan: " + jabatan);

            // Isi input dengan data yang diambil
            var modal = $(this);
            modal.find('input[name="nama_pengguna"]').val(nama);
            modal.find('input[name="username"]').val(username);
            modal.find('input[name="password"]').val(password); // Kosongkan password
            modal.find('select[name="jabatan"]').val(jabatan); // Setel nilai dropdown jabatan
            modal.find('input[name="id_pengguna"]').val(id); // Tambahkan input untuk ID pengguna
        });
    </script>

    <!-- Script untuk konfirmasi sebelum menghapus data -->
    <script>
        function konfirmasiHapus(id) {
            if (confirm('Yakin Ingin Menghapus Data?')) {
                window.location.href = 'prosesHapusPengguna.php?id_pengguna=' + id;
            }
        }
    </script>

  </body>
</html>

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

$conn = mysqli_connect("localhost", "root", "", "db_jasmine");
if (!$conn) {
    die("Koneksi ke basis data gagal: " . mysqli_connect_error());
}

$obat_masuk_result = mysqli_query($conn, "SELECT 
    o.nama_obat,
    j.nama_jenis AS jenis,
    s.nama_satuan AS satuan,
    sup.nama_supplier AS supplier,
    om.jumlah_masuk,
    CONCAT(pb.bulan, ' ', pt.tahun) AS periode
FROM obat_masuk om
JOIN obat o ON om.id_obat = o.id_obat
JOIN jenis j ON o.id_jenis = j.id_jenis
JOIN satuan s ON o.id_satuan = s.id_satuan
JOIN supplier sup ON o.id_supplier = sup.id_supplier
JOIN periode_bulan pb ON om.id_periode_bulan = pb.id_periode_bulan
JOIN periode_tahun pt ON om.id_periode_tahun = pt.id_periode_tahun
ORDER BY pt.tahun, pb.bulan");

// Periksa apakah kueri berhasil sebelum melanjutkan
if (!$obat_masuk_result) {
    die("Query error: " . mysqli_error($conn));
}

if ($obat_masuk_result) {
    $obat_masuk = mysqli_fetch_all($obat_masuk_result, MYSQLI_ASSOC);
}

 // Query untuk tabel obat
 $queryObat = "SELECT * FROM obat ORDER BY nama_obat";
 $resObat = mysqli_query($conn, $queryObat);
 if (!$resObat) {
     die("Query failed: " . mysqli_error($conn));
 } 
 
 // Query untuk tabel obat
 $queryObatMasuk = "SELECT * FROM obat_masuk ORDER BY jumlah_masuk";
 $resObatMasuk = mysqli_query($conn, $queryObatMasuk);
 if (!$resObatMasuk) {
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

$dataobatmasuk = "SELECT * FROM obat_masuk";
$resObatMasuk = mysqli_query($conn, $dataobatmasuk);

$dataobat = "SELECT * FROM obat";
$resobat = mysqli_query($conn, $dataobat);

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
                <span class="text-muted fw-light">Beranda /</span> Obat Masuk
              </h4>

              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Obat Masuk</h5>
                </div>

                <!-- Modal Tambah Obat -->
                <form action="prosesTambahObat.php" method="post">
                <div class="modal fade" id="modalTambahObat" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="modalCenterTitle">Tambah Obat</h5>
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
                                    <input
                                      type="text"
                                      name="nama_obat"
                                      class="form-control"
                                      placeholder="Masukkan Nama Obat"
                                    />
                                  </div>
                                </div>
                                <div class="row g-2">
                                  <div class="col mb-0">
                                  <label for="jenis" class="form-label">Jenis</label>
                                    <select
                                      name="nama_jenis"
                                      class="form-select"
                                      aria-label="Default select example">
                                      <option selected>Pilih Jenis</option>
                                      <?php
                                        while ($rowJenis = mysqli_fetch_assoc($resJenis)) {
                                            echo "<option value='" . $rowJenis['id_jenis'] . "'>" . $rowJenis['nama_jenis'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                  </div>
                                  <div class="col mb-2">
                                  <label for="satuan" class="form-label">Satuan</label>
                                    <select
                                      name="nama_satuan"
                                      class="form-select"
                                      aria-label="Default select example">
                                      <option selected>Pilih Satuan</option>
                                      <?php
                                        while ($rowSatuan = mysqli_fetch_assoc($resSatuan)) {
                                            echo "<option value='" . $rowSatuan['id_satuan'] . "'>" . $rowSatuan['nama_satuan'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="supplier" class="form-label">Nama Supplier</label>
                                    <select
                                      name="nama_supplier"
                                      class="form-select"
                                      aria-label="Default select example">
                                      <option selected>Pilih Supplier</option>
                                      <?php
                                        while ($rowSupplier = mysqli_fetch_assoc($resSupplier)) {
                                            echo "<option value='" . $rowSupplier['id_supplier'] . "'>" . $rowSupplier['nama_supplier'] . "</option>";
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

                        <form action="prosesUbahObat.php" method="post">
                        <div class="modal fade" id="modalUbahObat" tabindex="-1" aria-hidden="true">
                          <div class="modal-dialog modal-dialog-centered" role="document">
                            <div class="modal-content">
                              <div class="modal-header">
                                <h5 class="modal-title" id="modalCenterTitle">Ubah Obat Masuk</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                              </div>
                              <div class="modal-body">
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="id" class="form-label">ID</label>
                                    <input type="text" name="id_obat" class="form-control" placeholder="Masukkan Nama Obat" value="<?php echo isset($row['id_obat']) ? htmlspecialchars($row['id_obat']) : ''; ?>" readonly />
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                  <label for="nama" class="form-label">Nama Obat</label>
                                    <input
                                      type="text"
                                      name="nama_obat"
                                      class="form-control"
                                      placeholder="Masukkan Nama Obat"
                                      value="<?php echo isset($row['nama_obat']) ? htmlspecialchars($row['nama_obat']) : ''; ?>"
                                    />
                                  </div>
                                </div>
                                <div class="row g-2">
                                  <div class="col mb-0">
                                  <label for="jenis" class="form-label">Jenis</label>
                                  <select name="id_jenis" class="form-select">
                                      <option value="">Pilih Jenis</option>
                                      <?php
                                      // Menampilkan opsi untuk jenis
                                      while ($rowJenis = mysqli_fetch_assoc($resJenis)) {
                                          $selected = (isset($row['id_jenis']) && $row['id_jenis'] == $rowJenis['id_jenis']) ? 'selected' : '';
                                          echo "<option value='" . $rowJenis['id_jenis'] . "' $selected>" . $rowJenis['nama_jenis'] . "</option>";
                                      }
                                      ?>
                                  </select>

                                  </div>
                                  <div class="col mb-2">
                                  <label for="satuan" class="form-label">Satuan</label>
                                    <select name="id_satuan" id="id_satuan" class="form-select" aria-label="Default select example">
                                      <option value="">Pilih Satuan</option>
                                      <?php
                                        while ($rowSatuan = mysqli_fetch_assoc($resSatuan)) {
                                            $selected = isset($row['id_satuan']) && $rowSatuan['id_satuan'] == $row['id_satuan'] ? 'selected' : '';
                                            echo "<option $selected value='" . $rowSatuan['id_satuan'] . "'>" . $rowSatuan['nama_satuan'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                  </div>
                                </div>
                                <div class="row">
                                  <div class="col mb-3">
                                    <label for="supplier" class="form-label">Nama Supplier</label>
                                    <select name="id_supplier" id="id_supplier" class="form-select" aria-label="Default select example">
                                      <option value="">Pilih Supplier</option>
                                      <?php
                                        while ($rowSupplier = mysqli_fetch_assoc($resSupplier)) {
                                            $selected = isset($row['id_supplier']) && $rowSupplier['id_supplier'] == $row['id_supplier'] ? 'selected' : '';
                                            echo "<option $selected value='" . $rowSupplier['id_supplier'] . "'>" . $rowSupplier['nama_supplier'] . "</option>";
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
                        <th>Supplier</th>
                        <th>Jumlah Masuk</th>
                        <th>Periode</th>
                        <th>Aksi</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php $i = 1; ?>
                      <?php foreach ($obat_masuk as $row) { ?>
                    <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($i++); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_obat']);?></td>
                            <td><?php echo htmlspecialchars($row['jenis']);?></td>
                            <td><?php echo htmlspecialchars($row['satuan']);?></td>
                            <td><?php echo htmlspecialchars($row['supplier']);?></td>
                            <td><?php echo htmlspecialchars($row['jumlah_masuk']);?></td>
                            <td><?php echo htmlspecialchars($row['periode']);?></td>
                            <td>
                                <div class="dropdown">
                                    <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="bx bx-dots-vertical-rounded"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                    <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalUbahObatMasuk"
                                          data-id="<?php echo htmlspecialchars($row['id_obat_masuk'] ?? ''); ?>" 
                                          data-nama="<?php echo htmlspecialchars($row['id_obat'] ?? ''); ?>" 
                                          data-masuk="<?php echo htmlspecialchars($row['jumlah_masuk'] ?? ''); ?>"
                                          data-satuan="<?php echo htmlspecialchars($row['id_satuan'] ?? ''); ?>" 
                                          data-supplier="<?php echo htmlspecialchars($row['id_supplier'] ?? ''); ?>">
                                          <i class="bx bx-edit-alt me-1"></i> Edit
                                        </a>
                                        <a class="dropdown-item" href="#" onclick="konfirmasiHapus(<?php echo $row['id_obat'] ?? '0'; ?>)"><i class="bx bx-trash me-1"></i> Delete</a>
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

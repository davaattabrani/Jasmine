<!DOCTYPE html><?php
include '../config.php';
include '../checkRole.php';
checkRole(['Apoteker Pendamping']);

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
                <span class="text-muted fw-light">Beranda</span>
              </h4>

                <div class="card mb-4">
                    <div class="d-flex align-items-end row">
                        <div class="col-sm-7">
                            <div class="card-body">
                            <?php
                            $username = $username ?? 'Guest';
                            ?>
                            <h5 class="card-title text-primary">Selamat Datang <?php echo htmlspecialchars($nama_pengguna); ?>! 🎉</h5>
                            <p class="mb-4">
                                Selamat bekerja !
                            </p>
                            </div>
                        </div>
                        <div class="col-sm-5 text-center text-sm-left">
                            <div class="card-body pb-0 px-0 px-md-4">
                            <img
                                src="../assets/img/illustrations/man-with-laptop-light.png"
                                height="140"
                                alt="View Badge User"
                                data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                data-app-light-img="illustrations/man-with-laptop-light.png"
                            />
                            </div>
                        </div>
                    </div>
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
  </body>
</html>

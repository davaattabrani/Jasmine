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

$stok_result = mysqli_query($conn, "SELECT s.*, o.nama_obat, jen.nama_jenis, sat.nama_satuan, sup.nama_supplier 
                                        FROM stok s
                                        JOIN obat o ON s.id_obat = o.id_obat
                                        JOIN jenis jen ON o.id_jenis = jen.id_jenis
                                        JOIN satuan sat ON o.id_satuan = sat.id_satuan
                                        JOIN supplier sup ON o.id_supplier = sup.id_supplier
                                        ORDER BY s.id_stok");

// Periksa apakah kueri berhasil sebelum melanjutkan
if (!$stok_result) {
    die("Query error: " . mysqli_error($conn));
}

if ($stok_result) {
    $stok = mysqli_fetch_all($stok_result, MYSQLI_ASSOC);
}

 // Query untuk tabel stok
 $queryStok = "SELECT * FROM stok ORDER BY stok";
 $resStok = mysqli_query($conn, $queryStok);
 if (!$resStok) {
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

$datajenis = "SELECT * FROM jenis";
$resjenis = mysqli_query($conn, $datajenis);
$datasatuan = "SELECT * FROM satuan";
$ressatuan = mysqli_query($conn, $datasatuan);
$datasupplier = "SELECT * FROM supplier";
$ressupplier = mysqli_query($conn, $datasupplier);
$datastok = "SELECT * FROM stok";
$resstok = mysqli_query($conn, $datastok);

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
                <span class="text-muted fw-light">Beranda /</span> Stok
              </h4>
              <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Data Stok</h5>
                    <form class="d-flex">
                          <div class="input-group">
                            <span class="input-group-text"><i class="tf-icons bx bx-search"></i></span>
                            <input type="text" id="searchInput" class="form-control" placeholder="Search..." />
                          </div>
                        </form>
                </div>
                 <!-- Tabel -->                           
                <div class="table-responsive text-nowrap">
                  <table class="table table-striped" id="dataTable">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama Obat</th>
                        <th>Jenis</th>
                        <th>Satuan</th>
                        <th>Stok Akhir</th>
                        <th>Status</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
                      <?php $i = 1; ?>
                      <?php foreach ($stok as $row) { ?>
                    <tr>
                            <td>
                                <strong><?php echo htmlspecialchars($i++); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($row['nama_obat']);?></td>
                            <td><?php echo htmlspecialchars($row['nama_jenis']);?></td>
                            <td><?php echo htmlspecialchars($row['nama_satuan']);?></td>
                            <td><?php echo htmlspecialchars($row['stok']);?></td>
                            <td>
                                <?php if ($row['stok'] < 5): ?>
                                    <i class="bx bx-error"></i>
                                <?php else: ?>
                                    <?php echo ''; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
                <div id="pagination" class="d-flex justify-content-center" style="margin-top: 20px;"></div>
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

document.addEventListener("DOMContentLoaded", function () {
    let table = document.getElementById("dataTable");
    let rows = table.getElementsByTagName("tr");
    let rowsPerPage = 10;
    let currentPage = 1;
    let maxVisibleButtons = 10; // Maksimal 10 tombol paginasi yang ditampilkan

    function showPage(page) {
        let start = (page - 1) * rowsPerPage + 1; // +1 untuk menghindari header
        let end = start + rowsPerPage;

        for (let i = 1; i < rows.length; i++) {
            rows[i].style.display = (i >= start && i < end) ? "" : "none";
        }
    }

    function setupPagination() {
        let totalPages = Math.ceil((rows.length - 1) / rowsPerPage);
        let paginationContainer = document.getElementById("pagination");
        paginationContainer.innerHTML = `
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item prev">
                        <a class="page-link" href="javascript:void(0);">
                            <i class="tf-icon bx bx-chevrons-left"></i>
                        </a>
                    </li>
                    <li class="page-item next">
                        <a class="page-link" href="javascript:void(0);">
                            <i class="tf-icon bx bx-chevrons-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>`;

        let paginationList = paginationContainer.querySelector(".pagination");
        let prevButton = paginationList.querySelector(".prev");
        let nextButton = paginationList.querySelector(".next");

        function renderPageButtons() {
            let startPage = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

            // Jika halaman terakhir kurang dari maxVisibleButtons, geser ke belakang
            if (endPage - startPage + 1 < maxVisibleButtons) {
                startPage = Math.max(1, endPage - maxVisibleButtons + 1);
            }

            // Bersihkan tombol yang lama
            document.querySelectorAll(".pagination .page-item.number").forEach(el => el.remove());

            // Tambahkan tombol halaman
            for (let i = startPage; i <= endPage; i++) {
                let listItem = document.createElement("li");
                listItem.classList.add("page-item", "number");
                if (i === currentPage) listItem.classList.add("active");

                let link = document.createElement("a");
                link.classList.add("page-link");
                link.href = "javascript:void(0);";
                link.innerText = i;
                link.addEventListener("click", function () {
                    currentPage = i;
                    showPage(currentPage);
                    renderPageButtons();
                });

                listItem.appendChild(link);
                paginationList.insertBefore(listItem, nextButton);
            }

            // Tambahkan tombol "..." jika masih ada halaman sebelum dan sesudah
            if (startPage > 1) {
                let dots = document.createElement("li");
                dots.classList.add("page-item", "disabled", "number");
                dots.innerHTML = `<a class="page-link" href="javascript:void(0);">...</a>`;
                paginationList.insertBefore(dots, paginationList.querySelector(".number"));
            }

            if (endPage < totalPages) {
                let dots = document.createElement("li");
                dots.classList.add("page-item", "disabled", "number");
                dots.innerHTML = `<a class="page-link" href="javascript:void(0);">...</a>`;
                paginationList.insertBefore(dots, nextButton);
            }
        }

        prevButton.addEventListener("click", function () {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
                renderPageButtons();
            }
        });

        nextButton.addEventListener("click", function () {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
                renderPageButtons();
            }
        });

        showPage(currentPage);
        renderPageButtons();
    }

    setupPagination();
});

document.addEventListener("DOMContentLoaded", function () {
    let table = document.getElementById("dataTable");
    let rows = table.getElementsByTagName("tr");
    let rowsPerPage = 10;
    let currentPage = 1;
    let maxVisibleButtons = 10;
    let searchInput = document.getElementById("searchInput");

    function showPage(page) {
        let start = (page - 1) * rowsPerPage + 1; // +1 untuk menghindari header
        let end = start + rowsPerPage;
        let visibleRows = 0;

        for (let i = 1; i < rows.length; i++) {
            if (rows[i].style.display !== "none") {
                visibleRows++;
                rows[i].style.display = (visibleRows >= start && visibleRows < end) ? "" : "none";
            }
        }
    }

    function filterTable() {
        let filter = searchInput.value.toLowerCase();
        let visibleCount = 0;

        for (let i = 1; i < rows.length; i++) {
            let nameCell = rows[i].getElementsByTagName("td")[1]; // Kolom nama obat

            if (nameCell) {
                let textValue = nameCell.textContent || nameCell.innerText;
                if (textValue.toLowerCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                    visibleCount++;
                } else {
                    rows[i].style.display = "none";
                }
            }
        }

        currentPage = 1; // Reset ke halaman pertama saat filter diterapkan
        setupPagination(visibleCount);
        showPage(currentPage);
    }

    function setupPagination(filteredRowsCount) {
        let totalRows = filteredRowsCount || (rows.length - 1);
        let totalPages = Math.ceil(totalRows / rowsPerPage);
        let paginationContainer = document.getElementById("pagination");

        paginationContainer.innerHTML = `
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item prev">
                        <a class="page-link" href="javascript:void(0);">
                            <i class="tf-icon bx bx-chevrons-left"></i>
                        </a>
                    </li>
                    <li class="page-item next">
                        <a class="page-link" href="javascript:void(0);">
                            <i class="tf-icon bx bx-chevrons-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>`;

        let paginationList = paginationContainer.querySelector(".pagination");
        let prevButton = paginationList.querySelector(".prev");
        let nextButton = paginationList.querySelector(".next");

        function renderPageButtons() {
            let startPage = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
            let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

            if (endPage - startPage + 1 < maxVisibleButtons) {
                startPage = Math.max(1, endPage - maxVisibleButtons + 1);
            }

            document.querySelectorAll(".pagination .page-item.number").forEach(el => el.remove());

            for (let i = startPage; i <= endPage; i++) {
                let listItem = document.createElement("li");
                listItem.classList.add("page-item", "number");
                if (i === currentPage) listItem.classList.add("active");

                let link = document.createElement("a");
                link.classList.add("page-link");
                link.href = "javascript:void(0);";
                link.innerText = i;
                link.addEventListener("click", function () {
                    currentPage = i;
                    showPage(currentPage);
                    renderPageButtons();
                });

                listItem.appendChild(link);
                paginationList.insertBefore(listItem, nextButton);
            }

            if (startPage > 1) {
                let dots = document.createElement("li");
                dots.classList.add("page-item", "disabled", "number");
                dots.innerHTML = `<a class="page-link" href="javascript:void(0);">...</a>`;
                paginationList.insertBefore(dots, paginationList.querySelector(".number"));
            }

            if (endPage < totalPages) {
                let dots = document.createElement("li");
                dots.classList.add("page-item", "disabled", "number");
                dots.innerHTML = `<a class="page-link" href="javascript:void(0);">...</a>`;
                paginationList.insertBefore(dots, nextButton);
            }
        }

        prevButton.addEventListener("click", function () {
            if (currentPage > 1) {
                currentPage--;
                showPage(currentPage);
                renderPageButtons();
            }
        });

        nextButton.addEventListener("click", function () {
            if (currentPage < totalPages) {
                currentPage++;
                showPage(currentPage);
                renderPageButtons();
            }
        });

        showPage(currentPage);
        renderPageButtons();
    }

    searchInput.addEventListener("keyup", filterTable);

    setupPagination();
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

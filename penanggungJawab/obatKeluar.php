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
    ok.jumlah_keluar,
    ok.kadaluarsa,
    pb.id_periode_bulan,
    (ok.jumlah_keluar - ok.kadaluarsa) AS stok_akhir,
    CONCAT(pb.bulan, ' ', pt.tahun) AS periode
FROM obat_keluar ok
JOIN obat o ON ok.id_obat = o.id_obat
JOIN jenis j ON o.id_jenis = j.id_jenis
JOIN satuan s ON o.id_satuan = s.id_satuan
JOIN periode_bulan pb ON ok.id_periode_bulan = pb.id_periode_bulan
JOIN periode_tahun pt ON ok.id_periode_tahun = pt.id_periode_tahun
ORDER BY ok.id_obat_keluar");

// Periksa apakah kueri berhasil sebelum melanjutkan
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
                <span class="text-muted fw-light">Beranda /</span> Obat Keluar
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
                        <input type="text" id="searchInput" class="form-control" placeholder="Search..." />
                      </div>
                    </form>
                </div>

                 <!-- Tabel -->                           
                 <div class="table-responsive text-nowrap">
                  <table class="table table-striped" id="dataTable">
                    <thead>
                      <tr>
                        <th>No <i class="bx bx-sort" id="sortNoIcon" style="cursor: pointer;"></th>
                        <th>Nama Obat</th>
                        <th>Jenis</th>
                        <th>Satuan</th>
                        <th>Jumlah Keluar</th>
                        <th>Kadaluarsa</th>
                        <th>Periode <i class="bx bx-sort" id="sortPeriodeIcon" style="cursor: pointer;"></th>
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
                            <td><?php echo htmlspecialchars($row['jenis']);?></td>
                            <td><?php echo htmlspecialchars($row['satuan']);?></td>
                            <td><?php echo htmlspecialchars($row['jumlah_keluar']);?></td>
                            <td><?php echo htmlspecialchars($row['kadaluarsa']);?></td>
                            <td data-id-periode="<?php echo htmlspecialchars($row['id_periode_bulan']); ?>"><?php echo htmlspecialchars($row['periode']);?></td>
                            <td>
                                <div class="dropdown">
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#modalUbahObat"
                                          data-id="<?php echo htmlspecialchars($row['id_obat']); ?>" 
                                          data-nama="<?php echo htmlspecialchars($row['nama_obat']); ?>" 
                                          data-jenis="<?php echo htmlspecialchars($row['id_jenis']); ?>"
                                          data-satuan="<?php echo htmlspecialchars($row['id_satuan']); ?>" 
                                          data-supplier="<?php echo htmlspecialchars($row['id_supplier']); ?>">
                                    </div>
                                </div>
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
    let tbody = table.querySelector("tbody");
    let allRows = Array.from(tbody.querySelectorAll("tr")); // Menyimpan semua data awal
    let filteredRows = [...allRows]; // Data yang digunakan saat search atau sort
    let searchInput = document.getElementById("searchInput");
    let paginationContainer = document.getElementById("pagination");
    let sortIcon = document.getElementById("sortPeriodeIcon");

    let rowsPerPage = 10;
    let currentPage = 1;
    let maxVisibleButtons = 10;
    let sortDirection = 1; // 1 = Ascending, -1 = Descending

    function showPage(page) {
        currentPage = page;
        let start = (page - 1) * rowsPerPage;
        let end = start + rowsPerPage;

        filteredRows.forEach((row, index) => {
            row.style.display = index >= start && index < end ? "" : "none";
        });

        updatePagination();
    }

    function updatePagination() {
        let totalPages = Math.ceil(filteredRows.length / rowsPerPage);
        paginationContainer.innerHTML = `
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <li class="page-item prev ${currentPage === 1 ? "disabled" : ""}">
                        <a class="page-link" href="javascript:void(0);">
                            <i class="tf-icon bx bx-chevrons-left"></i>
                        </a>
                    </li>
                    <li class="page-item next ${currentPage === totalPages ? "disabled" : ""}">
                        <a class="page-link" href="javascript:void(0);">
                            <i class="tf-icon bx bx-chevrons-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>`;

        let paginationList = paginationContainer.querySelector(".pagination");
        let prevButton = paginationList.querySelector(".prev");
        let nextButton = paginationList.querySelector(".next");

        prevButton.addEventListener("click", function () {
            if (currentPage > 1) {
                showPage(currentPage - 1);
            }
        });

        nextButton.addEventListener("click", function () {
            if (currentPage < totalPages) {
                showPage(currentPage + 1);
            }
        });

        let startPage = Math.max(1, currentPage - Math.floor(maxVisibleButtons / 2));
        let endPage = Math.min(totalPages, startPage + maxVisibleButtons - 1);

        for (let i = startPage; i <= endPage; i++) {
            let pageItem = document.createElement("li");
            pageItem.className = `page-item ${i === currentPage ? "active" : ""}`;
            pageItem.innerHTML = `<a class="page-link" href="javascript:void(0);">${i}</a>`;
            pageItem.addEventListener("click", () => showPage(i));
            paginationList.insertBefore(pageItem, nextButton);
        }
    }

    searchInput.addEventListener("input", function () {
        let searchTerm = searchInput.value.toLowerCase();
        filteredRows = allRows.filter(row => row.cells[1].textContent.toLowerCase().includes(searchTerm));

        // Reset tampilan
        tbody.innerHTML = "";
        filteredRows.forEach(row => tbody.appendChild(row));

        showPage(1);
    });

    sortIcon.addEventListener("click", function () {
    filteredRows.sort((rowA, rowB) => {
        let periodeA = parseInt(rowA.cells[6].getAttribute("data-id-periode")) || 0;
        let periodeB = parseInt(rowB.cells[6].getAttribute("data-id-periode")) || 0;

        return (periodeA - periodeB) * sortDirection;
    });

    sortDirection *= -1;
    tbody.innerHTML = "";
    filteredRows.forEach(row => tbody.appendChild(row));

    showPage(1);

    if (sortDirection === 1) {
        sortIcon.classList.remove("bx-sort-down");
        sortIcon.classList.add("bx-sort-up");
    } else {
        sortIcon.classList.remove("bx-sort-up");
        sortIcon.classList.add("bx-sort-down");
    }
});

let sortNoDirection = 1; // 1 = Ascending, -1 = Descending
let sortNoIcon = document.getElementById("sortNoIcon");

sortNoIcon.addEventListener("click", function () {
    filteredRows.sort((rowA, rowB) => {
        let noA = parseInt(rowA.cells[0].textContent.trim()) || 0;
        let noB = parseInt(rowB.cells[0].textContent.trim()) || 0;

        return (noA - noB) * sortNoDirection;
    });

    sortNoDirection *= -1;
    tbody.innerHTML = "";
    filteredRows.forEach(row => tbody.appendChild(row));

    showPage(1);

    if (sortNoDirection === 1) {
        sortNoIcon.classList.remove("bx-sort-down");
        sortNoIcon.classList.add("bx-sort-up");
    } else {
        sortNoIcon.classList.remove("bx-sort-up");
        sortNoIcon.classList.add("bx-sort-down");
    }
});



    showPage(1);
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

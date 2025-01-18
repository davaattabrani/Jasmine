<?php
include 'config.php'; 
// Mengambil data dari tabel stok
$query = "SELECT 
            ok.jumlah_keluar, 
            ok.kadaluarsa, 
            o.nama_obat, 
            o.jenis, 
            o.satuan, 
            (ok.jumlah_keluar - ok.kadaluarsa) AS sisa_stok 
          FROM 
            stok s 
          JOIN 
            obat o ON s.id_obat = o.id_obat 
          JOIN 
            obat_keluar ok ON s.id_obat_keluar = ok.id_obat_keluar";

$result = mysqli_query($conn, $query);
$obat = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>
<div class="table-responsive text-nowrap">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Obat</th>
        <th>Jenis</th>
        <th>Satuan</th>
        <th>Kadaluarsa</th>
        <th>Sisa Stok</th>
      </tr>
    </thead>
    <tbody class="table-border-bottom-0">
      <?php $i = 1; ?>
      <?php if (isset($obat) && is_array($obat)) { ?>
      <?php foreach ($obat as $row) { ?>
      <tr>
        <td>
          <strong><?php echo htmlspecialchars($i++); ?></strong>
        </td>
        <td><?php echo htmlspecialchars($row['nama_obat']);?></td>
        <td><?php echo htmlspecialchars($row['jenis']);?></td>
        <td><?php echo htmlspecialchars($row['satuan']);?></td>
        <td><?php echo htmlspecialchars($row['kadaluarsa']);?></td>
        <td><?php echo htmlspecialchars($row['sisa_stok']);?></td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="6">Data tidak tersedia</td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
</div>


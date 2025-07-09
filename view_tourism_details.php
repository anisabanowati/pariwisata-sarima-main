<?php
include('includes/dbconnection.php');
if(isset($_POST['edit_id5'])){
  $eid = intval($_POST['edit_id5']);
  $sql = "SELECT * FROM tourism_data WHERE ID = :eid";
  $query = $dbh->prepare($sql);
  $query->bindParam(':eid', $eid, PDO::PARAM_INT);
  $query->execute();
  $result = $query->fetch(PDO::FETCH_OBJ);
  if($query->rowCount() > 0){
?>
<div class="container">
  <h4><?php echo htmlentities($result->NamaWisata); ?></h4>
  <p><strong>Jumlah Pengunjung:</strong> <?php echo htmlentities($result->JumlahPengunjung); ?></p>
  <p><strong>Pendapatan:</strong> Rp <?php echo number_format($result->Pendapatan, 0, ',', '.'); ?></p>
  <p><strong>Sewa Gedung:</strong> Rp <?php echo number_format($result->SewaGedung, 0, ',', '.'); ?></p>
  <p><strong>Rentang Waktu:</strong> <?php echo htmlentities($result->RentangWaktu); ?></p>
  <p><strong>Tanggal Input:</strong> <?php echo date("d-m-Y", strtotime($result->Tanggal)); ?></p>
</div>
<?php
  } else {
    echo "<p>Data tidak ditemukan.</p>";
  }
}
?>

<?php
session_start();
include('includes/checklogin.php');
check_login();

$uid = $_SESSION['odmsaid'];        // id user login

/* ===== Ambil profil user ===== */
$stmt = $dbh->prepare("SELECT AdminName, MobileNumber AS SiteName
                       FROM tbladmin WHERE ID = :id LIMIT 1");
$stmt->bindParam(':id', $uid, PDO::PARAM_INT);
$stmt->execute();
$user       = $stmt->fetch(PDO::FETCH_ASSOC);
$isAdmin    = ($user['AdminName'] === 'Admin');
$defaultSite = $user['SiteName'];   // e.g. "Pantai Parangtritis"

if (isset($_POST['save'])) {
  // admin pakai input; user pakai $defaultSite
  $nama_wisata       = $isAdmin ? $_POST['nama_wisata'] : $defaultSite;
  $jumlah_pengunjung = $_POST['jumlah_pengunjung'];
  $pendapatan        = $_POST['pendapatan'];
  // $sewa_gedung       = $_POST['sewa_gedung'];
  $rentang_waktu     = $_POST['rentang_waktu'];
  $tanggal           = $_POST['tanggal'];

  $sql = "INSERT INTO tourism_data
          (NamaWisata,JumlahPengunjung,Pendapatan,
           RentangWaktu,Tanggal,CreatedBy)
          VALUES
          (:nama,:jml,:pend,:rentang,:tgl,:uid)";
  $stmt = $dbh->prepare($sql);
  $stmt->bindParam(':nama',   $nama_wisata,       PDO::PARAM_STR);
  $stmt->bindParam(':jml',    $jumlah_pengunjung, PDO::PARAM_INT);
  $stmt->bindParam(':pend',   $pendapatan,        PDO::PARAM_STR);
  // $stmt->bindParam(':sewa',   $sewa_gedung,       PDO::PARAM_STR);
  $stmt->bindParam(':rentang', $rentang_waktu,     PDO::PARAM_STR);
  $stmt->bindParam(':tgl',    $tanggal,           PDO::PARAM_STR);
  $stmt->bindParam(':uid',    $uid,               PDO::PARAM_INT);
  $stmt->execute();

  if ($dbh->lastInsertId()) {
    echo "<script>alert('Data berhasil disimpan!');location='new_visitor.php';</script>";
  } else {
    echo "<script>alert('Gagal menyimpan data');</script>";
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<?php @include("includes/head.php"); ?>

<body>
  <div class="container-scroller">
    <?php @include("includes/header.php"); ?>
    <div class="container-fluid page-body-wrapper">
      <?php @include("includes/sidebar.php"); ?>
      <div class="main-panel">
        <div class="content-wrapper">
          <div class="row">
            <div class="col-lg-12 grid-margin stretch-card">
              <div class="card">
                <div class="modal-header">
                  <h5 class="modal-title">Input Data Tempat Wisata</h5>
                </div>
                <div class="col-md-12 mt-4">
                  <form method="post" class="forms-sample">
                    <div class="row">
                      <!-- === Kolom Nama Wisata === -->
                      <div class="form-group col-md-6">
                        <label>Nama Wisata</label>
                        <?php if ($isAdmin): ?>
                          <!-- Admin: bisa ketik -->
                          <input type="text" name="nama_wisata"
                            class="form-control" required>
                        <?php else: ?>
                          <!-- User: terkunci pada SiteName -->
                          <input type="text" class="form-control"
                            value="<?= htmlentities($defaultSite) ?>" disabled>
                          <input type="hidden" name="nama_wisata"
                            value="<?= htmlentities($defaultSite) ?>">
                        <?php endif; ?>
                      </div>

                      <div class="form-group col-md-6">
                        <label>Jumlah Pengunjung</label>
                        <input type="number" name="jumlah_pengunjung"
                          class="form-control" required>
                      </div>
                    </div>

                    <div class="row">
                      <div class="form-group col-md-6">
                        <label>Rentang Waktu</label>
                        <select name="rentang_waktu" class="form-control" required>
                          <option value="Harian">Harian</option>
                          <option value="Mingguan">Mingguan</option>
                          <option value="Bulanan">Bulanan</option>
                        </select>
                      </div>
                      <div class="form-group col-md-6">
                        <label>Tanggal</label>
                        <input type="date" name="tanggal"
                          class="form-control" required>
                      </div>
                    </div>


                    <div class="row">
                      <div class="form-group col-md-12">
                        <label>Pendapatan (Rp)</label>
                        <input type="number" name="pendapatan"
                          class="form-control" required>
                      </div>
                      <!-- <div class="form-group col-md-6">
                      <label>Sewa Gedung (Rp)</label>
                      <input type="number" name="sewa_gedung"
                             class="form-control" required>
                    </div> -->
                    </div>


                    <button type="submit" name="save"
                      class="btn btn-info mr-2 mb-4">Simpan</button>
                  </form>
                </div><!-- /.col -->
              </div><!-- /.card -->
            </div>
          </div>
        </div><!-- /.content-wrapper -->
        <?php @include("includes/footer.php"); ?>
      </div><!-- /.main-panel -->
    </div><!-- /.page-body-wrapper -->
  </div><!-- /.container-scroller -->
  <?php @include("includes/foot.php"); ?>
</body>

</html>
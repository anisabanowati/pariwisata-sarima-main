<?php
include('includes/checklogin.php');
include('includes/dbconnection.php');
check_login();
?>
<!DOCTYPE html>
<html lang="en">
  
  <?php include("includes/head.php"); ?>
  <body>
  <div class="container-scroller">
    <?php include("includes/header.php"); ?>
    <div class="container-fluid page-body-wrapper">
      <?php include("includes/sidebar.php"); ?>

      <div class="main-panel">
        <div class="content-wrapper">
          <h4 class="mb-3">Manajemen Pengelola Wisata</h4>
          <button id="btnTambah" class="btn btn-dark mb-3">Tambah Pengelola</button>
          <div id="jumlahPengelola" class="mb-3"></div>
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="thead-dark">
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Nama Wisata</th>
                      <th>Username</th>
                      <th>Password</th>
                      <th>Aksi</th>
                    </tr>
                  </thead>
                  <tbody id="tabelPengelola"></tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
        <!-- <footer>Made with love by <a href="http://iegcode.com"> iegcode</a></footer> -->
      </div>
    </div>
  </div>


<!-- Modal Form -->
<div class="modal" id="modalForm" tabindex="-1" style="display:none;">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalTitle">Tambah Pengelola</h5>
          <button type="button" class="btn-close" id="tutupModal"></button>
        </div>
        <div class="modal-body">
          <form id="formPengelola">
            <input type="hidden" name="id" id="id">
            <div class="mb-3">
              <input type="text" name="nama" id="nama" placeholder="Nama" class="form-control" required>
            </div>
            <div class="mb-3">
              <input type="text" name="nama_wisata" id="nama_wisata" placeholder="Nama Wisata" class="form-control" required>
            </div>
            <div class="mb-3">
              <input type="text" name="username" id="username" placeholder="Username" class="form-control" required>
            </div>
            <div class="mb-3">
              <input type="password" name="password" id="password" placeholder="Password" class="form-control" required>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Simpan</button>
              <button type="button" class="btn btn-secondary" id="tutupModal2">Batal</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

<?php include("includes/foot.php"); ?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function loadData() {
  $.get("fetch_pengelola.php", function(data) {
    $("#tabelPengelola").html(data);
  });
  $.get("fetch_pengelola.php?jumlah=1", function(data) {
    $("#jumlahPengelola").html("Jumlah Pengelola Wisata: " + data);
  });
}

$(document).ready(function() {
  loadData();

  $("#btnTambah").click(function() {
    $("#formPengelola")[0].reset();
    $("#modalTitle").text("Tambah Pengelola");
    $("#modalForm").show();
  });

  $("#tutupModal").click(function() {
    $("#modalForm").hide();
  });

  $(document).on("click", ".btn-edit", function() {
    const id = $(this).data("id");
    $.get("get_pengelola.php", { id: id }, function(data) {
      const pengelola = JSON.parse(data);
      $("#id").val(pengelola.id);
      $("#nama").val(pengelola.nama);
      $("#nama_wisata").val(pengelola.nama_wisata);
      $("#username").val(pengelola.username);
      $("#password").val(""); // kosongkan
      $("#modalTitle").text("Edit Pengelola");
      $("#modalForm").show();
    });
  });

  $(document).on("click", ".btn-hapus", function() {
    const id = $(this).data("id");
    if (confirm("Yakin ingin menghapus?")) {
      $.post("delete_pengelola.php", { id: id }, function() {
        loadData();
      });
    }
  });

  $("#formPengelola").submit(function(e) {
    e.preventDefault();
    $.post("add_pengelola.php", $(this).serialize(), function() {
      $("#modalForm").hide();
      loadData();
    });
  });
});
</script>
<style>
.modal { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; align-items: center; justify-content: center; }
.modal-content { background: white; border-radius: 5px; width: 400px; }
</style>
</body>
</html>

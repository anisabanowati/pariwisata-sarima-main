<?php
include('includes/checklogin.php');
check_login();
$id = intval($_GET['id']);
$sql = "SELECT * FROM tourism_data WHERE ID = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();
$data = $query->fetch(PDO::FETCH_OBJ);

if (isset($_POST['update'])) {
  $nama = $_POST['nama_wisata'];
  $jumlah = $_POST['jumlah_pengunjung'];
  $pendapatan = $_POST['pendapatan'];
  $sewa = $_POST['sewa_gedung'];
  $tanggal = $_POST['tanggal'];

  $sql = "UPDATE tourism_data SET NamaWisata=:nama, JumlahPengunjung=:jumlah, Pendapatan=:pendapatan, SewaGedung=:sewa, Tanggal=:tanggal WHERE ID=:id";
  $query = $dbh->prepare($sql);
  $query->bindParam(':nama', $nama);
  $query->bindParam(':jumlah', $jumlah);
  $query->bindParam(':pendapatan', $pendapatan);
  $query->bindParam(':sewa', $sewa);
  $query->bindParam(':tanggal', $tanggal);
  $query->bindParam(':id', $id);
  $query->execute();

  echo "<script>alert('Data berhasil diupdate'); window.location.href='manage_visitor.php';</script>";
}
?>

<!DOCTYPE html>
<html>

<head>
  <title>Edit Data Wisata</title>
  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: #f2f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .form-container {
      background: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 500px;
    }

    .form-container h2 {
      margin-bottom: 20px;
      color: #333;
      text-align: center;
    }

    label {
      display: block;
      margin-bottom: 6px;
      color: #555;
      font-weight: 600;
    }

    input[type="text"],
    input[type="number"],
    input[type="date"] {
      width: 100%;
      padding: 10px 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-sizing: border-box;
      transition: border-color 0.3s;
    }

    input[type="text"]:focus,
    input[type="number"]:focus,
    input[type="date"]:focus {
      border-color: #007bff;
      outline: none;
    }

    button[type="submit"] {
      background: #007bff;
      color: #fff;
      padding: 12px;
      border: none;
      border-radius: 8px;
      width: 100%;
      font-size: 16px;
      font-weight: bold;
      cursor: pointer;
      transition: background 0.3s;
    }

    button[type="submit"]:hover {
      background: #0056b3;
    }
  </style>
</head>

<body>
  <div class="form-container">
    <h2>Edit Data Wisata</h2>
    <form method="post">
      <label>Nama Wisata</label>
      <input type="text" name="nama_wisata" value="<?= htmlentities($data->NamaWisata) ?>" required>

      <label>Jumlah Pengunjung</label>
      <input type="number" name="jumlah_pengunjung" value="<?= htmlentities($data->JumlahPengunjung) ?>" required>

      <label>Pendapatan</label>
      <input type="number" name="pendapatan" value="<?= htmlentities($data->Pendapatan) ?>" required>

      <label>Tanggal</label>
      <input type="date" name="tanggal" value="<?= htmlentities($data->Tanggal) ?>" required>

      <button type="submit" name="update">Update</button>
    </form>
  </div>
</body>

</html>
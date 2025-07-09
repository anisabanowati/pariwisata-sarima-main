<?php
include('includes/dbconnection.php');
if (isset($_GET['jumlah'])) {
  $sql = "SELECT COUNT(*) FROM pengelola_wisata";
  echo $dbh->query($sql)->fetchColumn();
  exit;
}
$sql = "SELECT * FROM pengelola_wisata ORDER BY id ASC";
$query = $dbh->prepare($sql);
$query->execute();
$data = $query->fetchAll(PDO::FETCH_OBJ);
$no = 1;
foreach ($data as $row) {
  echo "<tr>
    <td>{$no}</td>
    <td>{$row->nama}</td>
    <td>{$row->nama_wisata}</td>
    <td>{$row->username}</td>
    <td>********</td>
    <td>
      <button class='btn btn-sm btn-info btn-edit' data-id='{$row->id}'>Edit</button>
      <button class='btn btn-sm btn-danger btn-hapus' data-id='{$row->id}'>Hapus</button>
    </td>
  </tr>";
  $no++;
}

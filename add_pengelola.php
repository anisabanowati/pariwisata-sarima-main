<?php
include('includes/dbconnection.php');
$id = $_POST['id'];
$nama = $_POST['nama'];
$nama_wisata = $_POST['nama_wisata'];
$username = $_POST['username'];
$password = $_POST['password'];
if ($id) {
  if (!empty($password)) {
    $password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE pengelola_wisata SET nama=?, nama_wisata=?, username=?, password=? WHERE id=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$nama, $nama_wisata, $username, $password, $id]);
  } else {
    $sql = "UPDATE pengelola_wisata SET nama=?, nama_wisata=?, username=? WHERE id=?";
    $stmt = $dbh->prepare($sql);
    $stmt->execute([$nama, $nama_wisata, $username, $id]);
  }
} else {
  $password = password_hash($password, PASSWORD_DEFAULT);
  $sql = "INSERT INTO pengelola_wisata (nama, nama_wisata, username, password) VALUES (?, ?, ?, ?)";
  $stmt = $dbh->prepare($sql);
  $stmt->execute([$nama, $nama_wisata, $username, $password]);
}

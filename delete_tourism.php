<?php
include('includes/checklogin.php');
check_login();
$id = intval($_GET['id']);

$sql = "DELETE FROM tourism_data WHERE ID = :id";
$query = $dbh->prepare($sql);
$query->bindParam(':id', $id, PDO::PARAM_INT);
$query->execute();

echo "<script>alert('Data berhasil dihapus'); window.location.href='manage_visitor.php';</script>";

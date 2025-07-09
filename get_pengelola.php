<?php
include('includes/dbconnection.php');
$id = $_GET['id'];
$sql = "SELECT * FROM pengelola_wisata WHERE id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$id]);
$data = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($data);

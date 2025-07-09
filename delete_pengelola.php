<?php
include('includes/dbconnection.php');
$id = $_POST['id'];
$sql = "DELETE FROM pengelola_wisata WHERE id = ?";
$stmt = $dbh->prepare($sql);
$stmt->execute([$id]);

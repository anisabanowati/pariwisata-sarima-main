<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');
if (isset($_POST['saveupdates'])) {
  $adminid = $_SESSION['editid2'];
  $fName2 = $_POST['firstname2'];
  $lName2 = $_POST['lastname2'];
  $namawisata2 = $_POST['namawisata2'];
  $username2 = $_POST['username2'];
  $sql4 = "update users set FirstName=:firstname2,LastName=:lastname2,NamaWisata=:namawisata2,UserName=:username2 where ID=:aid";
  $query4 = $dbh->prepare($sql4);
  $query4->bindParam(':firstname2', $fName2, PDO::PARAM_STR);
  $query4->bindParam(':lastname2', $lName2, PDO::PARAM_STR);
  $query4->bindParam(':username2', $username2, PDO::PARAM_STR);
  $query4->bindParam(':namawisata2', $namawisata2, PDO::PARAM_STR);
  $query4->bindParam(':aid', $adminid, PDO::PARAM_STR);
  $query4->execute();
  if ($query4->execute()) {
    echo '<script>alert("Profile has been updated")</script>';
  } else {
    echo '<script>alert("update failed! try again later")</script>';
  }
}
?>
<div class="card-body">
  <h4 class="card-title">Update User Form </h4>
  <form class="forms-sample" method="post" enctype="multipart/form-data" class="form-horizontal">
    <?php
    $eid = $_POST['edit_id'];
    $sql = "SELECT * from  users where users.ID=:eid";
    $query = $dbh->prepare($sql);
    $query->bindParam(':eid', $eid, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    $cnt = 1;
    if ($query->rowCount() > 0) {
      foreach ($results as $row) {
        $_SESSION['editid2'] = $row->ID;
    ?>
        <div class="form-group">
          <label for="exampleInputName1">First Name</label>
          <input type="text" name="firstname2" class="form-control" id="firstname2" value="<?php echo $row->FirstName; ?>" required>
        </div>
        <div class="form-group">
          <label for="exampleInputName1">Last Name</label>
          <input type="text" name="lastname2" class="form-control" id="lastname2" value="<?php echo $row->LastName; ?>" required>
        </div>
        <div class="form-group">
          <label for="exampleInputName1">Nama Wisata</label>
          <input type="text" name="namawisata2" class="form-control" id="namawisata2" value="<?php echo $row->NamaWisata; ?>" required>
        </div>
        <div class="form-group">
          <label for="exampleInputName1">Username</label>
          <input type="text" name="username2" class="form-control" id="username2" value="<?php echo $row->UserName; ?>" required>
        </div>
    <?php $cnt = $cnt + 1;
      }
    } ?>
    <button type="submit" name="saveupdates" class="btn btn-primary btn-fw mr-2">Update</button>
    <button type="button" class="btn btn-success" data-dismiss="modal">Cancel</button>
  </form>
</div>
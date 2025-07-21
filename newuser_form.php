<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

if (!empty($_POST["username"])) {
    $username = $_POST["username"];

    $sql = "SELECT UserName FROM users WHERE UserName=:username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);

    if ($query->rowCount() > 0) {
        echo "<script>alert('Username already exists. Try another one');</script>";
    } else {
        if (isset($_POST['signup'])) {
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $namapanggilan = $_POST['namapanggilan'];
            $namawisata = $_POST['namawisata'];
            $username = $_POST['username'];
            $dignity = $_POST['dignity'];
            $password = md5($_POST['password']);

            $sql = "INSERT INTO users(AdminName, UserName, FirstName, LastName, NamaPanggilan, NamaWisata, Password) 
                    VALUES(:dignity, :username, :firstname, :lastname, :namapanggilan, :namawisata, :password)";
            $query = $dbh->prepare($sql);
            $query->bindParam(':dignity', $dignity, PDO::PARAM_STR);
            $query->bindParam(':username', $username, PDO::PARAM_STR);
            $query->bindParam(':firstname', $firstname, PDO::PARAM_STR);
            $query->bindParam(':lastname', $lastname, PDO::PARAM_STR);
            $query->bindParam(':namapanggilan', $namapanggilan, PDO::PARAM_STR);
            $query->bindParam(':namawisata', $namawisata, PDO::PARAM_STR);
            $query->bindParam(':password', $password, PDO::PARAM_STR);
            $query->execute();

            $lastInsertId = $dbh->lastInsertId();
            if ($lastInsertId) {
                echo "<script>alert('Registration successful. Now you can login');</script>";
            } else {
                echo "<script>alert('Something went wrong. Please try again');</script>";
            }
        }
    }
}
?>


<script>
    function checkAvailability() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data: 'emailid=' + $("#emailid").val(),
            type: "POST",
            success: function(data) {
                $("#user-availability-status").html(data);
                $("#loaderIcon").hide();
            },
            error: function() {}
        });
    }
</script>

<script>
    function checkAvailability2() {
        $("#loaderIcon").show();
        jQuery.ajax({
            url: "check_availability.php",
            data: 'fullname=' + $("#fullname").val(),
            type: "POST",
            success: function(data) {
                $("#user-availability-status2").html(data);
                $("#loaderIcon").hide();
            },
            error: function() {}
        });
    }
</script>
<script type="text/javascript">
    function valid() {
        if (document.signup.password.value != document.signup.confirmpassword.value) {
            alert("Password and Confirm Password Field do not match  !!");
            document.signup.confirmpassword.focus();
            return false;
        }
        return true;
    }
</script>

<div class="card-body">
    <form method="post" name="signup" onSubmit="return valid();">
        <div class="row">
            <div class="form-group col-md-6">
                <select class="form-control" name="dignity" id="dignity" onchange="toggleWisata()" required>
                    <option value="">Select Permission</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
            </div>
            <div class="form-group col-md-6">
                <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <input type="text" class="form-control" name="namapanggilan" placeholder="Nama Panggilan" required>
            </div>
            <div class="form-group col-md-6">
                <input type="text" class="form-control" id="namawisata" name="namawisata" placeholder="Nama Wisata" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
                <span id="user-availability-status2" style="font-size:12px;"></span>
            </div>
            <div class="form-group col-md-6">
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
        </div>
        <div class="row">
            <div class="form-group col-md-6">
                <input type="password" class="form-control" name="confirmpassword" placeholder="Confirm Password" required>
            </div>
        </div>
        <div class="form-group">
            <input type="submit" value="Register" name="signup" class="btn btn-info">
        </div>
    </form>
</div>

<script>
function toggleWisata() {
    var dignity = document.getElementById("dignity").value;
    var wisata = document.getElementById("namawisata");
    if (dignity === "Admin") {
        wisata.disabled = true;
        wisata.value = '';
    } else {
        wisata.disabled = false;
    }
}

function valid() {
    var pass = document.signup.password.value;
    var confirmPass = document.signup.confirmpassword.value;
    if (pass != confirmPass) {
        alert("Password and Confirm Password do not match!");
        return false;
    }
    return true;
}
</script>


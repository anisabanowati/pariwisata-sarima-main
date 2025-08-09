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
    <form method="post" name="signup" onsubmit="return valid();">
        <!-- Baris 1: Select Permission & Last Name -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Select Permission</label>
                <select class="form-control" name="dignity" id="dignity" onchange="toggleWisata()" required>
                    <option value="">Select Permission</option>
                    <option value="Admin">Admin</option>
                    <option value="User">User</option>
                </select>
            </div>
            <div class="col-md-6">
                <label>Last Name</label>
                <input type="text" class="form-control" name="lastname" placeholder="Last Name" required>
            </div>
        </div>

        <!-- Baris 2: First Name & Nama Panggilan -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label>First Name</label>
                <input type="text" class="form-control" name="firstname" placeholder="First Name" required>
            </div>
            <div class="col-md-6">
                <label>Nama Panggilan</label>
                <input type="text" class="form-control" name="namapanggilan" placeholder="Nama Panggilan" required>
            </div>
        </div>

        <!-- Baris 3: Nama Wisata & Password -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Nama Wisata</label>
                <input type="text" class="form-control" id="namawisata" name="namawisata" placeholder="Nama Wisata" required>
            </div>
            <div class="col-md-6">
                <label>Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" required>
            </div>
        </div>

        <!-- Baris 4: Username & Confirm Password -->
        <div class="row mb-3">
            <div class="col-md-6">
                <label>Password</label>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>
            <div class="col-md-6">
                <label>Confirm Password</label>
                <input type="password" class="form-control" name="confirmpassword" placeholder="Confirm Password" required>
            </div>
        </div>

        <!-- Tombol Register -->
        <div class="text-center">
            <input type="submit" value="Register" name="signup" class="btn btn-primary px-4">
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


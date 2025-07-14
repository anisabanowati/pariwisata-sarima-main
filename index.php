<?php
session_start();
error_reporting(0);
include('includes/dbconnection.php');

$message = "";
if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = md5($_POST['password']);
    $sql = "SELECT * FROM users WHERE UserName=:username and Password=:password";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->bindParam(':password', $password, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetchAll(PDO::FETCH_OBJ);
    if ($query->rowCount() > 0) {
        foreach ($results as $result) {
            $_SESSION['odmsaid'] = $result->ID;
            $_SESSION['login'] = $result->username;
            $_SESSION['names'] = $result->FirstName;
            $_SESSION['permission'] = $result->AdminName;
            $_SESSION['companyname'] = $result->CompanyName;
            $_SESSION['role'] = $result->AdminName; // Bisa 'admin' atau 'pengelola'
            $_SESSION['wisata'] = $result->MobileNumber; // Anggap MobileNumber = Nama Wisata

            $get = $result->Status;
        }
        $aa = $_SESSION['odmsaid'];
        $sql = "SELECT * from users  where ID=:aa";
        $query = $dbh->prepare($sql);
        $query->bindParam(':aa', $aa, PDO::PARAM_STR);
        $query->execute();
        $results = $query->fetchAll(PDO::FETCH_OBJ);
        if ($query->rowCount() > 0) {
            foreach ($results as $row) {
                if ($row->Status == "1") {
                    echo "<script type='text/javascript'> document.location ='dashboard.php'; </script>";
                } else {
                    $message = "Akun Anda dinonaktifkan. Hubungi admin.";
                }
            }
        }
    } else {
        $message = "Username atau password salah.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Dinas Pariwisata Bantul</title>
    <link rel="shortcut icon" href="https://bantulkab.go.id/resource/doc/images/logos/logo-bantul-medium.png" type="image/x-icon">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #3498db, #2ecc71);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
            width: 100%;
            max-width: 400px;
            padding: 40px;
            text-align: center;
            position: relative;
        }

        .login-container img {
            width: 80px;
            margin-bottom: 20px;
        }

        .login-container h2 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .login-container p {
            color: #7f8c8d;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 6px;
            color: #2c3e50;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            transition: border 0.3s;
        }

        .form-control:focus {
            outline: none;
            border-color: #3498db;
        }

        .login-btn {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-btn:hover {
            background-color: #2980b9;
        }

        .alert {
            background-color: #e74c3c;
            color: white;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        @media (max-width: 480px) {
            .login-container {
                padding: 30px 20px;
            }
        }
    </style>
</head>

<body>
    <div class="login-container">
        <img src="https://bantulkab.go.id/resource/doc/images/logos/logo-bantul-medium.png" alt="Logo Bantul">
        <h2>Login</h2>
        <p>Dinas Pariwisata Kabupaten Bantul</p>

        <?php if ($message != ""): ?>
            <div class="alert"><?= htmlentities($message) ?></div>
        <?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Masukkan username" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" class="form-control" id="password" placeholder="********" required>
            </div>
            <button type="submit" name="login" class="login-btn">Log In</button>
        </form>
    </div>
</body>

</html>
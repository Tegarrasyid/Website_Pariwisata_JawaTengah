<?php
session_start();
require "koneksi.php";

if(isset($_POST['loginbtn'])){
    $username = htmlspecialchars($_POST['username']);
    $password = htmlspecialchars($_POST['password']);

    $query = mysqli_query($koneksi, "SELECT * FROM admin WHERE username='$username'");
    $countdata = mysqli_num_rows($query);
    $data = mysqli_fetch_array($query);

    if($countdata > 0 && $password === $data['password']){
        $_SESSION['username'] = $data['username'];
        $_SESSION['login'] = true;
        header("Location: index.php");
        exit; // penting supaya script berhenti
    } else {
        $error = ($countdata>0) ? "Password Anda Salah" : "Tidak Ada Akun";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background: #f5f6fa;
      color: #333;
    }

    .main {
      height: 100vh;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      width: 400px;
      padding: 30px;
      border-radius: 15px;
      background: #fff;
      box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    }

    .login-box label {
      display: block;
      margin-bottom: 5px;
      font-weight: 600;
      font-size: 14px;
    }

    .login-box input {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
      transition: 0.2s;
    }

    .login-box input:focus {
      outline: none;
      border-color: #007bff;
      box-shadow: 0 0 0 2px rgba(76,175,80,0.2);
    }

    .btn {
      width: 100%;
      padding: 12px;
      background: #007bff;
      color: #fff;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .btn:hover {
      background: #0056b3;
    }

    .alert {
      padding: 12px;
      border-radius: 8px;
      margin-top: 15px;
      text-align: center;
      font-size: 14px;
    }

    .alert-warning {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }
  </style>
</head>
<body>
  <?php require "navbar.php"; ?>

  <div class="main">
    <div class="login-box">
      <form action="" method="post">
        <div>
          <label for="username">Username</label>
          <input type="text" name="username" id="username">
        </div>
        <div>
          <label for="password">Password</label>
          <input type="password" name="password" id="password">
        </div>
        <div>
          <button class="btn" type="submit" name="loginbtn">Login</button>
        </div>
      </form>
    </div>

    <div class="mt-3" style="width: 400px">
        <?php if(isset($error)) echo "<div class='alert alert-warning'>$error</div>"; ?>
    </div>
  </div>
</body>
</html>

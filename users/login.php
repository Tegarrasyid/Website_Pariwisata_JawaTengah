<?php 
session_start(); 
require "koneksi.php"; 

if (!isset($_SESSION['redirect_after_login']) && isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], 'login.php') === false) {
  $_SESSION['redirect_after_login'] = $_SERVER['HTTP_REFERER'];
}

if (isset($_SESSION['id_users'])) {
    $id_user = $_SESSION['id_users'];
    $updateLogin = "UPDATE users SET last_login = NOW() WHERE id_users = '$id_user'";
    mysqli_query($koneksi, $updateLogin);
}

?>

<!DOCTYPE html> 
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <style>
    body {
      margin: 0;
      font-family: "Poppins", Arial, sans-serif;
      background: #f5f5f5;
    }

    .alert {
      max-width: 500px;
      margin: 15px auto;
      padding: 12px;
      border-radius: 6px;
      text-align: center;
      font-size: 14px;
    }

    .alert-warning {
      background: #fff3cd;
      color: #856404;
      border: 1px solid #ffeeba;
    }

    .alert-success {
      background: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .main {
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .login-box {
      width: 100%;
      max-width: 450px;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    label {
      font-weight: 500;
      margin-bottom: 6px;
      display: block;
      color: #333;
    }

    input[type="text"], 
    input[type="password"] {
      width: 430px;
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 6px;
      border: 1px solid #ccc;
      font-size: 14px;
    }

    input:focus {
      border-color: #007bff;
      outline: none;
    }

    button {
      width: 100%;
      padding: 12px;
      border: none;
      border-radius: 6px;
      background: #007bff;
      color: white;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s;
    }

    button:hover {
      background: #0056b3;
    }

    p {
      margin-top: 15px;
      text-align: center;
      font-size: 14px;
    }

    p a {
      color: #007bff;
      text-decoration: none;
    }

    p a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>


  <?php if (!empty($_SESSION['flash_login'])): ?>
    <div class="alert alert-success">
      <?= $_SESSION['flash_login']; unset($_SESSION['flash_login']); ?>
    </div>
  <?php endif; ?>

  <div class="main">
    <div class="login-box">
      <h2>Login</h2>
      <form action="" method="post">
        <div>
          <label for="username">Email</label>
          <input type="text" name="email" required>
        </div>
        <div>
          <label for="password">Password</label>
          <input type="password" name="password" required>
        </div>
        <div>
          <button type="submit" name="loginbtn">Login</button>
        </div>
        <p>Belum Punya Akun? <a href="register.php">Register</a></p>
      </form>
      <div class="text-center"> 
        <?php
          if (isset($_POST['loginbtn'])) {
            $email = htmlspecialchars($_POST['email']);
            $password = htmlspecialchars($_POST['password']);

            $sql = "SELECT * FROM users WHERE email='$email' AND password ='$password' ";
            $query = mysqli_query($koneksi, $sql);

            if (mysqli_num_rows($query) == 1) {
              $data = mysqli_fetch_array($query);
              $_SESSION['username'] = $data['username'];
              $_SESSION['id_users']  = $data['id_users'];
              unset($_SESSION['ukuran']);
                          
              $redirect = $_SESSION['redirect_after_login'] ?? 'index.php?=sukses';
              unset($_SESSION['redirect_after_login']);
              echo '<meta http-equiv="refresh" content="0 ; url='.$redirect.'" />';
            } else {
              echo '<div class="alert alert-warning">Password Anda Salah</div>';
              echo '<meta http-equiv="refresh" content="2 ; url=login.php" />';
            }
          }
        ?>
      </div>
    </div>
  </div>
</body>
</html>

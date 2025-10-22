<?php 
session_start();
require "koneksi.php";

$success = "";
$error   = "";

// Jika tombol register ditekan
if (isset($_POST['registerbtn'])) {
    $nama     = htmlspecialchars($_POST['nama']);
    $username = htmlspecialchars($_POST['username']);
    $email    = htmlspecialchars($_POST['email']);
    $password = htmlspecialchars($_POST['password']);

    // Cek apakah email sudah ada
    $cekEmail = mysqli_query($koneksi, "SELECT * FROM users WHERE email='$email'");
    if (mysqli_num_rows($cekEmail) > 0) {
        $error = "Email sudah terdaftar, silakan gunakan email lain.";
    } else {
        // Simpan data ke database
        $sql = "INSERT INTO users (nama, username, email, password, tanggal_daftar) 
                VALUES ('$nama', '$username', '$email', '$password', CURDATE())";
        if (mysqli_query($koneksi, $sql)) {
            $success = "Pendaftaran berhasil, silakan login.";
            // Redirect otomatis setelah 2 detik ke login.php
            echo '<meta http-equiv="refresh" content="2;url=login.php">';
        } else {
            $error = "Terjadi kesalahan saat mendaftar.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Register</title>
  <style>
    body { margin: 0; font-family: "Poppins", Arial, sans-serif; background: #f5f5f5; }
    .alert {
      max-width: 500px; margin: 15px auto; padding: 12px;
      border-radius: 6px; text-align: center; font-size: 14px;
    }
    .alert-warning { background: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
    .alert-success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
    .main { height: 100vh; display: flex; justify-content: center; align-items: center; }
    .login-box {
      width: 100%; max-width: 450px; background: #fff; padding: 30px;
      border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .login-box h2 { text-align: center; margin-bottom: 20px; color: #333; }
    label { font-weight: 500; margin-bottom: 6px; display: block; color: #333; }
    input[type="text"], input[type="password"], input[type="email"] {
      width: 430px; padding: 10px; margin-bottom: 15px;
      border-radius: 6px; border: 1px solid #ccc; font-size: 14px;
    }
    input:focus { border-color: #007bff; outline: none; }
    button {
      width: 100%; padding: 12px; border: none; border-radius: 6px;
      background: #007bff; color: white; font-size: 16px;
      cursor: pointer; transition: background 0.3s;
    }
    button:hover { background: #0056b3; }
    p { margin-top: 15px; text-align: center; font-size: 14px; }
    p a { color: #007bff; text-decoration: none; }
    p a:hover { text-decoration: underline; }
  </style>
</head>
<body>

  <?php if (!empty($success)): ?>
    <div class="alert alert-success"><?= $success; ?></div>
  <?php endif; ?>

  <?php if (!empty($error)): ?>
    <div class="alert alert-warning"><?= $error; ?></div>
  <?php endif; ?>

  <div class="main">
    <div class="login-box">
      <h2>Register</h2>
      <form action="" method="post">
        <div>
          <label for="nama">Nama Lengkap</label>
          <input type="text" name="nama" required>
        </div>
        <div>
          <label for="username">Username</label>
          <input type="text" name="username" required>
        </div>
        <div>
          <label for="email">Email</label>
          <input type="email" name="email" required>
        </div>
        <div>
          <label for="password">Password</label>
          <input type="password" name="password" required>
        </div>
        <div>
          <button type="submit" name="registerbtn">Register</button>
        </div>
        <p>Sudah punya akun? <a href="login.php">Login</a></p>
      </form>
    </div>
  </div>
</body>
</html>

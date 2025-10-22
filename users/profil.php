<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['id_users'])) {
    header("Location: login.php");
    exit;
}

$id_users = $_SESSION['id_users'];

// === PROSES UPDATE PROFIL ===
if (isset($_POST['update_profil'])) {
    $nama     = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email    = mysqli_real_escape_string($koneksi, $_POST['email']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);

    // Cek apakah ada upload file
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "uploads/";
        $foto_name  = basename($_FILES["foto"]["name"]);
        $foto_tmp   = $_FILES["foto"]["tmp_name"];
        $foto_size  = $_FILES["foto"]["size"];
        $ext        = strtolower(pathinfo($foto_name, PATHINFO_EXTENSION));

        // generate nama file unik
        $random_name = bin2hex(random_bytes(10));
        $new_name    = "user_" . $id_users . "_" . $random_name . "." . $ext;
        $target_file = $target_dir . $new_name;

        // Validasi ukuran maksimal (misalnya 2 MB)
        $maxSize = 2 * 1024 * 1024; 
        if ($foto_size > $maxSize) {
            echo "<script>alert('Ukuran foto maksimal 2MB'); window.location='profil.php';</script>";
            exit;
        }

        // Validasi tipe file
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($ext, $allowed_ext)) {
            echo "<script>alert('Format foto hanya JPG, JPEG, PNG, atau GIF'); window.location='profil.php';</script>";
            exit;
        }

        // Simpan ke folder "uploads"
        if (move_uploaded_file($foto_tmp, $target_file)) {
            $sql = "UPDATE users 
                    SET nama='$nama', email='$email', username='$username', foto='$new_name' 
                    WHERE id_users='$id_users'";
        } else {
            echo "<script>alert('Upload foto gagal');</script>";
            $sql = "UPDATE users 
                    SET nama='$nama', email='$email', username='$username' 
                    WHERE id_users='$id_users'";
        }
    } else {
        // Jika tidak ada foto baru, update tanpa ubah foto
        $sql = "UPDATE users 
                SET nama='$nama', email='$email', username='$username' 
                WHERE id_users='$id_users'";
    }

    if (mysqli_query($koneksi, $sql)) {
        echo "<script>alert('Profil berhasil diperbarui'); window.location='profil.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal update profil');</script>";
    }
}



// === PROSES UPDATE PASSWORD ===
if (isset($_POST['update_password'])) {
    $password_baru       = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if ($password_baru === $konfirmasi_password) {
        $sql = "UPDATE users SET password='$password_baru' WHERE id_users='$id_users'";
        if (mysqli_query($koneksi, $sql)) {
            echo "<script>alert('Password berhasil diperbarui'); window.location='profil.php';</script>";
            exit;
        } else {
            echo "<script>alert('Gagal update password');</script>";
        }
    } else {
        echo "<script>alert('Konfirmasi password tidak sama');</script>";
    }
}

// Ambil data user terbaru
$sql = "SELECT * FROM users WHERE id_users='$id_users'";
$query = mysqli_query($koneksi, $sql);
$data = mysqli_fetch_assoc($query);
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Profil Pengguna</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../fontawesome/css/fontawesome.min.css">

  <style>
    * {margin:0; padding:0; box-sizing:border-box;}
    body {
      font-family: "Segoe UI", Arial, sans-serif;
      background:#f2f2f2;
      color:#333;
      line-height:1.6;
    }

    .container {
      max-width:1200px;
      margin:20px auto;
      padding:20px;
      display:flex;
      gap:20px;
    }

    /* Sidebar */
    .sidebar {
      flex:1;
      background:#e0e0e0;
      padding:20px;
      border-radius:10px;
      height:fit-content;
    }
    .sidebar h3 {
      margin-bottom:15px;
      font-size:18px;
      border-bottom:2px solid #bdbdbd;
      padding-bottom:8px;
    }
    .list-group a {
      display:block;
      padding:10px 12px;
      margin-bottom:8px;
      background:#d1d1d1;
      border-radius:6px;
      text-decoration:none;
      color:#000;
      transition:0.2s;
    }
    .list-group a:hover {
      background:#bdbdbd;
    }
    .sidebar .btn {
      display:block;
      margin-top:20px;
      width:100%;
      padding:10px;
      background:#bdbdbd;
      color:#000;
      border:none;
      border-radius:6px;
      cursor:pointer;
      font-weight:600;
      text-align:center;
    }
    .sidebar .btn:hover {background:#a9a9a9;}

    /* Main content */
    .main {
      flex:3;
      background:#e0e0e0;
      padding:20px;
      border-radius:10px;
    }
    .main h3 {
      font-size:20px;
      margin-bottom:20px;
      display:flex;
      justify-content:space-between;
      align-items:center;
    }
    .main button.edit-btn {
      background:#bdbdbd;
      border:none;
      padding:6px 12px;
      border-radius:5px;
      cursor:pointer;
      font-size:14px;
    }
    .main button.edit-btn:hover {background:#a9a9a9;}

    .card-body {
      display:flex;
      align-items:flex-start;
      gap:20px;
    }

    /* Avatar */
    .avatar {
      width:180px;
      height:180px;
      border-radius:50%;
      background:#c1d9f0;
      display:flex;
      justify-content:center;
      align-items:center;
      flex-shrink:0;
    }
    .avatar img {
      width:180px;
      height:180px;
      border-radius:50%;
    }

    /* Data info */
    .data-info {
      flex:1;
    }
    .row {
      display:flex;
      margin-bottom:10px;
    }
    .row .col-5 {flex:0 0 40%; font-weight:600;}
    .row .col-7 {flex:1;}

    /* Modal */
    .modal {
      display:none;
      position:fixed;
      top:0; left:0;
      width:100%; height:100%;
      background:rgba(0,0,0,0.5);
      justify-content:center;
      align-items:center;
      z-index:1000;
    }
    .modal-content {
      background:#fff;
      padding:20px;
      border-radius:10px;
      width:90%;
      max-width:500px;
      position:relative;
      animation:fadeIn 0.3s ease;
    }
    .modal-header {
      display:flex;
      justify-content:space-between;
      align-items:center;
      margin-bottom:15px;
    }
    .modal-header h5 {margin:0;}
    .close {
      background:none;
      border:none;
      font-size:20px;
      cursor:pointer;
    }
    .modal form input {
      width:100%;
      padding:10px;
      margin-bottom:15px;
      border:1px solid #ccc;
      border-radius:6px;
    }
    .modal .btn {
      padding:10px 15px;
      border:none;
      border-radius:6px;
      cursor:pointer;
      font-weight:600;
    }
    .btn-secondary {background:#ccc; color:#333;}
    .btn-primary {background:#007bff; color:#fff;}
    .btn-primary:hover {background:#0056b3;}

    @keyframes fadeIn {
      from {opacity:0; transform:translateY(-20px);}
      to {opacity:1; transform:translateY(0);}
    }
  </style>
</head>
<body>
<?php require "navbar.php"; ?>

<div class="container">
  <!-- Sidebar -->
  <div class="sidebar">
    <h3>Menu</h3>
    <div class="list-group">
      <a href="profil.php"><i class="fa-solid fa-circle-user"></i> Profil</a>
      <a href="tempatf.php"><i class="fa-regular fa-heart"></i> Tempat Favorite</a>
      <a href="logout.php" id="logout-profil"><i class="fa-solid fa-arrow-right-from-bracket"></i> Logout</a>
    </div>
    <button class="btn" onclick="openModal('editPwModal')">Edit Password</button>
  </div>

  <div class="main">
    <h3 style="display: flex; align-items: center; justify-content: space-between;">
      <span>
        <i class="fa-solid fa-circle-user" style="margin-right: 8px;"></i>
        Data Diri
      </span>
      <button class="edit-btn" onclick="openModal('editModal')">
        ✏️ Edit Profil
      </button>
    </h3>

    <div class="card-body">
      <!-- Profil -->
      <div class="avatar">
        <?php if (!empty($data['foto'])): ?>
          <img src="uploads/<?php echo $data['foto']; ?>" alt="User">
        <?php else: ?>
          <img src="../aset/profil1.png" alt="User">
        <?php endif; ?>
      </div>

      <!-- Data -->
      <div class="data-info">
        <div class="row">
          <div class="col-5">Nama Lengkap</div><div class="col-7">: <?php echo $data['nama']; ?></div>
        </div>
        <div class="row">
          <div class="col-5">Email</div><div class="col-7">: <?php echo $data['email']; ?></div>
        </div>
        <div class="row">
          <div class="col-5">Username</div><div class="col-7">: <?php echo $data['username']; ?></div>
        </div>
        <div class="row">
          <div class="col-5">Password</div><div class="col-7">: *********</div>
        </div>
        <div class="row">
          <div class="col-5">Tanggal Daftar Akun</div><div class="col-7">: <?php echo date("d F Y", strtotime($data['tanggal_daftar'])); ?></div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal Edit Profil -->
<div class="modal" id="editModal">
  <div class="modal-content">
    <div class="modal-header">
      <h5>Edit Profil</h5>
      <button class="close" onclick="closeModal('editModal')">&times;</button>
    </div>
    <form action="" method="POST" enctype="multipart/form-data">
      <input type="text" name="nama" value="<?php echo $data['nama']; ?>" required>
      <input type="email" name="email" value="<?php echo $data['email']; ?>" required>
      <input type="text" name="username" value="<?php echo $data['username']; ?>" required>
      <input type="file" name="foto" accept="image/*">
      <div style="text-align:right;">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Batal</button>
        <button type="submit" class="btn btn-primary" name="update_profil">Simpan</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal Edit Password -->
<div class="modal" id="editPwModal">
  <div class="modal-content">
    <div class="modal-header">
      <h5>Edit Password</h5>
      <button class="close" onclick="closeModal('editPwModal')">&times;</button>
    </div>
    <form action="" method="POST">
      <input type="password" name="password" value="<?php echo $data['password']; ?>" readonly>
      <input type="password" name="password_baru" placeholder="Password Baru" required>
      <input type="password" name="konfirmasi_password" placeholder="Konfirmasi Password" required>
      <div style="text-align:right;">
        <button type="button" class="btn btn-secondary" onclick="closeModal('editPwModal')">Batal</button>
        <button type="submit" class="btn btn-primary" name="update_password">Simpan</button>
      </div>
    </form>
  </div>
</div>

<?php require "footer.php"; ?>

<script>
  function openModal(id){ document.getElementById(id).style.display='flex'; }
  function closeModal(id){ document.getElementById(id).style.display='none'; }

  document.getElementById("logout-profil").addEventListener("click", function(e){
    e.preventDefault();
    localStorage.removeItem("isLoggedIn");
    window.location.href = "logout.php";
  });
</script>
<script src="../fontawesome/js/all.min.js"></script>

</body>
</html>

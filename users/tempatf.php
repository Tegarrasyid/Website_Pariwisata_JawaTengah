<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['id_users'])) {
    header("Location: login.php");
    exit;
}

$id_users = $_SESSION['id_users'];

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

    .main h2 {
      font-size:20px;
      margin-bottom:20px;
      display:flex;
      align-items:center;
      gap:8px;
    }

    .grid {
      display:grid;
      grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
      gap:20px;
    }

    .card {
      background:#bdbdbd;
      border-radius:8px;
      overflow:hidden;
      text-align:center;
      padding-bottom:10px;
    }

    .card img {
      width:100%;
      height:150px;
      object-fit:cover;
    }

    .card-body {
      padding:10px;
    }

    .card-body h4 {
      margin:8px 0 4px;
      font-size:16px;
    }

    .card-body p {
      margin:0 0 10px;
      font-size:14px;
      color:#555;
    }

    .actions {
      display:flex;
      justify-content:center;
      align-items:center;
      gap:10px;
    }

    .actions button {
      padding:6px 12px;
      border:none;
      background:#3498db;
      color:white;
      border-radius:4px;
      cursor:pointer;
    }

    .love-btn {
      font-size:18px;
      color:#888;
      cursor:pointer;
    }

    .love-btn.active {
      color:red;
    }

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

  <!-- Main Content -->
  <div class="main">
    <h2><i class="fa-regular fa-heart"></i> Tempat Favorite Saya</h2>

    <div class="grid">
      <?php
      // Ambil tempat favorit user
      $sqlFav = "
        SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar
        FROM favorite f
        JOIN wisata w ON f.id_wisata = w.id_wisata
        WHERE f.id_users = '$id_users'
      ";
      $queryFav = mysqli_query($koneksi, $sqlFav);

      if (mysqli_num_rows($queryFav) > 0):
        while ($row = mysqli_fetch_assoc($queryFav)): ?>
          <div class="card">
            <img src="../image/<?php echo htmlspecialchars($row['gambar']); ?>" alt="">
            <div class="card-body">
              <h4><?php echo htmlspecialchars($row['nama_wisata']); ?></h4>
              <p><?php echo htmlspecialchars($row['lokasi']); ?></p>
              <div class="actions">
                <a href="wisata_detail.php?nama=<?php echo urlencode($row['nama_wisata']); ?>">
                  <button>Detail</button>
                </a>
                <span class="love-btn active">♥</span>
              </div>
            </div>
          </div>
        <?php endwhile;
      else: ?>
        <p>Belum ada tempat favorit.</p>
      <?php endif; ?>
    </div>
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

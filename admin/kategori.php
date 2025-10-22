<?php
  require "koneksi.php";
  require "session.php";

$querykategori = mysqli_query($koneksi, "SELECT * FROM kategori");
$jumlahkategori = mysqli_num_rows($querykategori);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kategori</title>
  <link rel="stylesheet" href="../fontawesome/css/all.min.css">
  <style>
    body { font-family: Arial, sans-serif; background: #f9f9f9; }
    .container { max-width: 1200px; margin: 40px auto; padding: 20px; background: #fff; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.08); }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    table th, table td { border: 1px solid #202020ff; padding: 12px; text-align: center; }
    table th { background: #57a7e7ff; }
    form {
        background: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    label {
        display: block;
        font-weight: bold;
        margin-bottom: 8px;
    }

    input[type="text"],
    input[type="file"] {
        width: 100%;
        padding: 10px;
        border-radius: 6px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    .btn { padding: 6px 12px; border-radius: 6px; text-decoration: none; border: none; color: #fff; }
    .btn-primary { background: #007bff; }
    .btn-danger { background: #dc3545; }
    .btn-primary:hover { background: #0056b3; }
    .btn-danger:hover { background: #a71d2a; }
    .alert { margin-top: 15px; padding: 10px; border-radius: 6px; }
    .alert-warning { background: #fff3cd; color: #856404; }
    .alert-primary { background: #cce5ff; color: #004085; }
    img.thumb { width: 80px; height: 60px; object-fit: cover; border-radius: 6px; }
  </style>
</head>
<body>
<?php require "navbar.php"; ?>

<div class="container">
  <h2>Tambah Kategori</h2>

  <form action="" method="post" enctype="multipart/form-data">
    <div>
      <label for="kategori">Kategori</label>
      <input type="text" name="kategori" id="kategori" placeholder="Input Nama Kategori" required>
    </div>
    <div style="margin-top:15px;">
      <label for="foto">Upload Foto</label>
      <input type="file" name="foto" id="foto" accept="image/*" required>
    </div>
    <div style="margin-top:15px;">
      <button class="btn btn-primary" type="submit" name="tambahKategori">Tambah</button>
    </div>
  </form>

  <!-- Proses Tambah Kategori -->
  <?php
  if(isset($_POST['tambahKategori'])){
      $kategori = htmlspecialchars($_POST['kategori']);

      // Cek kategori sudah ada
      $queryCek = mysqli_query($koneksi, "SELECT nama_kategori FROM kategori WHERE nama_kategori='$kategori'");
      $jumlahdatakategori = mysqli_num_rows($queryCek);

      if($jumlahdatakategori > 0){
          echo '<div class="alert alert-warning">Kategori Sudah Ada</div>';
      } else {
          // Upload foto
          $foto = $_FILES['foto']['name'];
          $tmp_name = $_FILES['foto']['tmp_name'];
          $size = $_FILES['foto']['size'];
          $allowed_ext = ['jpg','jpeg','png','gif'];
          $ext = strtolower(pathinfo($foto, PATHINFO_EXTENSION));

          if(!in_array($ext, $allowed_ext)){
              echo '<div class="alert alert-warning">Format file harus JPG, PNG, atau GIF</div>';
          } elseif($size > 2*1024*1024){
              echo '<div class="alert alert-warning">Ukuran file maksimal 2MB</div>';
          } else {
              $newname = time()."_".$foto;
              $uploadPath = "../image/".$newname;

              if(move_uploaded_file($tmp_name, $uploadPath)){
                  $querySimpan = mysqli_query($koneksi, "INSERT INTO kategori (nama_kategori, foto) VALUES ('$kategori', '$newname')");

                  if($querySimpan){
                      echo '<div class="alert alert-primary">Kategori Berhasil di Tambah</div>';
                      echo '<meta http-equiv="refresh" content="1; url=kategori.php" />';
                  } else {
                      echo mysqli_error($koneksi);
                  }
              } else {
                  echo '<div class="alert alert-warning">Gagal upload foto</div>';
              }
          }
      }
  }
  ?>
</div>

<div class="container">
  <h2>List Kategori</h2>
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Kategori</th>
        <th>Foto</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      <?php
      if($jumlahkategori==0){
          echo '<tr><td colspan="4">Tidak ada data Kategori</td></tr>';
      } else {
          $jumlah=1;
          while($data = mysqli_fetch_array($querykategori)){
              echo "
              <tr>
                <td>$jumlah</td>
                <td>{$data['nama_kategori']}</td>
                <td>";
                if(!empty($data['foto'])){
                    echo "<img src='../image/{$data['foto']}' class='thumb'>";
                } else {
                    echo "-";
                }
              echo "</td>
                <td>
                  <a href='ek.php?id={$data['id_kategori']}' class='btn btn-primary'>Edit</a>
                  <a href='hk.php?id={$data['id_kategori']}' class='btn btn-danger'>Hapus</a>
                </td>
              </tr>
              ";
              $jumlah++;
          }
      }
      ?>
    </tbody>
  </table>
</div>

<script src="../fontawesome/fontawesome/js/all.min.js"></script>
</body>
</html>

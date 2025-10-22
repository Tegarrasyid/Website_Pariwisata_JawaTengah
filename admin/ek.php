<?php
    require "koneksi.php";
    require "session.php";

$id_kategori = $_GET['id'];
$sql = "SELECT * FROM kategori WHERE id_kategori = '$id_kategori' ";
$query = mysqli_query($koneksi, $sql);
while ($data = mysqli_fetch_assoc($query)) { 
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Kategori</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f6f9; }
    .container { width: 90%; max-width: 600px; margin: 50px auto; background: #fff; padding: 20px 25px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
    h2 { margin-bottom: 20px; color: #333; text-align: center; }
    label { display: block; margin-bottom: 8px; font-weight: bold; color: #444; }
    input[type="text"], input[type="file"] { width: 100%; padding: 10px 12px; border: 1px solid #ccc; border-radius: 6px; font-size: 14px; margin-bottom: 15px; }
    input:focus { border-color: #007bff; outline: none; }
    .btn { padding: 10px 18px; border-radius: 6px; border: none; cursor: pointer; font-size: 14px; font-weight: bold; }
    .btn-primary { background: #007bff; color: #fff; }
    .btn-primary:hover { background: #0056b3; }
    .alert { padding: 12px 16px; border-radius: 6px; margin-top: 15px; font-size: 14px; }
    .alert-warning { background: #fff3cd; border: 1px solid #ffeeba; color: #856404; }
    .alert-primary { background: #cce5ff; border: 1px solid #b8daff; color: #004085; }
    img.thumb { width: 120px; height: 90px; object-fit: cover; border-radius: 6px; display: block; margin-bottom: 10px; }
  </style>
</head>
<body>
<?php require "navbar.php"; ?>

<div class="container">
  <h2>Edit Kategori</h2>
  <form action="" method="post" enctype="multipart/form-data">
    <label for="kategori">Kategori</label>
    <input type="text" name="kategori" id="kategori" value="<?php echo $data['nama_kategori']; ?>">

    <label>Foto Lama</label>
    <?php if(!empty($data['foto'])): ?>
      <img src="../image/<?php echo $data['foto']; ?>" class="thumb">
    <?php else: ?>
      <p>- Belum ada foto -</p>
    <?php endif; ?>

    <label for="foto">Ganti Foto (opsional)</label>
    <input type="file" name="foto" id="foto" accept="image/*">

    <button class="btn btn-primary" type="submit" name="editKategori">Edit</button>
  </form>

  <?php 
  if(isset($_POST['editKategori'])){
      $kategori = htmlspecialchars($_POST['kategori']);
      $fotoBaru = $_FILES['foto']['name'];

      if($data['nama_kategori']==$kategori && empty($fotoBaru)){
          echo "<meta http-equiv='refresh' content='0; url=kategori.php' />";
      } else {
          // cek duplikat nama kategori
          $query = mysqli_query($koneksi, "SELECT * FROM kategori WHERE nama_kategori='$kategori' AND id_kategori != '$id_kategori'");
          $jumlahData = mysqli_num_rows($query);

          if($jumlahData > 0 ){
              echo "<div class='alert alert-warning'>Kategori sudah ada.</div>";
          } else {
              $updateQuery = "UPDATE kategori SET nama_kategori='$kategori'";

              // kalau ada upload foto baru
              if(!empty($fotoBaru)){
                  $tmp_name = $_FILES['foto']['tmp_name'];
                  $size = $_FILES['foto']['size'];
                  $allowed_ext = ['jpg','jpeg','png','gif'];
                  $ext = strtolower(pathinfo($fotoBaru, PATHINFO_EXTENSION));

                  if(!in_array($ext, $allowed_ext)){
                      echo "<div class='alert alert-warning'>Format file harus JPG, PNG, atau GIF</div>";
                      exit;
                  } elseif($size > 100*1024*1024){
                      echo "<div class='alert alert-warning'>Ukuran file maksimal 2MB</div>";
                      exit;
                  } else {
                      $newname = time()."_".$fotoBaru;
                      $uploadPath = "../image/".$newname;

                      if(move_uploaded_file($tmp_name, $uploadPath)){
                          // hapus foto lama kalau ada
                          if(!empty($data['foto']) && file_exists("../image/".$data['foto'])){
                              unlink("../image/".$data['foto']);
                          }
                          $updateQuery .= ", foto='$newname'";
                      }
                  }
              }

              $updateQuery .= " WHERE id_kategori='$id_kategori'";
              $querySimpan = mysqli_query($koneksi, $updateQuery);

              if($querySimpan){
                  echo "<div class='alert alert-primary'>Kategori berhasil diedit.</div>";
                  echo "<meta http-equiv='refresh' content='2; url=kategori.php' />";
              } else {
                  echo mysqli_error($koneksi);
              }
          }
      }
  }
  ?>
</div>
</body>
</html>

<?php } ?>

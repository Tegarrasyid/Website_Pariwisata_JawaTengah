<?php
    require "koneksi.php";
    require "session.php";

$sql = "SELECT a.*, b.nama_kategori FROM wisata a JOIN kategori b ON a.id_kategori = b.id_kategori";

if (isset($_GET['kategori']) && $_GET['kategori'] !== "") {
    $kat = mysqli_real_escape_string($koneksi, $_GET['kategori']);
    $sql .= " WHERE a.id_kategori = '$kat'";
}

$sql .= " ORDER BY a.id_wisata DESC";

$querywisata   = mysqli_query($koneksi, $sql);
$jumlahwisata  = mysqli_num_rows($querywisata);

$querykategori = mysqli_query($koneksi, "SELECT * FROM kategori");

function generateRandomString($length = 10){
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++){
        $randomString .= $characters[rand(0, $charactersLength -1)];
    }
    return $randomString;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>wisata</title>
  <link rel="stylesheet" href="../fontawesome/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.ckeditor.com/ckeditor5/44.3.0/ckeditor5.css" />
  
  <style>
    body {
      font-family: 'Inter', sans-serif;
      margin: 0;
      padding: 0;
      background: #f4f4f9;
      color: #333;
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      padding: 20px;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }
    h2 {
      margin-bottom: 20px;
      font-size: 24px;
      color: #222;
    }
    form div {
      margin-bottom: 15px;
    }
    /* buat grid 2 kolom */
    .form-grid {
      display: flex;
      gap: 20px;
      flex-wrap: wrap; /* biar responsif */
    }

    .form-left, 
    .form-right {
      flex: 1; /* otomatis bagi lebar */
      min-width: 320px; /* biar nggak kepotong pas layar kecil */
    }

    .form-left input,
    .form-left select,
    .form-right input,
    .form-right textarea {
      width: 100%;
    }

    label {
      display: block;
      font-weight: 600;
      margin-bottom: 5px;
    }
    input[type="text"],
    input[type="number"],
    input[type="file"],
    select {
      width: 570px;
      padding: 10px;
      border: 1px solid #bbb;
      border-radius: 6px;
      font-size: 14px;
    }
    textarea {
      resize: vertical;
      width: 450px;
      height: 195px;
      border: 1px solid #bbb;
      border-radius: 6px;
      font-size: 14px;
    }
    .btn {
      display: inline-block;
      padding: 10px 18px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-size: 14px;
      transition: 0.2s ease;
    }
    .btn-primary {
      background: #007bff;
      color: white;
      text-decoration: none;
    }
    .btn-primary:hover { background: #0056b3; }
    .btn-danger {
      background: #dc3545;
      color: white;
      text-decoration: none;
    }
    .btn-danger:hover { background: #a71d2a; }

    .breadcrumb {
        font-size: 14px;
        margin-bottom: 20px;
    }

    .breadcrumb a {
        text-decoration: none;
        color: #000000ff;
    }

    .breadcrumb a:hover {
        color: #007bff;
    }

    .alert {
      padding: 12px 15px;
      border-radius: 6px;
      margin-top: 15px;
      font-size: 14px;
    }
    .alert-warning { background: #fff3cd; color: #856404; }
    .alert-primary { background: #cce5ff; color: #004085; }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      font-size: 14px;
      background: white;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }
    table th, table td {
        border: 1px solid #202020ff;
        padding: 12px;
        text-align: center;
    }
    table th {
        background: #57a7e7ff;
    }
    .d-flex {
      display: flex;
      align-items: center;
      gap: 10px;
    }
    .justify-content-between {
      display: flex;
      justify-content: space-between;
      align-items: center;
    }
  </style>
</head>
<body>
  <?php require "navbar.php"; ?>

  <div class="container">
    <nav class="breadcrumb">
        <a href="../admin" class="no-decor text-muted">
            <i class="fas fa-house-chimney"></i> Home
        </a> / Wisata
    </nav>

    <h2>Tambah wisata</h2>
    <form action="" method="post" enctype="multipart/form-data" class="form-grid">
      <div class="form-left">
        <div>
          <label for="nama_wisata">Nama Tempat</label>
          <input type="text" name="nama_wisata" id="nama_wisata" autocomplete="off">
        </div>
        <div>
          <label for="kategori">Kategori</label>
          <select name="kategori" id="kategori">
            <option value="">Pilih Kategori</option>
            <?php while($data=mysqli_fetch_array($querykategori)){ ?>
              <option value="<?php echo $data['id_kategori']; ?>"><?php echo $data['nama_kategori']; ?></option>
            <?php } ?>
          </select> 
        </div>
        <div>
          <label for="lokasi">Lokasi</label>
          <input type="text" name="lokasi" id="lokasi">
        </div>
        <div>
          <label for="gambar">Upload Gambar</label>
          <input type="file" name="gambar" id="gambar">
        </div>
            <div>
      <button class="btn btn-primary" type="submit" name="simpan">Simpan</button>
    </div>
      </div>

      <div class="form-right">
        <div>
          <label for="deskripsi">Deskripsi Wisata</label>
          <textarea name="deskripsi" id="deskripsi" ></textarea>
        </div>
      </div>
    </form>

    <!-- proses tambah wisata -->
    <?php
      if(isset($_POST['simpan'])){
        $nama = htmlspecialchars($_POST['nama_wisata']);
        $kategori = htmlspecialchars($_POST['kategori']);
        $lokasi = htmlspecialchars($_POST['lokasi']);
        $deskripsi = htmlspecialchars($_POST['deskripsi']);

        $target_dir = "../image/";
        $nama_file =  basename($_FILES["gambar"]["name"]);
        $target_file = $target_dir . $nama_file;
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        $image_size = $_FILES["gambar"]["size"];
        $random_name = generateRandomString(20);
        $baru = $random_name . "." . $imageFileType;

        if($nama=='' || $lokasi==''){
            echo '<div class="alert alert-warning">Nama, lokasi wajib diisi</div>';
        }else{
            if($nama_file!=''){
                if($image_size > 5000000){
                    echo '<div class="alert alert-warning">gambar tidak boleh lebih dari 5MB</div>';
                }else{
                    if($imageFileType != 'jpg' && $imageFileType != 'png' && $imageFileType != 'gif'){
                        echo '<div class="alert alert-warning">gambar wajib bertipe jpg, png, atau gif</div>';
                    }else{
                        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $baru);
                    }
                }
            }

            $querytambah = mysqli_query($koneksi, "INSERT INTO wisata (id_kategori, nama_wisata, lokasi, gambar, deskripsi) 
            VALUES ('$kategori', '$nama', '$lokasi', '$baru', '$deskripsi')");

            if($querytambah){
                echo '<div class="alert alert-primary">wisata Berhasil di Tambah</div>';
                echo '<meta http-equiv="refresh" content="1 ; url=wisata.php" />';
            }else{
                echo mysqli_error($koneksi);
            }
        }
      }
    ?>




    <div class="mt-3 mb-6">
      <div class="justify-content">
        <h2>List Tempat Wisata</h2>
        <form method="GET" action="" class="d-flex">
          <select name="kategori">
            <option value="">-- Semua Kategori --</option>
            <?php
              $query_kategori = mysqli_query($koneksi, "SELECT * FROM kategori");
              while ($kategori = mysqli_fetch_array($query_kategori)) {
                $selected = (isset($_GET['kategori']) && $_GET['kategori'] == $kategori['id_kategori']) ? 'selected' : '';
                echo "<option value='".$kategori['id_kategori']."' $selected>".$kategori['nama_kategori']."</option>";
              }
            ?>
          </select>
          <button type="submit" class="btn btn-primary">Cari</button>
        </form>
      </div>

      <table>
        <thead>
          <tr>
            <th>Id</th>
            <th>Nama Tempat</th>
            <th>Kategori</th>
            <th>Lokasi</th>
            <!-- <th>Jumlah Favorite</th> -->
            <th>Gambar wisata</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if($jumlahwisata==0){
              echo '<tr><td colspan="8">Tidak ada data wisata</td></tr>';
          }else{
              $jumlah=1;
              while($data = mysqli_fetch_array($querywisata)){
                  echo "<tr>
                      <td>".$jumlah."</td>
                      <td>".$data['nama_wisata']."</td>
                      <td>".$data['nama_kategori']."</td>
                      <td>".$data['lokasi']."</td>
                      <td><img src='../image/".$data['gambar']."' width='100'></td>
                      <td>
                        <a href='ew.php?id=".$data['id_wisata']."' class='btn btn-primary'>Edit</a>
                        <a href='hw.php?id=".$data['id_wisata']."' class='btn btn-danger'>Hapus</a>
                      </td>
                  </tr>";
                  $jumlah++;
              }
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script src="https://cdn.ckeditor.com/ckeditor5/44.3.0/classic/ckeditor.js"></script>

  <script>
    ClassicEditor.create(document.querySelector('#deskripsi')).catch(error => { console.error(error); });
  </script>
</body>
</html>

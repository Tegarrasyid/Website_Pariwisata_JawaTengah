<?php
    require "koneksi.php";
    require "session.php";

$id_wisata = $_GET['id'];
$sql = "SELECT a.*, b.nama_kategori FROM wisata a JOIN kategori b ON a.id_kategori=b.id_kategori WHERE a.id_wisata = '$id_wisata' ";
$query = mysqli_query($koneksi, $sql);
while ($data = mysqli_fetch_assoc($query)) { 

$querykategori = mysqli_query($koneksi, "SELECT * FROM kategori WHERE id_kategori!='$data[id_kategori]'");

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
<title>Edit Wisata</title>
<link rel="stylesheet" href="../fontawesome/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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
    label {
        display: block;
        font-weight: 600;
        margin-bottom: 5px;
    }
    input[type="text"],
    input[type="number"],
    input[type="file"],
    select,
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #bbb;
        border-radius: 6px;
        font-size: 14px;
    }
    textarea {
        resize: vertical;
        height: 180px;
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
    }
    .btn-primary:hover { background: #0056b3; }
    .alert {
        padding: 12px 15px;
        border-radius: 6px;
        margin-top: 15px;
        font-size: 14px;
    }
    .alert-warning { background: #fff3cd; color: #856404; }
    .alert-primary { background: #cce5ff; color: #004085; }

    /* grid dua kolom */
    .form-grid {
        display: flex;
        gap: 20px;
        flex-wrap: wrap;
    }
    .form-left, .form-right {
        flex: 1;
        min-width: 300px;
    }

    /* responsive untuk layar kecil */
    @media (max-width: 768px) {
        .form-left, .form-right {
            flex: 100%;
        }
        img {
            max-width: 100%;
            height: auto;
        }
    }
</style>
</head>
<body>
<?php require "navbar.php"; ?>

<div class="container">
<h2>Edit Wisata</h2>
<form action="" method="post" enctype="multipart/form-data" class="form-grid">

    <div class="form-left">
        <div>
            <label for="nama_wisata">Nama Wisata</label>
            <input type="text" name="nama_wisata" id="nama_wisata" value="<?php echo $data['nama_wisata']; ?>" autocomplete="off">
        </div>

        <div>
            <label for="kategori">Kategori</label>
            <select name="kategori" id="kategori">
                <option value=""><?php echo $data['nama_kategori']; ?></option>
                <?php while($dataKategori=mysqli_fetch_array($querykategori)) { ?>
                    <option value="<?php echo $dataKategori['id_kategori']; ?>"><?php echo $dataKategori['nama_kategori']; ?></option>
                <?php } ?>
            </select>
        </div>

        <div>
            <label for="lokasi">Lokasi</label>
            <input type="text" name="lokasi" id="lokasi" value="<?php echo $data['lokasi']; ?>">
        </div>

        <div>
            <label>Gambar Wisata Sekarang</label><br>
            <img src="../image/<?php echo $data['gambar']; ?>" alt="gambar wisata" width="350">
        </div>

        <div>
            <label for="gambar">Ganti Gambar</label>
            <input type="file" name="gambar" id="gambar">
        </div>

        <div>
            <button class="btn btn-primary" type="submit" name="editwisata">Edit</button>
        </div>
    </div>

    <div class="form-right">
        <div>
            <label for="deskripsi">Deskripsi Wisata</label>
            <textarea name="deskripsi" id="deskripsi"><?php echo trim($data['deskripsi']); ?></textarea>
        </div>
    </div>

</form>

<?php
// proses edit sama seperti sebelumnya
if(isset($_POST['editwisata'])){
    $nama = htmlspecialchars($_POST['nama_wisata']);
    $lokasi = htmlspecialchars($_POST['lokasi']);
    $deskripsi = htmlspecialchars($_POST['deskripsi']);
    $kategori = htmlspecialchars($_POST['kategori']); // tambahkan ini

    $target_dir = "../image/";
    $nama_file =  basename($_FILES["gambar"]["name"]);
    $target_file = $target_dir . $nama_file;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $image_size = $_FILES["gambar"]["size"];
    $random_name = generateRandomString(20);
    $baru = $random_name . "." . $imageFileType;

    if($nama=='' || $lokasi=='' || $kategori==''){
        echo '<div class="alert alert-warning">Nama, kategori, dan lokasi wajib diisi</div>';
    } else {
        // 🔹 Update semua kolom termasuk kategori
        $queryUpdate = mysqli_query($koneksi, "
            UPDATE wisata 
            SET nama_wisata='$nama', 
                lokasi='$lokasi', 
                deskripsi='$deskripsi',
                id_kategori='$kategori'
            WHERE id_wisata='$id_wisata'
        ");

        if($queryUpdate) {
            echo '<div class="alert alert-primary">Wisata berhasil di edit</div>';
        } else {
            echo '<div class="alert alert-warning">Gagal update: '.mysqli_error($koneksi).'</div>';
        }

        // 🔹 Jika user upload gambar baru
        if($nama_file!=''){
            if($image_size > 5000000){
                echo '<div class="alert alert-warning">Gambar terlalu besar</div>';
            } else if(!in_array($imageFileType, ['jpg','png','gif','jpeg'])){
                echo '<div class="alert alert-warning">Gambar harus jpg, png, atau gif</div>';
            } else {
                move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $baru);
                $queryUpdateGambar = mysqli_query($koneksi, "
                    UPDATE wisata SET gambar='$baru' WHERE id_wisata='$id_wisata'
                ");
                if($queryUpdateGambar) {
                    echo '<div class="alert alert-primary">Gambar berhasil di update</div>';
                }
            }
        }
    }
}
?>
</div>

<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('#kategori').select2();
    });
</script>

</body>
</html>
<?php } ?>

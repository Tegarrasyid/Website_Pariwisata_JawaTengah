<?php
    require "koneksi.php";
    require "session.php";

$id_kategori = $_GET['id'];

$queryCheck = mysqli_query($koneksi, "SELECT * FROM wisata WHERE id_kategori='$id_kategori'");
$dataCount = mysqli_num_rows($queryCheck);

if($dataCount>0){
    header("location:kategori.php?hapus=gagal-karena-sudah-digunakan-diwisata");
    die();
}

$sql = "DELETE FROM kategori WHERE id_kategori = '$id_kategori' ";
$query = mysqli_query($koneksi, $sql);

 

if($query){
    ?>
    <div class="alert alert-primary mt-3" role="alert">
        Kategori Berhasil di Hapus
    </div>

    <meta http-equiv="refresh" content="0 ; url=kategori.php" />
    <?php
}else{
    echo mysqli_error($koneksi);
}

if ($query) {
    header("location:kategori.php?hapus=sukses");
    exit;
    } else {
    header("location:kategori.php?hapus=gagal");
    }
?>

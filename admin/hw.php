<?php
    require "koneksi.php";
    require "session.php";

$id_wisata = $_GET['id'];
$sql = "DELETE FROM wisata WHERE id_wisata = '$id_wisata' ";
$query = mysqli_query($koneksi, $sql);

if($query){
    ?>
    <div class="alert alert-primary mt-3" role="alert">
        wisata Berhasil di Hapus
    </div>

    <meta http-equiv="refresh" content="2 ; url=wisata.php" />
    <?php
}else{
    echo mysqli_error($koneksi);
} 

if ($query) {
    header("location:wisata.php?hapus=sukses");
    exit;
    } else {
    header("location:wisata.php?hapus=gagal");
    }
?>

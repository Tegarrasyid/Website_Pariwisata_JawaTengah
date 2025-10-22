<?php
session_start();
require "koneksi.php";

if (!isset($_SESSION['id_users'])) {
    echo "Harus login dulu";
    exit;
}

$id_users = $_SESSION['id_users'];
$id_wisata = $_POST['id_wisata'];

// cek apakah sudah ada di favorite
$check = mysqli_query($koneksi, "SELECT * FROM favorite WHERE id_users='$id_users' AND id_wisata='$id_wisata'");

if (mysqli_num_rows($check) > 0) {
    // sudah ada → hapus
    mysqli_query($koneksi, "DELETE FROM favorite WHERE id_users='$id_users' AND id_wisata='$id_wisata'");
    echo "removed";
} else {
    // belum ada → tambahkan
    mysqli_query($koneksi, "INSERT INTO favorite (id_users, id_wisata, tambah_favorite) VALUES ('$id_users', '$id_wisata', NOW())");
    echo "added";
}
?>

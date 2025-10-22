<?php
    require "koneksi.php";
    require "session.php";

$id_users = $_GET['id'];

// cek terakhir login
$queryCheck = mysqli_query($koneksi, "SELECT last_login FROM users WHERE id_users='$id_users'");
$data = mysqli_fetch_assoc($queryCheck);

if($data){
    $lastLogin = $data['last_login'];
    
    // hitung selisih waktu
    $sixMonthsAgo = date('Y-m-d H:i:s', strtotime('-6 months'));
    
    if($lastLogin !== null && $lastLogin > $sixMonthsAgo){
        header("location:users.php?hapus=gagal-karena-masih-aktif");
        exit;
    }
}

// kalau lolos, baru hapus
$sql = "DELETE FROM users WHERE id_users = '$id_users'";
$query = mysqli_query($koneksi, $sql);

if ($query) {
    header("location:users.php?hapus=sukses");
} else {
    header("location:users.php?hapus=gagal");
}

?>

<?php

session_start();
include 'koneksi.php';

if (isset($_SESSION['id_users'])) {
    $id_user = $_SESSION['id_users'];

    $updateLogout = "UPDATE users SET last_logout = NOW() WHERE id_users = '$id_user'";
    mysqli_query($koneksi, $updateLogout);
}

session_destroy();
header("Location: login.php");
exit;


?>
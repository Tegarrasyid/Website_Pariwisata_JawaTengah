<?php
    require "koneksi.php";
    require "session.php";

    $queryusers = mysqli_query($koneksi, "SELECT * FROM users");
    $jumlahusers = mysqli_num_rows($queryusers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Users</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }

        .container {
        max-width: 1200px;
        margin: 40px auto;
        padding: 20px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        }

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

        h2 {
            margin: 20px 0 10px;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 8px 15px;
            font-size: 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            text-decoration: none;
            color: white;
        }

        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #a71d2a; }

        .alert {
            margin-top: 15px;
            padding: 10px;
            border-radius: 6px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
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

        .no-decor {
            text-decoration: none;
        }
    </style>
</head>

<body>
    <?php require "navbar.php"; ?>

    <div class="container">
        <nav class="breadcrumb">
            <a href="../admin" class="no-decor text-muted">
                <i class="fas fa-house-chimney"></i> Home
            </a> / Users
        </nav>
        <div>
            <h2>List Users</h2>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Username</th>
                            <th>Tanggal Pembuatan Akun</th>
                            <th>Terakhir Aktif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if($jumlahusers==0){
                                echo '<tr><td colspan="4">Tidak ada data users</td></tr>';
                            }else{
                                $jumlah=1;
                                while($data = mysqli_fetch_array($queryusers)){
                                    echo "
                                        <tr>
                                            <td>$jumlah</td>
                                            <td>{$data['username']}</td>
                                            <td>{$data['tanggal_daftar']}</td>
                                            <td>{$data['last_login']}</td>
                                            <td>
                                                <a href='hu.php?id={$data['id_users']}' class='btn btn-danger'>Hapus</a>
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
        </div>
    </div>

        <script src="../fontawesome/fontawesome/js/all.min.js"></script>
</body>
</html>
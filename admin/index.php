<?php
    require "koneksi.php";
    require "session.php";

    $querykategori = mysqli_query($koneksi, "SELECT * FROM kategori");
    $jumlahkategori = mysqli_num_rows($querykategori);

    $querywisata = mysqli_query($koneksi, "SELECT * FROM wisata");
    $jumlahwisata = mysqli_num_rows($querywisata);

    $queryusers = mysqli_query($koneksi, "SELECT * FROM users");
    $jumlahusers = mysqli_num_rows($queryusers);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../fontawesome/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #f9f9f9;
        }

        .container {
            width: 90%;
            max-width: 1100px;
            margin: auto;
            padding: 20px;
        }

        nav.breadcrumb {
            font-size: 14px;
            margin-bottom: 20px;
        }

        nav.breadcrumb a {
            color: #007bff;
            text-decoration: none;
        }

        nav.breadcrumb a:hover {
            text-decoration: underline;
        }

        h2 {
            margin-bottom: 20px;
        }

        /* Grid layout untuk summary box */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 80px;
        }

        /* Style box */
        .summary-box {
            padding: 25px;
            border-radius: 15px;
            color: white;
            display: flex;
            align-items: center;
            justify-content:space-between;
        }

        .summary-box i {
            font-size: 80px;
            opacity: 0.8;
        }

        .summary-box h3 {
            margin: 0;
            font-size: 24px;
        }

        .summary-box p {
            margin: 5px 0;
        }

        .summary-box a {
            color: white;
            text-decoration: none;
            font-weight: bold;
        }

        .summary-box a:hover {
            text-decoration: underline;
        }

        /* Warna kotak */
        .summary-kategori { background-color: #817116; }
        .summary-wisata { background-color: #1e9eaf; }
        .summary-users { background-color: rgb(114, 184, 49); }
    </style>
</head>
<body>
    <?php require "navbar.php"; ?>
    
    <div class="container">
        <nav class="breadcrumb">
            <i class="fas fa-house-chimney"></i> Home
        </nav>

        <h2>Halo Admin</h2>

        <div class="grid">
            <div class="summary-box summary-kategori">
                <i class="fas fa-list"></i>
                <div>
                    <h3>Kategori</h3>
                    <p><?php echo $jumlahkategori; ?> Kategori</p>
                    <p><a href="kategori.php">Lihat Detail</a></p>
                </div>
            </div>

            <div class="summary-box summary-wisata">
                <i class="fa-solid fa-map-location-dot"></i>
                <div>
                    <h3>Wisata</h3>
                    <p><?php echo $jumlahwisata; ?> Wisata</p>
                    <p><a href="wisata.php">Lihat Detail</a></p>
                </div>
            </div>

            <div class="summary-box summary-users">
                <i class="fa-solid fa-users"></i>
                <div>
                    <h3>users</h3>
                    <p><?php echo $jumlahusers; ?> Users</p>
                    <p><a href="users.php">Lihat users</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="../fontawesome/fontawesome/js/all.min.js"></script>
</body>
</html>

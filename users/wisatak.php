<?php
session_start();
require "koneksi.php";

$id_users = $_SESSION['id_users'] ?? null;
$querykategori = mysqli_query($koneksi, "SELECT * FROM kategori");

// --- LOGIKA IF ELSE ---
if (isset($_GET['keyword']) && trim($_GET['keyword']) !== '') {
    $keyword = mysqli_real_escape_string($koneksi, trim($_GET['keyword']));

    // pilih wisata yang cocok dengan keyword (tambahkan is_favorite via LEFT JOIN ke favorite untuk user ini)
    $sql = "
        SELECT DISTINCT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar, w.deskripsi,
               IF(fu.id_users IS NULL, 0, 1) AS is_favorite
        FROM wisata w
        JOIN kategori k ON w.id_kategori = k.id_kategori
        LEFT JOIN favorite fu ON fu.id_wisata = w.id_wisata AND fu.id_users = '".mysqli_real_escape_string($koneksi,$id_users)."'
        WHERE LOWER(w.nama_wisata) LIKE LOWER('%$keyword%')
           OR LOWER(k.nama_kategori) LIKE LOWER('%$keyword%')
        ORDER BY RAND()
    ";
    $querywisata = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
}
else if (isset($_GET['kategori'])) {
    $kategori = mysqli_real_escape_string($koneksi, trim($_GET['kategori']));

    // 🔹 Jika kategori = Wisata Terfavorite (tampilkan yang paling banyak di-favorite kan)
    if (strtolower($kategori) === 'wisata terfavorite') {
        // hitung jumlah favorit per wisata, sertakan is_favorite untuk user saat ini (jika login)
        $sql = "
            SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar, w.deskripsi,
                   COUNT(f.id_wisata) AS total_favorit,
                   IF(fu.id_users IS NULL, 0, 1) AS is_favorite
            FROM wisata w
            LEFT JOIN favorite f ON w.id_wisata = f.id_wisata
            LEFT JOIN favorite fu ON fu.id_wisata = w.id_wisata AND fu.id_users = '".mysqli_real_escape_string($koneksi,$id_users)."'
            GROUP BY w.id_wisata
            HAVING total_favorit > 0
            ORDER BY total_favorit DESC
        ";
        $querywisata = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
    }
    else {
        // kategori biasa: ambil wisata sesuai id_kategori dan sertakan is_favorite
        $querygetkategoriid = mysqli_query($koneksi, "SELECT id_kategori FROM kategori WHERE nama_kategori='$kategori'");
        $kategoriid = mysqli_fetch_array($querygetkategoriid);

        if ($kategoriid) {
            $sql = "
                SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar, w.deskripsi,
                       IF(fu.id_users IS NULL, 0, 1) AS is_favorite
                FROM wisata w
                LEFT JOIN favorite fu ON fu.id_wisata = w.id_wisata AND fu.id_users = '".mysqli_real_escape_string($koneksi,$id_users)."'
                WHERE w.id_kategori = '".(int)$kategoriid['id_kategori']."'
                ORDER BY RAND()
            ";
            $querywisata = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
        } else {
            $querywisata = mysqli_query($koneksi, "SELECT * FROM wisata WHERE 1=0");
        }
    }
}
else {
    // default: semua wisata (sertakan is_favorite)
    $sql = "
        SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar, w.deskripsi,
               IF(fu.id_users IS NULL, 0, 1) AS is_favorite
        FROM wisata w
        LEFT JOIN favorite fu ON fu.id_wisata = w.id_wisata AND fu.id_users = '".mysqli_real_escape_string($koneksi,$id_users)."'
        ORDER BY RAND()
    ";
    $querywisata = mysqli_query($koneksi, $sql) or die(mysqli_error($koneksi));
}

$countdata = mysqli_num_rows($querywisata);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wisata Purbalingga | Wisata</title>
  <style>
    body {
      margin: 0;
      font-family: "Poppins", Arial, sans-serif;
      background: #f8f8f8;
      color: #333;
    }

    .banner {
      width: 100%;
      height: 60vh; /* tinggi fleksibel (60% tinggi layar) */
      height: 725px;
      background: url('../aset/w.png') center no-repeat;
      background-size: cover; /* biar gambar menyesuaikan kontainer */
    }

    .container {
      width: 90%;
      max-width: 1200px;
      margin: auto;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
      gap: 20px;
      align-items: flex-start; /* penting biar sidebar ikut tinggi isi */
    }

    .sidebar {
      width: 220px;               /* fixed width */
      background: #fff;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      flex: 0 0 auto;             /* tidak ikut melar */
      height: auto;               /* tinggi otomatis */
    }

    .sidebar h3 {
      margin-bottom: 15px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }

    .sidebar ul li {
      margin-bottom: 8px;
    }

    .sidebar ul li a {
      display: block;
      padding: 10px;
      background: #f1f1f1;
      border-radius: 6px;
      text-decoration: none;
      color: inherit;
      transition: background 0.3s;
    }

    .sidebar ul li a:hover {
      background: #ddd;
    }

    .content {
      flex: 1 1 700px;
    }

    .content h3 {
      text-align: center;
      margin-bottom: 30px;
    }

    .products {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
      gap: 20px;
    }

    .card {
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }

    .card:hover {
      transform: translateY(-4px);
    }

    .card img {
      width: 100%;
      height: 200px;
      object-fit: cover;
    }

    .card-body {
      padding: 15px;
      display: flex;
      flex-direction: column;
      flex: 1;
      align-items: center;       /* semua isi ke tengah */
      text-align: center;
    }

    .card-title {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .card-text {
      font-size: 16px;
      color: #000;
      margin-bottom: 35px;
    }

    .btn {
      text-align: center;
      padding: 10px;
      background: #007bff;
      color: #fff;
      border-radius: 6px;
      text-decoration: none;
      transition: background 0.3s;
    }

    .btn:hover {
      background: #0056b3;
    }
    .btn-detail {
      padding: 5px 55px;
      border-radius: 6px;
      background: #007bff;
      border: none;
      text-decoration: none;
      color: white;
      font-size: 14px;
    }
    .btn-detail:hover {
      background: #0056b3;
    }
    .love-btn {
      cursor: pointer;
      font-size: 22px;
      user-select: none;
      transition: color 0.2s ease;
    }
    .love-btn.active {
      color: red;
    }

    h4.text-center {
      text-align: center;
      margin: 40px 0;
    }
  </style>
</head>
<body>
  <?php require "navbar.php"; ?>

  <div class="banner"></div>

  <div class="container" style="padding: 50px 0;">
    <div class="row">
      
      <!-- Sidebar kategori -->
      <div class="sidebar">
        <h3>Kategori</h3>
        <ul>
          <li><a href="wisatak.php?kategori=Wisata+Terfavorite">Wisata Terfavorite</a></li>
          <?php while($kategori = mysqli_fetch_array($querykategori)): ?>
            <li><a href="wisata.php?kategori=<?php echo urlencode($kategori['nama_kategori']); ?>">
              <?php echo $kategori['nama_kategori']; ?>
            </a></li>
          <?php endwhile; ?>
        </ul>
      </div>

      <!-- Konten wisata -->
      <div class="content">
        <h3>Semua Wisata</h3>

        <?php if($countdata < 1): ?>
          <h4 class="text-center">Wisata terfavorite tidak tersedia</h4>
        <?php endif; ?>

        <div class="products">
          <?php while($wisata = mysqli_fetch_array($querywisata)): ?>
            <div class="card">
              <img src="../image/<?php echo $wisata['gambar']; ?>" alt="wisata">
              <div class="card-body">
                <h4 class="card-title"><?php echo $wisata['nama_wisata']; ?></h4>
                <p class="card-text"><?php echo $wisata['lokasi']; ?></p>
                <div class="card-footer">
                  <a href="wisata_detail.php?nama=<?php echo $wisata['nama_wisata']; ?>" class="btn-detail">Detail</a>
                  <span class="love-btn <?php echo $wisata['is_favorite'] ? 'active' : ''; ?>"
                    onclick="toggleLove(this, <?php echo $wisata['id_wisata']; ?>)">
                    <?php echo $wisata['is_favorite'] ? '♥' : '♡'; ?>
                  </span>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        </div>
      </div>

    </div>
  </div>

  <?php require "footer.php"; ?>


  <script>
  function toggleLove(el, id_wisata) {
    fetch("toggle_favorite.php", {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "id_wisata=" + encodeURIComponent(id_wisata)
    })
    .then(res => res.text())
    .then(text => {
      const t = text.trim();
      if (t === "added") {
        el.textContent = "♥";
        el.classList.add("active");
      } else if (t === "removed") {
        el.textContent = "♡";
        el.classList.remove("active");
      } else if (t.toLowerCase().includes("login")) {
        alert("Silakan login untuk menandai favorite.");
      } else {
        console.log("toggle_favorite.php response:", t);
      }
    })
    .catch(err => {
      console.error(err);
      alert("Terjadi kesalahan saat menyimpan favorite.");
    });
  }
</script>
</body>
</html>

<?php
session_start();
require "koneksi.php";

$id_users = $_SESSION['id_users'] ?? null;

// path ke file JSON yang akan menyimpan ID wisata random
$file = __DIR__ . '/random_products.json';

if (file_exists($file) && ($ids = json_decode(file_get_contents($file), true))) {
} else {
    $all  = mysqli_query($koneksi, "SELECT id_wisata FROM wisata");
    $ids  = [];
    while ($r = mysqli_fetch_assoc($all)) {
        $ids[] = (int)$r['id_wisata'];
    }
    shuffle($ids);
    $ids = array_slice($ids, 0, 8);
    file_put_contents($file, json_encode($ids));
}

if (count($ids) > 0) {
    $in   = implode(',', $ids);
    $sql  = "SELECT id_wisata,nama_wisata,lokasi,gambar,deskripsi FROM wisata WHERE id_wisata IN ($in) ORDER BY FIELD(id_wisata, $in)";
    $querywisataGrid = mysqli_query($koneksi, $sql);
} else {
    $querywisataGrid = mysqli_query($koneksi,"SELECT id_wisata,nama_wisata,gambar,deskripsi FROM wisata LIMIT 6" );
}

// query untuk favorit
if ($id_users) {
    $sql  = "
        SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar, w.deskripsi,
              IF(f.id_users IS NULL, 0, 1) AS is_favorite
        FROM wisata w
        LEFT JOIN favorite f 
          ON w.id_wisata = f.id_wisata AND f.id_users = '$id_users'
        WHERE w.id_wisata IN ($in)
        ORDER BY FIELD(w.id_wisata, $in)";
} else {
    // kalau belum login, tidak bisa ada favorite
    $sql  = "
        SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar, w.deskripsi,
              0 AS is_favorite
        FROM wisata w
        WHERE w.id_wisata IN ($in)
        ORDER BY FIELD(w.id_wisata, $in)";
}
$querywisataGrid = mysqli_query($koneksi, $sql);

// query khusus untuk informasi wisata (2 item acak)
$querywisataDetail = mysqli_query($koneksi, "SELECT * FROM wisata WHERE id_wisata IN (1, 5)");

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Wisata Purbalingga | Home</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    /* =========================
      GLOBAL
    ========================= */
    body {
      margin: 0;
      font-family: "Inter", Arial, sans-serif;
      background-color: #f7f6f6ff;
    }

    /* =========================
      CAROUSEL
    ========================= */
    .carousel {
      position: relative;
      overflow: hidden;
      max-height: 1000px;
    }
    .carousel img {
      width: 1350px;
      display: block;
    }

    /* =========================
      KATEGORI
    ========================= */
    .kategori-section {
      padding: 50px 20px;
      max-width: 1200px;
      margin: auto;
    }
    .kategori-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
      margin: 50px auto;
      max-width: 1000px;
    }
    .highlighted-kategori {
      display: flex;
      flex-direction: column;   /* Biar gambar di atas, teks di bawah */
      align-items: center;      /* Center horizontal */
      justify-content: center;  /* Center vertical */
      text-align: center;       /* Center tulisan */
      gap: 10px;
      padding: 15px;
      cursor: pointer;
      transition: 0.3s;
    }
    .highlighted-kategori h5 {
      margin: 0;
      font-size: 14px;
      color: #333;
    }
    .highlighted-kategori img {
      height: 65px;
      width: 108px;
      border-radius: 10px;
    }
    .highlighted-kategori:hover {
      background: #f1f1f1;
    }
    .no-decoration {
      text-decoration: none;
      color: black;
    }
    .logo {
      align-items: center;
      justify-content: flex-start;
      gap: 15px;
      padding: 15px;
      cursor: pointer;
      transition: 0.3s;
    }
    .logo img {
      height: 180px;
      width: 205px;
    }

    /* =========================
      Informasi Wisata
    ========================= */
    .wisata-detail {
      display: flex;
      flex-direction: column;
      gap: 40px;           /* jarak antar item */
      max-width: 1200px;
      margin: auto;
      padding: 20px;
    }

    .wisata-detail a {
      text-decoration: none;
      color: black;
    }

    .wisata-item {
      display: flex;
      align-items: center;
      gap: 30px;
    }

    .wisata-item.reverse {
      flex-direction: row-reverse; /* tukar posisi gambar & teks */
    }

    .wisata-item img {
      width: 45%;           /* gambar ambil 45% */
      height: auto;
      border-radius: 10px;
      object-fit: cover;
      flex-shrink: 0;       /* biar tidak gepeng */
    }

    .wisata-info {
      flex: 1;              /* teks isi sisa ruang */
    }

    .wisata-info p {
      font-size: 16px;
      line-height: 1.6;
      text-align: justify;
    }

    /* Responsif (HP) */
    @media (max-width: 768px) {
      .wisata-item,
      .wisata-item.reverse {
        flex-direction: column;
        text-align: center;
      }

      .wisata-item img {
        width: 100%;
      }
    }

    /* =========================
      WISATA
    ========================= */
    .wisata-section {
      padding: 80px 50px;
      max-width: 1200px;
      margin: auto;
    }
    .wisata-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }
    .card {
      position: relative;
      overflow: hidden;
      border: 1px solid #ddd;
      border-radius: 8px;
      background: #f8f8f8;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      display: flex;
      flex-direction: column;
      transition: 0.3s;
    }
    .card img {
      width: 100%;
      height: 250px;
      object-fit: cover;
      display: block;
    }
    .card-body {
      padding: 10px;
      text-align: center;
      flex: 1;
      display: flex;
      flex-direction: column;
    }
    .card-title {
      font-size: 25px;
      font-weight: bold;
      margin: 0 0 10px;
    }
    .card-text {
      font-size: 15px;
      margin-bottom: 15px;
    }
    .card-footer {
      display: flex;
      justify-content: center;   /* semua item ke tengah */
      align-items: center;
      gap: 70px;                 /* jarak antara tombol detail dan ikon love */
      margin-top: 8px;
    }

    /* =========================
      BUTTON & LOVE
    ========================= */
    .btn {
      display: inline-block;
      text-align: center;
      padding: 10px;
      border-radius: 6px;
      text-decoration: none;
      font-weight: bold;
      cursor: pointer;
      transition: 0.3s;
    }
    .btn-detail {
      padding: 5px 75px;
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
    .btn-primary {
      background: #007bff;
      color: white;
    }
    .btn-primary:hover {
      background: #0056b3;
    }
    .btn-outline-primary {
      border: 2px solid #007bff;
      color: #007bff;
      background: transparent;
    }
    .btn-outline-primary:hover {
      background: #007bff;
      color: white;
    }
    .lihat-semua {
      text-align: center;
      margin-top: 30px;
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
  </style>
</head>
<body>

  <?php require "navbar.php"; ?>

  <!-- Banner -->
  <div class="carousel">
    <img src="../aset/pbg.png" alt="banner1">
  </div>

  <!-- Infomasi wisata -->
<div class="wisata-detail">
  <?php 
    $i = 0; 
    while($wisata = mysqli_fetch_array($querywisataDetail)){ 
        // kalau item ke-2 kasih class reverse
        $class = ($i == 1) ? "wisata-item reverse" : "wisata-item"; 
  ?>
    <a href="wisata_detail.php?nama=<?php echo $wisata['nama_wisata']; ?>" class="no-decoration">
      <div class="<?php echo $class; ?>">
        <img src="../image/<?php echo $wisata['gambar']; ?>" alt="gambar wisata">
        <div class="wisata-info">
          <p><?php echo $wisata['deskripsi']; ?></p>
        </div>
      </div>
    </a>
  <?php 
      $i++; 
    } 
  ?>
</div>



  <!-- Highlighted kategori -->
<div class="kategori-section">
  <h3>Kategori Wisata</h3>
  <div class="kategori-container">
    <a class="no-decoration" href="wisatak.php?kategori=Wisata+Terfavorite">
      <div class="highlighted-kategori">
        <img src="../aset/um.jpg" alt="logo pbg">
        <h5>Wisata Terfavorite</h5>
      </div>
    </a>
    <?php
      require "koneksi.php";
      $sql = "SELECT * FROM kategori ORDER BY id_kategori ASC";
      $query = mysqli_query($koneksi, $sql);

      while($row = mysqli_fetch_assoc($query)){
    ?>
      <a class="no-decoration" href="wisata.php?kategori=<?php echo urlencode($row['nama_kategori']); ?>">
        <div class="highlighted-kategori">
          <img src="../image/<?php echo $row['foto']; ?>" alt="<?php echo $row['nama_kategori']; ?>">
          <h5><?php echo $row['nama_kategori']; ?></h5>
        </div>
      </a>
    <?php } ?>
  </div>
</div>



  <!-- wisata -->
<div class="wisata-section">
  <h3>Tempat Wisata</h3>

  <div class="wisata-grid">
    <?php while($wisata = mysqli_fetch_array($querywisataGrid)){ ?>
      <div class="card">
        <img src="../image/<?php echo $wisata['gambar']; ?>" alt="gambar wisata">
        <div class="card-body">
          <h4><?php echo $wisata['nama_wisata']; ?></h4>
          <p><?php echo $wisata['lokasi']; ?></p>
          <div class="card-footer">
            <a href="wisata_detail.php?nama=<?php echo $wisata['nama_wisata']; ?>" class="btn-detail">Detail</a>
            <span class="love-btn <?php echo $wisata['is_favorite'] ? 'active' : ''; ?>"
              onclick="toggleLove(this, <?php echo $wisata['id_wisata']; ?>)">
              <?php echo $wisata['is_favorite'] ? '♥' : '♡'; ?>
            </span>
          </div>
        </div>
      </div>
    <?php } ?>
  </div>

  <div class="lihat-semua">
    <a href="wisata.php" class="btn-detail">Lihat Semua</a>
  </div>
</div>


  <div class="kategori-container">
    <div class="logo">
      <img src="../aset/lpbg.png" alt="logo pbg">
    </div>
    <div class="logo">
      <img src="../aset/ljtng.png" alt="logo jateng">
    </div>
    <div class="logo">
      <img src="../aset/lwi1.png" alt="logo wi">
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

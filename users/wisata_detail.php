<?php
session_start();
require "koneksi.php";

$id_users = $_SESSION['id_users'] ?? null;
// ambil parameter nama dengan aman
$nama_raw = $_GET['nama'] ?? '';
$nama = mysqli_real_escape_string($koneksi, trim($nama_raw));

if ($nama === '') {
    echo "Nama wisata tidak diberikan.";
    exit;
}

// ambil data wisata
$querywisata = mysqli_query($koneksi, "SELECT * FROM wisata WHERE nama_wisata='$nama' LIMIT 1");
if (!$querywisata || mysqli_num_rows($querywisata) === 0) {
    echo "Wisata tidak ditemukan.";
    exit;
}
$wisata = mysqli_fetch_assoc($querywisata);

// id wisata
$id_wisata = (int)$wisata['id_wisata'];

// cek apakah user login & apakah sudah favorit
if ($id_users) {
    $chk = mysqli_query($koneksi, "
        SELECT 1 FROM favorite 
        WHERE id_users='".mysqli_real_escape_string($koneksi,$id_users)."' 
          AND id_wisata='$id_wisata' LIMIT 1
    ");
    $is_favorite = (mysqli_num_rows($chk) > 0) ? 1 : 0;
} else {
    $is_favorite = 0;
}

// ambil wisata terkait (dengan is_favorite)
if ($id_users) {
    $querywisataTerkait = mysqli_query($koneksi, "
        SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar,
               IF(f.id_users IS NULL, 0, 1) AS is_favorite
        FROM wisata w
        LEFT JOIN favorite f 
          ON f.id_wisata = w.id_wisata AND f.id_users = '$id_users'
        WHERE w.id_kategori = '".$wisata['id_kategori']."' 
          AND w.id_wisata != '$id_wisata'
        ORDER BY RAND() LIMIT 4
    ");
} else {
    $querywisataTerkait = mysqli_query($koneksi, "
        SELECT w.id_wisata, w.nama_wisata, w.lokasi, w.gambar,
               0 AS is_favorite
        FROM wisata w
        WHERE w.id_kategori = '".$wisata['id_kategori']."' 
          AND w.id_wisata != '$id_wisata'
        ORDER BY RAND() LIMIT 4
    ");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Wisata Purbalingga | Detail wisata</title>
  <style>
    body {
      font-family: "Poppins", sans-serif;
      margin:0; padding:0;
      background:#f9f9f9; color:#333;
    }
    .container { max-width:1200px; margin:20px auto; padding:20px; }
    img { width:100%; border-radius:10px; display:block; }
    h1 { margin: 12px 0; font-size:28px; display:inline-block; vertical-align:middle; }
    .meta { color:black; margin-bottom:16px; }

    /* Card style sama dengan wisata.php */
    .card {
      background: #fff;
      border-radius: 8px;
      overflow: hidden;
      display: flex;
      flex-direction: column;
      box-shadow: 0 2px 6px rgba(0,0,0,0.1);
      transition: transform 0.2s;
    }
    .card:hover { transform: translateY(-4px); }
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
      align-items: center;
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
    .card-footer {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 40px;
      margin-top: 8px;
    }
    .btn-detail {
      padding: 5px 75px;
      border-radius: 6px;
      background: #007bff;
      border: none;
      text-decoration: none;
      color: white;
      font-size: 14px;
      cursor: pointer;
    }
    .btn-detail:hover { background: #0056b3; }
    .love-btn {
      cursor: pointer;
      font-size: 22px;
      user-select: none;
      transition: color 0.2s ease;
    }
    .love-btn.active { color: red; }

    .wisata-terkait { background:#eee; padding:40px 20px; margin-top:40px; }

    .kategori-container {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 40px;
      margin: 50px auto;
      max-width: 1000px;
    }
    .logo {
      align-items: center;
      justify-content: flex-start;
      gap: 15px;
      padding: 15px;
      cursor: pointer;
      transition: 0.3s;
    }
    .logo img { height: 180px; width: 205px; }

    .name-row { display:flex; align-items:center; gap:12px; flex-wrap:wrap; }
    @media(max-width:600px){
      h1{ font-size:20px; }
      .love-btn{ font-size:20px; }
    }
  </style>
</head>
<body>
  <?php require "navbar.php"; ?>

  <div class="container">
    <!-- gambar -->
    <img src="../image/<?php echo htmlspecialchars($wisata['gambar']); ?>" alt="<?php echo htmlspecialchars($wisata['nama_wisata']); ?>">

    <!-- nama + favorite -->
    <div class="name-row">
      <h1><?php echo htmlspecialchars($wisata['nama_wisata']); ?></h1>
      <span id="loveBtn" class="love-btn <?php echo $is_favorite ? 'active' : ''; ?>"
            onclick="toggleLove(this, <?php echo $id_wisata; ?>)">
        <?php echo $is_favorite ? '♥' : '♡'; ?>
      </span>
    </div>

    <!-- lokasi -->
    <div class="meta">
      <strong>Lokasi:</strong> <?php echo htmlspecialchars($wisata['lokasi']); ?>
    </div>

    <!-- deskripsi -->
    <div>
      <h4>Detail Wisata :</h4>
      <p><?php echo nl2br(htmlspecialchars($wisata['deskripsi'])); ?></p>
    </div>
  </div>

  <!-- wisata terkait -->
  <div class="wisata-terkait">
    <div class="container">
      <h2 style="text-align:center;">Wisata Terkait</h2>
      <div class="products" style="display:grid; grid-template-columns: repeat(auto-fill,minmax(250px,1fr)); gap:20px; margin-top:20px;">
        <?php while($p = mysqli_fetch_assoc($querywisataTerkait)): ?>
          <div class="card">
            <img src="../image/<?php echo htmlspecialchars($p['gambar']); ?>" 
                 alt="<?php echo htmlspecialchars($p['nama_wisata']); ?>">
            
            <div class="card-body">
              <h4 class="card-title"><?php echo htmlspecialchars($p['nama_wisata']); ?></h4>
              <div class="card-text"><?php echo htmlspecialchars($p['lokasi']); ?></div>
              
              <div class="card-footer">
                <a href="wisata_detail.php?nama=<?php echo urlencode($p['nama_wisata']); ?>">
                  <button class="btn-detail">Lihat Detail</button>
                </a>
                
                <span class="love-btn <?php echo $p['is_favorite'] ? 'active' : ''; ?>"
                      onclick="toggleLove(this, <?php echo $p['id_wisata']; ?>)">
                  <?php echo $p['is_favorite'] ? '♥' : '♡'; ?>
                </span>
              </div>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>

  <div class="kategori-container">
    <div class="logo"><img src="../aset/lpbg.png" alt="logo pbg"></div>
    <div class="logo"><img src="../aset/ljtng.png" alt="logo jateng"></div>
    <div class="logo"><img src="../aset/lwi1.png" alt="logo wi"></div>
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

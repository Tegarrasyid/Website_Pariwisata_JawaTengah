<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Navbar Login</title>
  <style>
    /* Reset */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family:Verdana, Geneva, Tahoma, sans-serif;
      background-color: #f7f6f6ff; /* Putih abu-abu terang untuk body */
    }

    /* Navbar */
    .navbar {
      background-color: rgb(255, 255, 255);
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 10px 20px;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    /* Logo */
    .navbar .brand img {
      height: 50px;
    }

    /* Navbar links */
    .navbar ul {
      list-style: none;
      display: flex;
      gap: 20px;
    }

    .navbar ul li a {
      text-decoration: none;
      color: rgb(0, 0, 0);
      font-weight: 500;
      transition: 0.3s;
    }

    .navbar ul li a:hover {
      color: #ffcc00;
    }

    /* Burger menu (mobile) */
    .menu-toggle {
      display: none;
      flex-direction: column;
      cursor: pointer;
      gap: 5px;
    }

    .menu-toggle span {
      width: 25px;
      height: 3px;
      background: rgb(0, 0, 0);
      border-radius: 5px;
    }

    /* Responsive */
    @media (max-width: 768px) {
      .navbar ul {
        position: absolute;
        top: 60px;
        right: 0;
        background: rgba(255, 255, 255, 1);
        flex-direction: column;
        width: 200px;
        display: none;
        padding: 10px;
      }

      .navbar ul.active {
        display: flex;
      }

      .menu-toggle {
        display: flex;
      }
    }
  </style>
</head>
<body>
  <nav class="navbar">
    <div class="brand">
      <a href="../admin">
        <img src="../aset/coba5.png" alt="Logo Sepatu">
      </a>
    </div>

    <div class="menu-toggle" onclick="toggleMenu()">
      <span></span>
      <span></span>
      <span></span>
    </div>

    <ul id="nav-links">
      <li><a href="../admin">Home</a></li>
      <li><a href="kategori.php">Kategori</a></li>
      <li><a href="wisata.php">Wisata</a></li>
      <li><a href="users.php">users</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </nav>

  <script>
    function toggleMenu() {
      document.getElementById("nav-links").classList.toggle("active");
    }
  </script>
</body>
</html>

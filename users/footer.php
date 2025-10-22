<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Footer</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <style>
    body {
      margin: 0;
      font-family: "Poppins", Arial, sans-serif;
    }

    .footer {
      background-color: #ffffffff;
      color: #000000;
      padding: 40px 20px;
      margin-top: 40px;
    }

    .footer-container {
      max-width: 1200px;
      margin: 0 auto;
      display: flex;
      flex-wrap: wrap;
      justify-content: space-between;
      gap: 30px;
    }

    /* Kolom kiri */
    .footer-left {
      flex: 1 1 55%;
      min-width: 300px;
    }

    .footer-left img {
      max-width: 180px;
      margin-bottom: 15px;
    }

    .footer-left p {
      margin: 8px 0;
      line-height: 1.6;
    }

    .footer-left h6 {
      margin-top: 20px;
      font-size: 16px;
      font-weight: bold;
    }

    .social-links {
      display: flex;
      gap: 15px;
      margin-top: 10px;
    }

    .social-links a {
      color: #000000;
      font-size: 20px;
      transition: color 0.3s;
    }

    .social-links a:hover {
      color: #f39c12;
    }

    /* Kolom kanan */
    .footer-right {
      flex: 1 1 40%;
      display: flex;
      justify-content: space-between;
      gap: 20px;
      min-width: 250px;
    }

    .footer-right h6 {
      font-size: 16px;
      margin-bottom: 10px;
      font-weight: bold;
    }

    .footer-right p,
    .footer-right li {
      margin: 6px 0;
      font-size: 14px;
    }

    .footer-right ul {
      list-style: none;
      padding: 0;
    }

    .footer-right i {
      margin-right: 6px;
    }

    /* Garis bawah */
    .footer-bottom {
      text-align: center;
      padding: 15px;
      margin-top: 20px;
      border-top: 1px solid #555;
      font-size: 14px;
    }

    /* Responsif */
    @media (max-width: 768px) {
      .footer-container {
        flex-direction: column;
        align-items: flex-start;
      }
      .footer-right {
        flex-direction: column;
      }
    }
  </style>
</head>
<body>

  <footer class="footer">
    <div class="footer-container">

      <!-- Kolom Kiri -->
      <div class="footer-left">
        <a href="#"><img src="../aset/coba5.png" alt="wisitpbg logo"></a>
        <p>----</p>
        <p>Alamat: Jl. Mayor Jend. Sungkono No.34, Selabaya, Kec. Kalimanah, Kabupaten Purbalingga, Jawa Tengah 53371</p>

        <h6>Follow Us</h6>
        <div class="social-links">
          <a href=""><i class="fab fa-instagram"></i></a>
          <a href=""><i class="fab fa-tiktok"></i></a>
          <a href=""><i class="fab fa-youtube"></i></a>
          <a href=""><i class="fab fa-x-twitter"></i></a>
        </div>
      </div>

      <!-- Kolom Kanan -->
      <div class="footer-right">
        <div>
          <h6>Information & Policies</h6>
          <p>Privacy policies</p>
          <p>Links</p>
          <p>Delivery exchange</p>
          <p>Payment confirmation</p>
        </div>

        <div>
          <h6>Customer Services</h6>
          <ul>
            <li><i class="fab fa-whatsapp"></i> -----</li>
            <li><i class="far fa-envelope"></i> wisata@gmail.com</li>
          </ul>
        </div>
      </div>

    </div>

    <div class="footer-bottom">
      &copy; Copyright <?= date('Y') ?> Wisata. All rights reserved.
    </div>
  </footer>

</body>
</html>

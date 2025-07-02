<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Financeku</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
  <style>
  /* Global Settings */
    * {
      box-sizing: border-box;
      font-family: 'Inter', sans-serif; 
    }

    html, body {
      margin: 0;
      padding: 0;
      background: linear-gradient(180deg, #FFFFFF 0%, #D0D0D0 100%);
      color: #1a1a1a;
      width: 100%;
      scroll-behavior: smooth;
    }

    .container {
      margin: 0 auto;
      max-width: 1200px;
      width: 90%;
      padding: 0;
    }

    /* Animations */
    @keyframes slideFromLeft {
      0% {
        transform: translateX(-100px);
        opacity: 0;
      }
      100% {
        transform: translateX(0);
        opacity: 1;
      }
    }

    @keyframes slideFromRight {
      0% {
        transform: translateX(100px);
        opacity: 0;
      }
      100% {
        transform: translateX(0);
        opacity: 1;
      }
    }

    @keyframes fadeIn {
      0% {
        opacity: 0;
      }
      100% {
        opacity: 1;
      }
    }

    @keyframes pulse {
      0% {
        transform: scale(1);
      }
      50% {
        transform: scale(1.05);
      }
      100% {
        transform: scale(1);
      }
    }

    /* Navbar */
    nav {
      height: 70px;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      z-index: 10;
      background-color: #FDFEFE;
      display: flex;
      align-items: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.05);
      padding: 0;
      transition: all 0.3s ease;
    }

    .nav-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
    }

    .logo {
      font-size: 35px;
      font-weight: 700;
      color: #000;
      text-decoration: none;
      transition: all 0.3s ease;
    }

    .logo:hover {
      transform: scale(1.05);
    }

    .nav-link {
      list-style: none;
      display: flex;
      gap: 20px;
      padding: 0;
      margin: 0;
    }

    .nav-right {
      display: flex;
      align-items: center;
      gap: 20px;
    }

    .nav-link li a {
      text-decoration: none;
      color: #000;
      font-weight: 500;
      transition: color 0.3s ease;
      position: relative;
    }

    .nav-link li a:after {
      content: '';
      position: absolute;
      width: 0;
      height: 2px;
      background: #000;
      bottom: -5px;
      left: 0;
      transition: width 0.3s ease;
    }

    .nav-link li a:hover:after {
      width: 100%;
    }

    .nav-link li a:hover {
      color: #555;
    }

    .btn-login {
      background-color: #000;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
      margin-left: 10px;
      transition: all 0.3s ease;
      cursor: pointer;
    }

    .btn-login a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
    }

    .btn-login:hover {
      background-color: #444;
      transform: translateY(-3px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.2);
    }

    .hamburger {
      display: none;
      cursor: pointer;
    }

    .hamburger div {
      width: 25px;
      height: 3px;
      background-color: #000;
      margin: 5px 0;
      transition: all 0.3s ease;
    }

    /* Header */
    header {
      padding-top: 70px;
    }

    .header-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 90px 0;
      min-height: 90vh;
    }

    .header-left {
      flex: 1;
      padding-right: 30px;
      max-width: 50%;
      animation: slideFromLeft 1s ease forwards;
    }

    .header-left h1 {
      font-size: 40px;
      font-weight: 700;
      margin-top: 0;
      margin-bottom: 15px;
      line-height: 1.2;
    }

    .header-left p {
      font-size: 18px;
      color: #666;
      margin-bottom: 25px;
      max-width: 500px;
      line-height: 1.4;
    }

    .btn-daftar {
      background-color: #000;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      transition: all 0.3s ease;
      cursor: pointer;
      animation: pulse 2s infinite;
    }
    
    .btn-daftar:hover {
      background-color: #444;
      transform: translateY(-5px);
      box-shadow: 0 6px 12px rgba(0,0,0,0.15);
      animation: none;
    }

    .btn-daftar a {
      color: #fff;
      text-decoration: none;
      font-weight: 500;
      font-size: 16px;
    }

    .header-right {
      flex: 1;
      display: flex;
      justify-content: flex-end;
      max-width: 50%;
      animation: slideFromRight 1s ease forwards;
    }

    .header-right img {
      width: 100%;
      max-width: 550px;
      object-fit: contain;
      transition: transform 0.5s ease;
    }

    .header-right img:hover {
      transform: translateY(-10px);
    }

    /* Section Headings */
    .heading {
      text-align: center;
      font-weight: 700;
      font-size: 24px;
      margin-top: 50px;
      color: #111;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease;
    }

    .heading.show {
      opacity: 1;
      transform: translateY(0);
    }

    /* Tentang */
    section {
      padding-top: 70px;
      margin-top: -70px;
    }
    
    section#Fitur {
      padding-top: 100px;
      margin-top: -70px;
      margin-bottom: 60px;
    }
    
    section#Kontak {
      padding-top: 100px;
      margin-top: -70px;
      padding-bottom: 100px; 
    }

    .about-container {
      text-align: justify;
      padding: 30px 0;
      margin-bottom: 60px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease;
    }

    .about-container.show {
      opacity: 1;
      transform: translateY(0);
    }

    .about-container p {
      font-size: 17px;
      color: #333;
      max-width: 800px;
      margin: 0 auto;
      line-height: 1.8;
      padding: 15px 0;
    }

    /* Fitur */
    .fitur {
      display: flex;
      justify-content: space-around;
      flex-wrap: wrap;
      gap: 30px;
      margin-top: 30px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease;
    }

    .fitur.show {
      opacity: 1;
      transform: translateY(0);
    }

    .fitur-item {
      background-color: #fff;
      text-align: center;
      padding: 20px;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      flex: 1 1 220px;
      max-width: 250px;
      transition: all 0.3s ease;
    }

    .fitur-item:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 16px rgba(0,0,0,0.1);
    }

    .fitur-item img {
      width: 60px;
      margin-bottom: 15px;
      transition: transform 0.3s ease;
    }

    .fitur-item:hover img {
      transform: scale(1.1);
    }

    .heading2 {
      font-size: 20px;
      font-weight: 600;
      text-align: center;   
    }
    
    .heading3 {
      font-size: 18px;
      font-weight: 600;
      margin-bottom: 10px;
    }

    .fitur-item p {
      font-size: 14px;
      color: #555;
    }

    /* Kontak */
    .contact-container {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 50px;
      margin-top: 30px;
      padding-bottom: 50px;
      opacity: 0;
      transform: translateY(20px);
      transition: all 0.5s ease;
    }

    .contact-container.show {
      opacity: 1;
      transform: translateY(0);
    }

    .contact-item {
      display: flex;
      flex-direction: column;
      align-items: center;
      text-align: center;
    }

    .contact-container .icon1 {
      width: 40px;
      height: 40px;
      transition: transform 0.3s ease;
      margin-bottom: 10px;
      cursor: pointer;
    }

    .contact-container .icon1:hover {
      transform: scale(1.2) rotate(5deg);
    }
    
    .contact-item a {
      color: #333;
      text-decoration: none;
      font-size: 14px;
      margin-top: 5px;
    }

    /* Footer */
    footer {
      background-color: transparent;
      text-align: center;
      padding: 40px 10px;
      color: #333;
      border-top: 1px solid #e0e0e0;
      margin-top: 20px;
    }

    footer .nav-link {
      justify-content: center;
      margin-bottom: 20px;
      padding: 0;
      flex-wrap: wrap;
    }

    footer .nav-link li a {
      color: #000;
      text-decoration: none;
      margin: 0 15px;
      font-size: 18px;
      font-weight: 500;
    }
    
    footer .nav-link li a:hover {
      color: #555;
    }
    
    footer p {
      font-size: 16px;
      margin-top: 15px;
      font-weight: 500;
    }

    /* Responsive adjustments */
    @media (max-width: 1024px) {
      .header-left h1 {
        font-size: 36px;
      }
      
      .header-container {
        padding: 70px 0;
      }
    }

    @media (max-width: 992px) {
      .fitur-item {
        flex: 1 1 200px;
      }
    }

    @media (max-width: 768px) {
      .header-container {
        flex-direction: column;
        padding: 40px 0;
        text-align: center;
      }
      
      .header-left, .header-right {
        width: 100%;
        max-width: 100%;
        padding: 0;
        margin-bottom: 30px;
      }
      
      .header-left {
        order: 2;
      }
      
      .header-right {
        order: 1;
        justify-content: center;
        margin-bottom: 40px;
      }
      
      .header-left h1 {
        font-size: 32px;
      }
      
      .header-left p {
        margin: 0 auto 25px;
      }
      
      .header-right img {
        max-width: 90%;
      }
      
      .hamburger {
        display: block;
        z-index: 11;
      }
      
      .nav-right {
        position: fixed;
        top: 0;
        right: -100%;
        width: 80%;
        height: 100vh;
        background: #fff;
        flex-direction: column;
        justify-content: center;
        padding: 50px 0;
        transition: all 0.5s ease;
        box-shadow: -5px 0 15px rgba(0,0,0,0.1);
      }
      
      .nav-right.active {
        right: 0;
      }
      
      .nav-link {
        flex-direction: column;
        align-items: center;
      }
      
      .nav-link li {
        margin: 15px 0;
      }
      
      .btn-login {
        margin-top: 20px;
      }
      
      .fitur {
        gap: 20px;
      }
      
      .contact-container {
        flex-direction: column;
        gap: 30px;
      }
      
      footer .nav-link li {
        margin: 5px 0;
      }
    }

    @media (max-width: 576px) {
      .logo {
        font-size: 28px;
      }
      
      .header-left h1 {
        font-size: 28px;
      }
      
      .header-left p {
        font-size: 16px;
      }
      
      .heading {
        font-size: 22px;
      }
      
      .about-container p {
        font-size: 15px;
      }
      
      .fitur-item {
        flex: 1 1 100%;
        max-width: 100%;
      }
    }

    @media (max-width: 400px) {
      .container {
        width: 95%;
      }
      
      .header-left h1 {
        font-size: 26px;
      }
      
      .btn-daftar {
        padding: 10px 20px;
      }
      
      .btn-daftar a {
        font-size: 14px;
      }
    }

    /* Animation for hamburger menu */
    .toggle .line1 {
      transform: rotate(-45deg) translate(-5px, 6px);
    }
    
    .toggle .line2 {
      opacity: 0;
    }
    
    .toggle .line3 {
      transform: rotate(45deg) translate(-5px, -6px);
    }
  </style>
</head>
<body>
    <nav>
        <div class="container nav-container">
            <a href="index.php" class="logo">Financeku.</a>
            <div class="hamburger">
                <div class="line1"></div>
                <div class="line2"></div>
                <div class="line3"></div>
            </div>
            <div class="nav-right">
                <ul class="nav-link">
                    <li><a href="#Beranda">Beranda</a></li>
                    <li><a href="#Tentang">Tentang</a></li>
                    <li><a href="#Fitur">Fitur</a></li>
                    <li><a href="#Kontak">Kontak</a></li>
                </ul>
                <button class="btn-login">
                    <a href="login.php">Login</a>
                </button>
            </div>
        </div>
    </nav>

    <header id="Beranda">
        <div class="container header-container">
            <div class="header-left">
                <div class="hero-text">
                    <h1>Kelola Keuangan Pribadimu Lebih Mudah!</h1>
                    <p>Dengan Financeku, semua transaksi tercatat rapi dan bisa dipantau kapan saja.</p>
                    <button class="btn-daftar">
                        <a href="login.php">Daftar Sekarang</a>
                    </button>
                </div>
            </div>
            <div class="header-right">
                <img src="img/image_index.png" alt="Ilustrasi Financeku">
            </div>
        </div>
    </header>

    <section id="Tentang">    
        <h2 class="heading">Tentang</h2>
        <hr style="width: 150px; margin: 20px auto;">
        <div class="container about-container">
             <div class="edu-box">
                <p>
                    <b>Financeku</b> adalah aplikasi pencatatan keuangan pribadi yang sederhana dan mudah digunakan. Dirancang khusus untuk membantu individu dalam mengelola keuangan harian, Financeku memungkinkan pengguna mencatat pemasukan dan pengeluaran secara rapi, memantau saldo secara real-time, serta menyusun laporan keuangan secara otomatis. Dengan antarmuka yang ramah pengguna dan fitur yang praktis, Financeku cocok digunakan oleh siapa saja yang ingin lebih sadar dan tertib dalam mengatur keuangan pribadi.
                </p>
            </div>
        </div>
    </section>
    
    <section id="Fitur">
        <h2 class="heading">Fitur Utama</h2>
        <hr style="width: 150px; margin: 20px auto;">
        <div class="container fitur">
            <div class="fitur-item">
                <img src="img/atur_budget.png" alt="Atur Budget">
                <h3 class="heading3">Atur Budget Bulanan</h3>
                <p>Buat dan sesuaikan anggaran bulanan sesuai kebutuhan pribadi.</p>
            </div>
            <div class="fitur-item">
                <img src="img/catat_transaksi.png" alt="Catat Transaksi">
                <h3 class="heading3">Pencatatan Transaksi</h3>
                <p>Catat pemasukan dan pengeluaran dengan cepat, kapan pun dan di mana pun.</p>
            </div>
            <div class="fitur-item">
                <img src="img/laporan_keuangan.png" alt="Laporan Keuangan">
                <h3 class="heading3">Laporan Keuangan Otomatis</h3>
                <p>Lihat ringkasan pemasukan, pengeluaran, dan saldo secara otomatis.</p>
            </div>
            <div class="fitur-item">
                <img src="img/riwayat.png" alt="Riwayat Transaksi">
                <h3 class="heading3">Riwayat Transaksi</h3>
                <p>Akses histori lengkap semua transaksi untuk keperluan analisis dan pencatatan ulang.</p>
            </div>
        </div>
    </section>

    <section id="Kontak"> 
        <h1 class="heading">Kontak</h1>
        <hr style="width: 150px; margin: 20px auto;">
        <h2 class="heading2">Hubungi Kami :</h2>
        <div class="contact-container container">
            <div class="contact-item">
                <img src="img/insta.png" class="icon1" alt="Instagram">
                <a href="" target="_blank"></a>
            </div>
            <div class="contact-item">
                <img src="img/mail.png" class="icon1" alt="Email">
                <a href="mailto:info@financeku.com" target="_blank"></a>
            </div>
            <div class="contact-item">
                <img src="img/gihub.png" class="icon1" alt="GitHub">
                <a href="" target="_blank"></a>
            </div>
        </div>
    </section>

    <footer>
        <ul class="nav-link">
            <li><a href="#Beranda">Beranda</a></li>
            <li><a href="#Tentang">Tentang</a></li>
            <li><a href="#Fitur">Fitur</a></li>
            <li><a href="#Kontak">Kontak</a></li>
        </ul>
        <p>&copy; Copyright 2025 Financeku. All rights reserved.</p>
        <p>Versi 1.1 - Update July 2025</p>
    </footer>

    <script>
        // Mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navRight = document.querySelector('.nav-right');
        const navLinks = document.querySelectorAll('.nav-link li a');

        hamburger.addEventListener('click', () => {
            navRight.classList.toggle('active');
            hamburger.classList.toggle('toggle');
        });

        // Close menu when clicking on links
        navLinks.forEach(link => {
            link.addEventListener('click', () => {
                navRight.classList.remove('active');
                hamburger.classList.remove('toggle');
            });
        });

        // Scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('show');
                }
            });
        }, {
            threshold: 0.1
        });

        // Elements to animate on scroll
        const animatedElements = document.querySelectorAll('.heading, .about-container, .fitur, .contact-container');
        animatedElements.forEach(el => observer.observe(el));

        // Shrink navbar on scroll
        window.addEventListener('scroll', () => {
            const nav = document.querySelector('nav');
            const logo = document.querySelector('.logo');
            if (window.scrollY > 50) {
                nav.style.height = '60px';
                logo.style.fontSize = '30px';
            } else {
                nav.style.height = '70px';
                logo.style.fontSize = '35px';
            }
        });
    </script>
</body>
</html>
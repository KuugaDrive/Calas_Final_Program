<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Halaman Utama Website</title>
<style>
  body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #f9fafb;

    /* Biar konten main center vertikal & horizontal */
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh; /* full viewport height */
  }
  
  header {
    background-color: #50aaffec;
    color: white;
    padding: 15px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    z-index: 100;
  }

  .logo-container {
    display: flex;
    align-items: center;
    gap: 10px; /* jarak antara logo dan teks */
  }

  .logo-img {
    height: 40px; /* sesuaikan ukuran logo */
    width: auto;
  }

  header .logo {
    font-size: 24px;
    font-weight: bold;
  }
  
  header .login-btn {
    background-color: #6f42c1;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    text-decoration: none;
    font-size: 16px;
  }
  header .login-btn:hover {
    background-color: #5936a0;
  }

  main {
    background: white;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.1);
    max-width: 600px;
    text-align: center;
    margin-top: 70px; 
  }

  main h1 {
  font-size: 30px;
  margin-bottom: 30px;
  color: #50aaffec;
  padding-bottom: 10px;
  border-bottom: 2px solid #50aaffec;
}
  
  main p {
    font-size: 18px;
    color: #555;
    line-height: 1.5;
  }
</style>
</head>
<body>

<header>
  <div class="logo-container">
    <img src="src/img/logo_Lab_1.png" alt="Logo Lab ICT" class="logo-img">
    <div class="logo">Lab ICT</div>
  </div>
  <a href="src/login.php" class="login-btn">Login</a>
</header>

<main>
  <h1>Selamat Datang di Website Event Lab ICT</h1>
  <p>
    Ini adalah website resmi Lab ICT yang menyediakan fitur pengelolaan kegiatan, proposal, LPJ, sertifikat, banner, dan dokumentasi.
    Silakan login untuk mengakses dashboard dan fitur lengkap.
  </p>
</main>

</body>
</html>
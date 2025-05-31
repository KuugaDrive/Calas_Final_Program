<?php
session_start();
include 'koneksi.php';

// Cek user login dan role
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['asisten_lab', 'spv'])) {
    header('Location: login.php');
    exit;
}

// Query semua kegiatan dengan nama user pembuat
$sql = "SELECT k.kd_kegiatan, k.nama_kegiatan, k.tgl_kegiatan, k.status, u.nama 
        FROM kegiatan k 
        JOIN user u ON k.kd_user = u.kd_user 
        ORDER BY k.tgl_kegiatan DESC";

$result = $conn->query($sql);

$kegiatan_all = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $kegiatan_all[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Dashboard Event Lab</title>
<link rel="stylesheet" href="css/style_dashboard.css" />
<!-- Bisa pakai font icon gratis seperti FontAwesome, atau pakai emoji sederhana -->
</head>
<body>

<div class="container">
  <nav class="sidebar">
    <img src="img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
    <a href="dashboard_user.php" class="active"">Dashboard</a>
    <a href="proposal.php">Proposal</a>
    <a href="lpj.php">LPJ</a>
    <a href="sertifikat.php">Sertifikat</a>
    <a href="banner.php">Banner</a>
    <a href="dokumentasi.php">Dokumentasi</a>
   <div class="dropdown">
  <button class="dropdown-btn create-template-btn">Create Based on Template</button>
  <div class="dropdown-content">
    <div class="dropdown-submenu">
    <button class="dropdown-btn create-template-btn">Proposal</button>  
      <div class="dropdown-content">
        <a href="../code_template/form_proposal_webinar.php">Webinar</a>
        <a href="../code_template/form_proposal_workshop.php">Workshop</a>
        <a href="../code_template/form_proposal_talkshow.php">Talkshow</a>
        <a href="../code_template/form_proposal_bukber.php">Bukber</a>
      </div>
    </div>

    <div class="dropdown-submenu">
    <button class="dropdown-btn create-template-btn">LPJ</button>  
      <div class="dropdown-content">
        <a href="../code_template/form_lpj_webinar.php">Webinar</a>
        <a href="../code_template/form_lpj_workshop.php">Workshop</a>
        <a href="../code_template/form_lpj_talkshow.php">Talkshow</a>
        <a href="../code_template/form_lpj_bukber.php">Bukber</a>
      </div>
    </div>

</nav>

<main class="main-content">
  <div class="header-top">
    <div class="welcome">
       <?php
        if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] !== 'asisten_lab' && $_SESSION['role'] !== 'spv')) {
            header('Location: login.php');
            exit;
        }
        ?>
        <h2>Selamat Datang, <?php echo $_SESSION['nama'] ?></h2>
    </div>
    <a href="logout.php" class="btn-logout">Logout</a>
  </div>

  <!-- tombol tambah kegiatan floating -->
  <a href="create_kegiatan.php" class="btn-tambah-kegiatan">+</a>

  <div class="cards">
    <?php foreach ($kegiatan_all as $kegiatan): ?>
    <div class="card">
        <h3><?= htmlspecialchars($kegiatan['nama_kegiatan']) ?></h3>
        <p>Dibuat: <?= htmlspecialchars($kegiatan['nama']) ?></p>
        <p>Tanggal: <?= htmlspecialchars($kegiatan['tgl_kegiatan']) ?></p>
        <a href="detail_kegiatan.php?kd=<?= urlencode($kegiatan['kd_kegiatan']) ?>">Detail</a>
    </div>
    <?php endforeach; ?>

    <script>
  document.querySelectorAll('.dropdown-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const dropdown = btn.nextElementSibling;
      if (dropdown.style.display === 'block') {
        dropdown.style.display = 'none';
      } else {
        dropdown.style.display = 'block';
      }
    });
  });

  window.addEventListener('click', function(e) {
    document.querySelectorAll('.dropdown-content').forEach(menu => {
      if (!menu.previousElementSibling.contains(e.target) && !menu.contains(e.target)) {
        menu.style.display = 'none';
      }
    });
  });
</script>
</div>
</main>
</body>
</html>




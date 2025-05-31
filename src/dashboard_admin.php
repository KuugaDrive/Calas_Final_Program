<?php
session_start();
include 'koneksi.php';

// Cek role admin (kepala_lab)
if (!isset($_SESSION['logged_in']) || $_SESSION['role'] !== 'kepala_lab') {
    header('Location: login.php');
    exit;
}

// Tangani update status jika ada POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kd_kegiatan'], $_POST['status_baru'])) {
    $kd_kegiatan_update = $_POST['kd_kegiatan'];
    $status_baru = $_POST['status_baru'];

    $allowed_status = ['Proses', 'Terima', 'Tolak'];
    if (in_array($status_baru, $allowed_status)) {
        $stmt = $conn->prepare("UPDATE kegiatan SET status = ? WHERE kd_kegiatan = ?");
        $stmt->bind_param("ss", $status_baru, $kd_kegiatan_update);
        $stmt->execute();
        header("Location: dashboard_admin.php");
        exit;
    }
}

// Ambil semua kegiatan dengan data user pembuat
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
<title>Dashboard Admin</title>
<link rel="stylesheet" href="css/style_dashboard.css" />
<style>
.cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  margin-top: 20px;
}
.card {
  background: white;
  border-radius: 10px;
  padding: 15px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  color: #333;
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  min-width: 0;
}
.status-form select {
  width: 100%;
  padding: 5px;
  font-size: 1rem;
  border-radius: 5px;
}
.status-form button {
  margin-top: 10px;
  padding: 8px;
  background-color: #6f42c1;
  color: white;
  border: none;
  border-radius: 5px;
  cursor: pointer;
}
.status-form button:hover {
  background-color: #563d7c;
}
</style>
</head>
<body>
<div class="container">
  <nav class="sidebar">
    <img src="img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
    <a href="dashboard_admin.php" class="active">Dashboard</a>
    <a href="proposal.php">Proposal</a>
    <a href="lpj.php">LPJ</a>
    <a href="sertifikat.php">Sertifikat</a>
    <a href="banner.php">Banner</a>
    <a href="dokumentasi.php">Dokumentasi</a>
    <a href="registrasi.php">Registrasi Akun</a>
  </nav>

  <main class="main-content">
    <div class="header-top">
      <div class="welcome">
        <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?></h2>
      </div>
      <a href="logout.php" class="btn-logout">Logout</a>
    </div>

    <div class="cards">
      <?php foreach ($kegiatan_all as $kegiatan): ?>
        <div class="card">
          <h3><?= htmlspecialchars($kegiatan['nama_kegiatan']) ?></h3>
          <p>Dibuat: <?= htmlspecialchars($kegiatan['nama']) ?></p>
          <p>Tanggal: <?= htmlspecialchars($kegiatan['tgl_kegiatan']) ?></p>

          <a href="detail_kegiatan.php?kd=<?= urlencode($kegiatan['kd_kegiatan']) ?>">Detail</a>
        </div>
      <?php endforeach; ?>
    </div>
  </main>
</div>
</body>
</html>

<?php
session_start();
include 'koneksi.php';

// Cek login dan role valid
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['kepala_lab', 'asisten_lab', 'spv'])) {
    header('Location: login.php');
    exit;
}

// link dashboard sesuai role
$dashboard_link = $_SESSION['role'] === 'kepala_lab' ? 'dashboard_admin.php' : 'dashboard_user.php';

// --- PAGINATION LOGIC ---
$items_per_page = 5; // Jumlah sertifikat per halaman

// 1. Tentukan halaman aktif
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

// 2. Hitung total sertifikat (untuk pagination)
$count_sql = "SELECT COUNT(s.kd_sertifikat) AS total_items
              FROM sertifikat s
              JOIN kegiatan k ON s.kd_kegiatan = k.kd_kegiatan
              JOIN user u ON k.kd_user = u.kd_user";
$count_result = $conn->query($count_sql);
$total_items = 0;
if ($count_result && $count_result->num_rows > 0) {
    $total_items = $count_result->fetch_assoc()['total_items'];
}

// 3. Hitung total halaman
$total_pages = ceil($total_items / $items_per_page);
if ($current_page > $total_pages && $total_pages > 0) {
    $current_page = $total_pages;
} elseif ($total_pages == 0 && $current_page > 1) {
    $current_page = 1;
}

// 4. Hitung offset
$offset = ($current_page - 1) * $items_per_page;

// 5. Modifikasi query utama dengan LIMIT, OFFSET, dan ORDER BY kd_sertifikat
$sql = "SELECT s.kd_sertifikat, s.tgl_setuju, s.status, s.file_path, u.nama AS nama_user, k.nama_kegiatan
        FROM sertifikat s
        JOIN kegiatan k ON s.kd_kegiatan = k.kd_kegiatan
        JOIN user u ON k.kd_user = u.kd_user
        ORDER BY s.kd_sertifikat ASC  -- Diurutkan berdasarkan kd_sertifikat dari kecil ke besar
        LIMIT $items_per_page OFFSET $offset";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<title>Daftar Semua Sertifikat</title>
<link rel="stylesheet" href="css/style_dashboard.css" />
<style>
    /* Gaya Tabel Dasar */
    table {
        border-collapse: collapse;
        width: 100%;
        margin-bottom: 20px;
    }
    th, td {
        border: 1px solid #ddd;
        padding: 8px;
    }
    th { /* Gaya default untuk TH */
        background-color: #2c89e0ec;
        color: white;
    }
    /* Menengahkan teks di THEAD */
    table thead th {
        text-align: center;
    }
    /* Gaya Link Dasar */
    a {
        color: #2c89e0ec;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }

    /* Gaya Pagination */
    .pagination {
        margin-top: 20px;
        text-align: center;
    }
    .pagination a {
        color: black;
        float: left; /* Atau display: inline-block; */
        padding: 8px 16px;
        text-decoration: none;
        transition: background-color .3s;
        border: 1px solid #ddd;
        margin: 0 4px;
    }
    .pagination a.active {
        background-color: #4CAF50; /* Sesuaikan warnanya */
        color: white;
        border: 1px solid #4CAF50;
    }
    .pagination a:hover:not(.active) {
        background-color: #ddd;
    }
    .pagination span { 
        float: left; /* Atau display: inline-block; */
        padding: 8px 16px;
    }
</style>
</head>
<body>

<div class="container">
    <nav class="sidebar">
        <img src="img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
        <a href="<?= htmlspecialchars($dashboard_link) ?>">Dashboard</a>
        <a href="proposal.php">Proposal</a>
        <a href="lpj.php">LPJ</a>
        <a href="sertifikat.php" class="active">Sertifikat</a>
        <a href="banner.php">Banner</a>
        <a href="dokumentasi.php">Dokumentasi</a>
    </nav>

    <main class="main-content">
        <div class="header-top">
            <div class="welcome">
                <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['nama']) ?></h2>
            </div>
            <a href="logout.php" class="btn-logout">Logout</a>
        </div>

        <h2>Daftar Semua Sertifikat</h2>

        <table>
            <thead>
                <tr>
                    <th>Kode Sertifikat</th>
                    <th>Tanggal Setuju</th>
                    <th>Status</th>
                    <th>File</th>
                    <th>Nama Pengaju</th>
                    <th>Nama Kegiatan</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['kd_sertifikat']) ?></td>
                        <td><?= $row['tgl_setuju'] ? htmlspecialchars(date('d M Y', strtotime($row['tgl_setuju']))) : '-' ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td>
                            <?php if (!empty($row['file_path'])): ?>
                                <a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">Download</a>
                            <?php else: ?>
                                Tidak ada file
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($row['nama_user']) ?></td>
                        <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="6" style="text-align:center;">Belum ada sertifikat</td></tr>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if ($total_pages > 1) : ?>
        <div class="pagination">
            <?php if ($current_page > 1) : ?>
                <a href="sertifikat.php?page=1">&laquo; Awal</a>
            <?php endif; ?>

            <?php
            $num_links_shown = 2; 
            $start_page = max(1, $current_page - $num_links_shown);
            $end_page = min($total_pages, $current_page + $num_links_shown);

            if ($start_page > 1) {
                echo '<a href="sertifikat.php?page=1">1</a>';
                if ($start_page > 2) {
                    echo '<span>...</span>';
                }
            }

            for ($i = $start_page; $i <= $end_page; $i++) : ?>
                <a href="sertifikat.php?page=<?= $i ?>" class="<?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
            <?php endfor; ?>

            <?php
            if ($end_page < $total_pages) {
                if ($end_page < $total_pages - 1) {
                    echo '<span>...</span>';
                }
                echo '<a href="sertifikat.php?page=' . $total_pages . '">' . $total_pages . '</a>';
            }
            ?>

            <?php if ($current_page < $total_pages) : ?>
                <a href="sertifikat.php?page=<?= $total_pages ?>">Akhir &raquo;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

      <main>
</div>

</body>
</html>
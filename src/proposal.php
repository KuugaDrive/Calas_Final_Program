<?php
session_start();
include 'koneksi.php';

// Cek login dan role sebelum output HTML
if (!isset($_SESSION['logged_in']) || !in_array($_SESSION['role'], ['kepala_lab', 'asisten_lab', 'spv'])) {
    header('Location: login.php');
    exit;
}

// Tentukan link dashboard sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'kepala_lab') {
        $dashboard_link = 'dashboard_admin.php';
    } else {
        $dashboard_link = 'dashboard_user.php';
    }
} else {
    $dashboard_link = 'login.php'; // Fallback, meskipun seharusnya tidak tercapai jika cek login di atas bekerja
}

// --- PAGINATION LOGIC ---
$proposals_per_page = 5; // Jumlah proposal per halaman

// 1. Tentukan halaman aktif
$current_page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
if ($current_page < 1) {
    $current_page = 1;
}

// 2. Hitung total proposal (untuk pagination)
// Kita perlu memastikan COUNT query ini konsisten dengan kondisi JOIN pada query utama jika ada filter yang mungkin mengurangi jumlah.
// Dalam kasus ini, JOIN tidak akan mengurangi jumlah proposal, hanya menambahkan data.
$count_sql = "SELECT COUNT(p.kd_proposal) AS total_proposals 
              FROM proposal p
              JOIN kegiatan k ON p.kd_kegiatan = k.kd_kegiatan
              JOIN user u ON k.kd_user = u.kd_user"; // Menambahkan JOIN agar konsisten jika ada kondisi WHERE nantinya
$count_result = $conn->query($count_sql);
$total_proposals = 0;
if ($count_result && $count_result->num_rows > 0) {
    $total_proposals = $count_result->fetch_assoc()['total_proposals'];
}

// 3. Hitung total halaman
$total_pages = ceil($total_proposals / $proposals_per_page);
if ($current_page > $total_pages && $total_pages > 0) { // Jika halaman yang diminta melebihi total halaman
    $current_page = $total_pages; // Alihkan ke halaman terakhir
} elseif ($total_pages == 0 && $current_page > 1) {
    $current_page = 1; // Jika tidak ada proposal, pastikan tetap di halaman 1
}


// 4. Hitung offset
$offset = ($current_page - 1) * $proposals_per_page;

// 5. Modifikasi query utama dengan LIMIT dan OFFSET
// 5. Modifikasi query utama dengan LIMIT dan OFFSET
$sql = "SELECT p.kd_proposal, p.judul, p.tgl_upload, p.tgl_setuju, p.status, u.nama AS nama_user, k.nama_kegiatan
        FROM proposal p
        JOIN kegiatan k ON p.kd_kegiatan = k.kd_kegiatan
        JOIN user u ON k.kd_user = u.kd_user
        ORDER BY p.kd_proposal ASC  -- Diubah di sini
        LIMIT $proposals_per_page OFFSET $offset";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <title>Daftar Semua Proposal</title>
    <link rel="stylesheet" href="css/style_dashboard.css" />
    <style>
        /* Tambahkan style sederhana untuk pagination */
        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            color: black;
            float: left;
            padding: 8px 16px;
            text-decoration: none;
            transition: background-color .3s;
            border: 1px solid #ddd;
            margin: 0 4px;
        }

        .pagination a.active {
            background-color: #4CAF50; /* Atau warna tema Anda */
            color: white;
            border: 1px solid #4CAF50;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
        .pagination span { /* Untuk "..." atau teks non-link */
            float: left;
            padding: 8px 16px;
        }
    </style>
</head>

<body>

    <div class="container">
        <nav class="sidebar">
            <img src="img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
            <a href="<?= htmlspecialchars($dashboard_link) ?>">Dashboard</a>
            <a href="proposal.php" class="active">Proposal</a>
            <a href="lpj.php">LPJ</a>
            <a href="sertifikat.php">Sertifikat</a>
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

            <h2>Daftar Semua Proposal</h2>

            <table>
                <thead>
                    <tr>
                        <th>Kode Proposal</th>
                        <th>Judul</th>
                        <th>Tanggal Upload</th>
                        <th>Tanggal Setuju</th>
                        <th>Status</th>
                        <th>Nama Pengaju</th>
                        <th>Nama Kegiatan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result && $result->num_rows > 0) : ?>
                        <?php while ($row = $result->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($row['kd_proposal']) ?></td>
                                <td><?= htmlspecialchars($row['judul']) ?></td>
                                <td><?= htmlspecialchars(date('d M Y', strtotime($row['tgl_upload']))) // Format tanggal ?></td>
                                <td><?= $row['tgl_setuju'] ? htmlspecialchars(date('d M Y', strtotime($row['tgl_setuju']))) : '-' // Format tanggal jika ada ?></td>
                                <td><?= htmlspecialchars($row['status']) ?></td>
                                <td><?= htmlspecialchars($row['nama_user']) ?></td>
                                <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" style="text-align:center;">Belum ada proposal</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <?php if ($total_pages > 1) : ?>
            <div class="pagination">
                <?php if ($current_page > 1) : ?>
                    <a href="?page=1"> Awal</a>

                <?php endif; ?>

                <?php
                // Logika untuk menampilkan nomor halaman (misal: 1 2 ... 5 6 7 ... 9 10)
                // Untuk sederhana, kita tampilkan beberapa halaman di sekitar halaman aktif
                $num_links_shown = 2; // Jumlah link sebelum dan sesudah halaman aktif
                $start_page = max(1, $current_page - $num_links_shown);
                $end_page = min($total_pages, $current_page + $num_links_shown);

                if ($start_page > 1) {
                    echo '<a href="?page=1">1</a>';
                    if ($start_page > 2) {
                        echo '<span>...</span>';
                    }
                }

                for ($i = $start_page; $i <= $end_page; $i++) : ?>
                    <a href="?page=<?= $i ?>" class="<?= ($i == $current_page) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>

                <?php
                if ($end_page < $total_pages) {
                    if ($end_page < $total_pages - 1) {
                        echo '<span>...</span>';
                    }
                    echo '<a href="?page=' . $total_pages . '">' . $total_pages . '</a>';
                }
                ?>

                <?php if ($current_page < $total_pages) : ?>
                    <a href="?page=<?= $total_pages ?>">Akhir</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>


        </main>
    </div>

</body>
</html>
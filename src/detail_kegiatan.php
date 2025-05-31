<?php
session_start();
include 'koneksi.php';

// Cek user login
if (!isset($_SESSION['kd_user'])) {
    header('Location: login.php');
    exit;
}

// Tentukan link dashboard sesuai role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'kepala_lab') {
        $dashboard_link = 'dashboard_admin.php';
    } elseif ($_SESSION['role'] === 'asisten_lab') { // Hanya asisten lab dan kepala lab yang bisa lihat dashboard user/admin
        $dashboard_link = 'dashboard_user.php';
    } elseif ($_SESSION['role'] === 'spv') { // Tambahkan spv jika mereka punya dashboard sendiri atau ke user
        $dashboard_link = 'dashboard_user.php'; // atau dashboard_spv.php jika ada
    }
    else {
        // Role lain atau tidak dikenal, mungkin ke login atau halaman error
        $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Role tidak dikenal.'];
        header('Location: login.php');
        exit;
    }
} else {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Sesi role tidak ditemukan.'];
    header('Location: login.php');
    exit;
}

// Ambil kd_kegiatan dari parameter GET
$kd_kegiatan = $_GET['kd'] ?? '';

if (!$kd_kegiatan) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Kode kegiatan tidak ditemukan.'];
    header('Location: ' . $dashboard_link); // Redirect ke dashboard jika kd_kegiatan tidak ada
    exit;
}

// Ambil data kegiatan utama
$sql_kegiatan = "SELECT * FROM kegiatan WHERE kd_kegiatan = ?";
$stmt_kegiatan = $conn->prepare($sql_kegiatan);
$stmt_kegiatan->bind_param("s", $kd_kegiatan);
$stmt_kegiatan->execute();
$kegiatan = $stmt_kegiatan->get_result()->fetch_assoc();
$stmt_kegiatan->close();

if (!$kegiatan) {
    $_SESSION['flash_message'] = ['type' => 'error', 'message' => 'Data kegiatan tidak ditemukan.'];
    header('Location: ' . $dashboard_link);
    exit;
}

// --- Logika Pesan Flash ---
$flash_message = null;
if (isset($_SESSION['flash_message'])) {
    $flash_message = $_SESSION['flash_message'];
    unset($_SESSION['flash_message']);
}

// --- Pengaturan Pagination Global ---
$items_per_page = 5;
$all_current_pages = []; // Untuk menyimpan page saat ini dari semua tabel

$document_types = ['proposal', 'lpj', 'sertifikat', 'banner', 'dokumentasi'];
$results_data = [];
$total_pages_data = [];

// --- Fungsi Helper untuk URL Pagination ---
function generate_detail_kegiatan_url($kd_kegiatan, $all_pages_info, $type_to_change_page = null, $new_page = null) {
    $params = ['kd' => $kd_kegiatan];
    foreach ($all_pages_info as $type => $page_num) {
        if ($type === $type_to_change_page) {
            if ($new_page > 1) { // Hanya tambahkan jika halaman > 1
                $params['page_' . $type] = $new_page;
            }
        } elseif ($page_num > 1) { // Hanya tambahkan jika halaman > 1
             $params['page_' . $type] = $page_num;
        }
    }
    return 'detail_kegiatan.php?' . http_build_query($params);
}

// --- Proses Pagination untuk setiap tipe dokumen ---
foreach ($document_types as $type) {
    $page_param_name = 'page_' . $type;
    $all_current_pages[$type] = isset($_GET[$page_param_name]) && is_numeric($_GET[$page_param_name]) ? (int)$_GET[$page_param_name] : 1;
    if ($all_current_pages[$type] < 1) $all_current_pages[$type] = 1;

    // Hitung total item
    $count_sql = "SELECT COUNT(*) as total FROM $type WHERE kd_kegiatan = ?";
    $stmt_count = $conn->prepare($count_sql);
    $stmt_count->bind_param("s", $kd_kegiatan);
    $stmt_count->execute();
    $total_items = $stmt_count->get_result()->fetch_assoc()['total'];
    $stmt_count->close();

    $total_pages_data[$type] = ceil($total_items / $items_per_page);
    if ($all_current_pages[$type] > $total_pages_data[$type] && $total_pages_data[$type] > 0) {
        $all_current_pages[$type] = $total_pages_data[$type];
    } elseif ($total_pages_data[$type] == 0 && $all_current_pages[$type] > 1) {
         $all_current_pages[$type] = 1;
    }


    $offset = ($all_current_pages[$type] - 1) * $items_per_page;

    // Ambil data untuk halaman saat ini
    // Menggunakan kd_{type} sebagai kolom pengurutan, pastikan kolom ini ada.
    // Jika tidak ada, gunakan kolom lain yang sesuai (misal tgl_upload DESC atau kd_primary_key ASC)
    $data_sql = "SELECT * FROM $type WHERE kd_kegiatan = ? ORDER BY kd_$type ASC LIMIT ? OFFSET ?";
     if ($type === 'dokumentasi' && !property_exists((object)$conn->query("SELECT * FROM dokumentasi LIMIT 1")->fetch_assoc()??[], 'kd_dokumentasi')) { // Cek jika kd_dokumentasi tidak ada
        // Fallback order jika kd_dokumentasi tidak ada (misalnya untuk tabel dokumentasi yang mungkin punya struktur berbeda)
        // Atau pastikan semua tabel (proposal, lpj, sertifikat, banner, dokumentasi) punya kolom kd_{nama_tabel}
        $data_sql = "SELECT * FROM $type WHERE kd_kegiatan = ? ORDER BY tgl_upload DESC LIMIT ? OFFSET ?"; // Contoh fallback
    }


    $stmt_data = $conn->prepare($data_sql);
    $stmt_data->bind_param("sii", $kd_kegiatan, $items_per_page, $offset);
    $stmt_data->execute();
    $results_data[$type] = $stmt_data->get_result();
    $stmt_data->close();
}

// --- Fungsi Helper untuk Menampilkan Blok Pagination HTML ---
function display_pagination_html($base_file_ignored, $kd_kegiatan, $all_current_pages_info, $type_key, $current_page_for_this_type, $total_pages_for_this_type) {
    if ($total_pages_for_this_type <= 1) {
        return;
    }
    echo '<div class="pagination">';

    // Tombol Awal dan Sebelumnya
    if ($current_page_for_this_type > 1) {
        $url_first = generate_detail_kegiatan_url($kd_kegiatan, $all_current_pages_info, $type_key, 1);
        echo "<a href='" . htmlspecialchars($url_first) . "'>&laquo; Awal</a>";
        $url_prev = generate_detail_kegiatan_url($kd_kegiatan, $all_current_pages_info, $type_key, $current_page_for_this_type - 1);
    }

    // Nomor Halaman
    $num_links_shown = 2;
    $start_page = max(1, $current_page_for_this_type - $num_links_shown);
    $end_page = min($total_pages_for_this_type, $current_page_for_this_type + $num_links_shown);

    if ($start_page > 1) {
        $url_page_1 = generate_detail_kegiatan_url($kd_kegiatan, $all_current_pages_info, $type_key, 1);
        echo "<a href='" . htmlspecialchars($url_page_1) . "'>1</a>";
        if ($start_page > 2) {
            echo "<span>...</span>";
        }
    }

    for ($i = $start_page; $i <= $end_page; $i++) {
        $url_page_i = generate_detail_kegiatan_url($kd_kegiatan, $all_current_pages_info, $type_key, $i);
        $active_class = ($i == $current_page_for_this_type) ? 'active' : '';
        echo "<a href='" . htmlspecialchars($url_page_i) . "' class='$active_class'>$i</a>";
    }

    if ($end_page < $total_pages_for_this_type) {
        if ($end_page < $total_pages_for_this_type - 1) {
            echo "<span>...</span>";
        }
        $url_last_page = generate_detail_kegiatan_url($kd_kegiatan, $all_current_pages_info, $type_key, $total_pages_for_this_type);
        echo "<a href='" . htmlspecialchars($url_last_page) . "'>$total_pages_for_this_type</a>";
    }

    // Tombol Berikutnya dan Akhir
    if ($current_page_for_this_type < $total_pages_for_this_type) {
        $url_next = generate_detail_kegiatan_url($kd_kegiatan, $all_current_pages_info, $type_key, $current_page_for_this_type + 1);
        $url_last = generate_detail_kegiatan_url($kd_kegiatan, $all_current_pages_info, $type_key, $total_pages_for_this_type);
        echo "<a href='" . htmlspecialchars($url_last) . "'>Akhir &raquo;</a>";
    }
    echo '</div>';
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Detail Kegiatan <?= htmlspecialchars($kegiatan['nama_kegiatan']) ?></title>
    <link rel="stylesheet" href="css/style_dashboard.css" />
    <link rel="stylesheet" href="css/style_upload.css" />
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 10px; /* Disesuaikan agar pagination tidak terlalu jauh */
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        th {
            background-color: #2c89e0ec;
            color: white;
            /* text-align: left; akan ditimpa oleh rule di bawah jika ada */
        }
        table thead th, table th { /* Memastikan semua th (termasuk di tbody jika ada) mengikuti ini */
             text-align: center;
        }

        a {
            color: #2c89e0ec;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .btn-primary {
            background-color: #6f42c1;
            color: white;
            padding: 10px 20px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-weight: 600;
        }
        .tambah-data-container { margin: 20px 0; }
        .dropdown-menu {
            position: absolute; background: white; border: 1px solid #ddd;
            border-radius: 6px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            min-width: 300px; max-height: 400px; overflow-y: auto;
            display: none; right: 0; /* margin-bottom: 8px; Dihilangkan agar tidak mendorong konten */
            z-index: 1001; padding: 15px; top: 100%; /* Posisi dropdown di bawah tombol */
        }
        .dropdown-menu form { margin-bottom: 15px; }
        .dropdown-menu form:last-child { margin-bottom: 0; }
        .dropdown-menu label { display: block; margin-bottom: 5px; font-weight: bold;}
        .dropdown-menu input[type="file"] { display: block; margin-bottom: 8px; }

        .dropdown-wrapper { display: inline-block; position: relative; }

        /* Style Flash Message */
        .flash-message { padding: 15px; margin-bottom: 20px; border: 1px solid transparent; border-radius: 4px; }
        .flash-message.success { color: #155724; background-color: #d4edda; border-color: #c3e6cb; }
        .flash-message.error { color: #721c24; background-color: #f8d7da; border-color: #f5c6cb; }

        /* Style Pagination */
        .pagination { margin-top: 15px; margin-bottom: 25px; text-align: center; clear: both; }
        .pagination a {
            color: black; display: inline-block; /* Mengganti float dengan inline-block */
            padding: 8px 16px; text-decoration: none;
            transition: background-color .3s; border: 1px solid #ddd; margin: 0 4px;
        }
        .pagination a.active { background-color: #4CAF50; color: white; border: 1px solid #4CAF50; }
        .pagination a:hover:not(.active) { background-color: #ddd; }
        .pagination span { display: inline-block; padding: 8px 16px; }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <img src="img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
            <a href="<?= htmlspecialchars($dashboard_link) ?>" class="active">Dashboard</a>
            <a href="proposal.php">Proposal</a>
            <a href="lpj.php">LPJ</a>
            <a href="sertifikat.php">Sertifikat</a>
            <a href="banner.php">Banner</a>
            <a href="dokumentasi.php">Dokumentasi</a>
        </nav>
        <main class="main-content">
            <?php if ($flash_message): ?>
                <div class="flash-message <?= htmlspecialchars($flash_message['type']) ?>">
                    <?= htmlspecialchars($flash_message['message']) ?>
                </div>
                 <?php
                // Opsi jika ingin menggunakan JavaScript alert juga (biasanya salah satu saja)
                // if ($flash_message['type'] === 'success') { // Hanya untuk pesan sukses dari upload
                //     echo "<script>alert('" . htmlspecialchars($flash_message['message'], ENT_QUOTES, 'UTF-8') . "');</script>";
                // }
                ?>
            <?php endif; ?>

            <h2>Detail Kegiatan: <?= htmlspecialchars($kegiatan['nama_kegiatan']) ?></h2>
            <p><strong>Tanggal Kegiatan:</strong> <?= htmlspecialchars(date('d M Y', strtotime($kegiatan['tgl_kegiatan']))) ?></p>

            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'asisten_lab'): ?>
            <div class="tambah-data-container">
                <div class="dropdown-wrapper">
                    <button id="btnTambahData" class="btn-primary">Upload Data</button>
                    <div id="dropdownMenu" class="dropdown-menu">
                        <form action="upload_proposal.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="kd" value="<?= htmlspecialchars($kd_kegiatan) ?>">
                            <label for="proposal_file">Upload Proposal (.doc, .docx):</label>
                            <input type="file" name="proposal_file" id="proposal_file" accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                            <button type="submit" class="btn-primary" style="margin-top: 5px;">Upload Proposal</button>
                        </form>
                        <form action="upload_lpj.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="kd" value="<?= htmlspecialchars($kd_kegiatan) ?>">
                            <label for="lpj_file">Upload LPJ (.doc, .docx):</label>
                            <input type="file" name="lpj_file" id="lpj_file" accept=".doc,.docx,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required>
                            <button type="submit" class="btn-primary" style="margin-top: 5px;">Upload LPJ</button>
                        </form>
                        <form action="upload_sertifikat.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="kd" value="<?= htmlspecialchars($kd_kegiatan) ?>">
                            <label for="sertifikat_file">Upload Sertifikat (.pdf, .jpg, .png):</label>
                            <input type="file" name="sertifikat_file" id="sertifikat_file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" required>
                            <button type="submit" class="btn-primary" style="margin-top: 5px;">Upload Sertifikat</button>
                        </form>
                        <form action="upload_banner.php" method="post" enctype="multipart/form-data">
                             <input type="hidden" name="kd" value="<?= htmlspecialchars($kd_kegiatan) ?>">
                             <label for="banner_file">Upload Banner (.jpg, .png, .gif):</label>
                             <input type="file" name="banner_file" id="banner_file" accept=".jpg,.jpeg,.png,.gif,image/jpeg,image/png,image/gif" required>
                             <button type="submit" class="btn-primary" style="margin-top: 5px;">Upload Banner</button>
                        </form>
                        <form action="upload_dokumentasi.php" method="post" enctype="multipart/form-data">
                            <input type="hidden" name="kd" value="<?= htmlspecialchars($kd_kegiatan) ?>">
                            <label for="dokumentasi_file">Upload Dokumentasi (zip/rar/jpg/png):</label>
                            <input type="file" name="dokumentasi_file" id="dokumentasi_file" accept=".zip,.rar,.jpg,.jpeg,.png,application/zip,application/x-rar-compressed,image/jpeg,image/png" required>
                            <button type="submit" class="btn-primary" style="margin-top: 5px;">Upload Dokumentasi</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <h3>Proposal</h3>
            <table>
                <thead><tr>
                    <th>Judul</th><th>Tanggal Upload</th><th>Status</th><th>Tanggal Setuju</th>
                    <?php if ($_SESSION['role'] === 'kepala_lab'): ?><th>Aksi</th><?php endif; ?>
                </tr></thead>
                <tbody>
                <?php if ($results_data['proposal']->num_rows === 0): ?>
                    <tr><td colspan="<?= $_SESSION['role'] === 'kepala_lab' ? '5' : '4' ?>">Belum ada data proposal.</td></tr>
                <?php else: ?>
                    <?php while ($row = $results_data['proposal']->fetch_assoc()): ?>
                    <tr>
                        <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" download><?= htmlspecialchars($row['judul']) ?></a></td>
                        <td><?= htmlspecialchars(date('d M Y ', strtotime($row['tgl_upload']))) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $row['tgl_setuju'] ? htmlspecialchars(date('d M Y ', strtotime($row['tgl_setuju']))) : '-' ?></td>
                        <?php if ($_SESSION['role'] === 'kepala_lab'): ?>
                        <td>
                            <form method="POST" action="update_status.php" style="display:inline;">
                                <input type="hidden" name="jenis" value="proposal" />
                                <input type="hidden" name="kd_item" value="<?= htmlspecialchars($row['kd_proposal']) ?>" />
                                <input type="hidden" name="kd_kegiatan_redirect" value="<?= htmlspecialchars($kd_kegiatan) ?>" />
                                <select name="status" required>
                                    <option value="Proses" <?= $row['status'] == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="Terima" <?= $row['status'] == 'Terima' ? 'selected' : '' ?>>Terima</option>
                                    <option value="Tolak" <?= $row['status'] == 'Tolak' ? 'selected' : '' ?>>Tolak</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php display_pagination_html('detail_kegiatan.php', $kd_kegiatan, $all_current_pages, 'proposal', $all_current_pages['proposal'], $total_pages_data['proposal']); ?>

            <h3>LPJ</h3>
            <table>
                <thead><tr>
                    <th>Judul</th><th>Tanggal Upload</th><th>Status</th><th>Tanggal Setuju</th>
                    <?php if ($_SESSION['role'] === 'kepala_lab'): ?><th>Aksi</th><?php endif; ?>
                </tr></thead>
                <tbody>
                <?php if ($results_data['lpj']->num_rows === 0): ?>
                    <tr><td colspan="<?= $_SESSION['role'] === 'kepala_lab' ? '5' : '4' ?>">Belum ada data LPJ.</td></tr>
                <?php else: ?>
                    <?php while ($row = $results_data['lpj']->fetch_assoc()): ?>
                    <tr>
                        <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank" download><?= htmlspecialchars($row['judul']) ?></a></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($row['tgl_upload']))) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $row['tgl_setuju'] ? htmlspecialchars(date('d M Y', strtotime($row['tgl_setuju']))) : '-' ?></td>
                         <?php if ($_SESSION['role'] === 'kepala_lab'): ?>
                        <td>
                            <form method="POST" action="update_status.php" style="display:inline;">
                                <input type="hidden" name="jenis" value="lpj" />
                                <input type="hidden" name="kd_item" value="<?= htmlspecialchars($row['kd_lpj']) ?>" />
                                <input type="hidden" name="kd_kegiatan_redirect" value="<?= htmlspecialchars($kd_kegiatan) ?>" />
                                <select name="status" required>
                                    <option value="Proses" <?= $row['status'] == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="Terima" <?= $row['status'] == 'Terima' ? 'selected' : '' ?>>Terima</option>
                                    <option value="Tolak" <?= $row['status'] == 'Tolak' ? 'selected' : '' ?>>Tolak</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php display_pagination_html('detail_kegiatan.php', $kd_kegiatan, $all_current_pages, 'lpj', $all_current_pages['lpj'], $total_pages_data['lpj']); ?>

            <h3>Sertifikat</h3>
            <table>
                <thead><tr>
                    <th>Judul</th><th>File</th><th>Tanggal Upload</th><th>Status</th><th>Tanggal Setuju</th>
                    <?php if ($_SESSION['role'] === 'kepala_lab'): ?><th>Aksi</th><?php endif; ?>
                </tr></thead>
                <tbody>
                <?php if ($results_data['sertifikat']->num_rows === 0): ?>
                    <tr><td colspan="<?= $_SESSION['role'] === 'kepala_lab' ? '6' : '5' ?>">Belum ada data sertifikat.</td></tr>
                <?php else: ?>
                    <?php while ($row = $results_data['sertifikat']->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul'] ?? 'N/A') // Asumsi ada kolom judul, jika tidak ganti ?></td>
                        <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">Lihat Sertifikat</a></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($row['tgl_upload']))) // Asumsi ada tgl_upload ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $row['tgl_setuju'] ? htmlspecialchars(date('d M Y', strtotime($row['tgl_setuju']))) : '-' ?></td>
                        <?php if ($_SESSION['role'] === 'kepala_lab'): ?>
                        <td>
                            <form method="POST" action="update_status.php" style="display:inline;">
                                <input type="hidden" name="jenis" value="sertifikat" />
                                <input type="hidden" name="kd_item" value="<?= htmlspecialchars($row['kd_sertifikat']) ?>" />
                                <input type="hidden" name="kd_kegiatan_redirect" value="<?= htmlspecialchars($kd_kegiatan) ?>" />
                                <select name="status" required>
                                    <option value="Proses" <?= $row['status'] == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="Terima" <?= $row['status'] == 'Terima' ? 'selected' : '' ?>>Terima</option>
                                    <option value="Tolak" <?= $row['status'] == 'Tolak' ? 'selected' : '' ?>>Tolak</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php display_pagination_html('detail_kegiatan.php', $kd_kegiatan, $all_current_pages, 'sertifikat', $all_current_pages['sertifikat'], $total_pages_data['sertifikat']); ?>

            <h3>Banner</h3>
             <table>
                <thead><tr>
                    <th>Judul</th><th>File</th><th>Tanggal Upload</th><th>Status</th><th>Tanggal Setuju</th>
                    <?php if ($_SESSION['role'] === 'kepala_lab'): ?><th>Aksi</th><?php endif; ?>
                </tr></thead>
                <tbody>
                <?php if ($results_data['banner']->num_rows === 0): ?>
                    <tr><td colspan="<?= $_SESSION['role'] === 'kepala_lab' ? '6' : '5' ?>">Belum ada data banner.</td></tr>
                <?php else: ?>
                    <?php while ($row = $results_data['banner']->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul'] ?? 'N/A') ?></td>
                        <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">Lihat Banner</a></td>
                        <td><?= htmlspecialchars(date('d M Y ', strtotime($row['tgl_upload']))) ?></td>
                        <td><?= htmlspecialchars($row['status']) ?></td>
                        <td><?= $row['tgl_setuju'] ? htmlspecialchars(date('d M Y ', strtotime($row['tgl_setuju']))) : '-' ?></td>
                        <?php if ($_SESSION['role'] === 'kepala_lab'): ?>
                        <td>
                            <form method="POST" action="update_status.php" style="display:inline;">
                                <input type="hidden" name="jenis" value="banner" />
                                <input type="hidden" name="kd_item" value="<?= htmlspecialchars($row['kd_banner']) ?>" />
                                 <input type="hidden" name="kd_kegiatan_redirect" value="<?= htmlspecialchars($kd_kegiatan) ?>" />
                                <select name="status" required>
                                    <option value="Proses" <?= $row['status'] == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="Terima" <?= $row['status'] == 'Terima' ? 'selected' : '' ?>>Terima</option>
                                    <option value="Tolak" <?= $row['status'] == 'Tolak' ? 'selected' : '' ?>>Tolak</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php display_pagination_html('detail_kegiatan.php', $kd_kegiatan, $all_current_pages, 'banner', $all_current_pages['banner'], $total_pages_data['banner']); ?>

            <h3>Dokumentasi</h3>
            <table>
                <thead><tr>
                    <th>Judul</th><th>File</th><th>Tanggal Upload</th>
                    <?php if ($_SESSION['role'] === 'kepala_lab'): ?><th>Aksi</th><?php endif; ?>
                </tr></thead>
                <tbody>
                <?php if ($results_data['dokumentasi']->num_rows === 0): ?>
                    <tr><td colspan="<?= $_SESSION['role'] === 'kepala_lab' ? '4' : '3' ?>">Belum ada data dokumentasi.</td></tr>
                <?php else: ?>
                    <?php while ($row = $results_data['dokumentasi']->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['judul'] ?? 'N/A') ?></td>
                        <td><a href="<?= htmlspecialchars($row['file_path']) ?>" target="_blank">Lihat Dokumentasi</a></td>
                        <td><?= htmlspecialchars(date('d M Y', strtotime($row['tgl_upload']))) ?></td>
                        <?php if ($_SESSION['role'] === 'kepala_lab' && isset($row['kd_dokumentasi'])): // Asumsi kd_dokumentasi ada untuk aksi ?>
                        <td>
                            <form method="POST" action="update_status.php" style="display:inline;">
                                <input type="hidden" name="jenis" value="dokumentasi" />
                                <input type="hidden" name="kd_item" value="<?= htmlspecialchars($row['kd_dokumentasi']) ?>" />
                                 <input type="hidden" name="kd_kegiatan_redirect" value="<?= htmlspecialchars($kd_kegiatan) ?>" />
                                <select name="status" required> <option value="Proses" <?= ($row['status'] ?? '') == 'Proses' ? 'selected' : '' ?>>Proses</option>
                                    <option value="Terima" <?= ($row['status'] ?? '') == 'Terima' ? 'selected' : '' ?>>Terima</option>
                                    <option value="Tolak" <?= ($row['status'] ?? '') == 'Tolak' ? 'selected' : '' ?>>Tolak</option>
                                </select>
                                <button type="submit">Update</button>
                            </form>
                        </td>
                        <?php elseif ($_SESSION['role'] === 'kepala_lab'): ?>
                            <td>-</td> <?php endif; ?>
                    </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
            <?php display_pagination_html('detail_kegiatan.php', $kd_kegiatan, $all_current_pages, 'dokumentasi', $all_current_pages['dokumentasi'], $total_pages_data['dokumentasi']); ?>

        </main>
    </div>

    <script>
        const btnTambahData = document.getElementById('btnTambahData');
        const dropdownMenu = document.getElementById('dropdownMenu');

        if (btnTambahData) {
            btnTambahData.addEventListener('click', (event) => {
                event.stopPropagation(); // Mencegah event sampai ke window listener langsung
                if (dropdownMenu.style.display === 'none' || dropdownMenu.style.display === '') {
                    dropdownMenu.style.display = 'block';
                } else {
                    dropdownMenu.style.display = 'none';
                }
            });
        }

        // Klik di luar dropdown untuk menutup
        window.addEventListener('click', function(e) {
            if (dropdownMenu && dropdownMenu.style.display === 'block') { // Hanya proses jika dropdown terlihat
                 if (btnTambahData && !btnTambahData.contains(e.target) && !dropdownMenu.contains(e.target)) {
                    dropdownMenu.style.display = 'none';
                }
            }
        });
    </script>

</body>
</html>
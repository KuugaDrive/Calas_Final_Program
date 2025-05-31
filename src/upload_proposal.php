<?php
session_start(); // Pastikan session_start() di paling atas
include 'koneksi.php';

// 1. Cek Login Pengguna
if (!isset($_SESSION['kd_user'])) {
    // Jika belum login, siapkan pesan error dan redirect ke halaman login
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Anda harus login terlebih dahulu untuk mengakses halaman ini.'
    ];
    header('Location: login.php');
    exit;
}

// Tentukan link dashboard berdasarkan role (digunakan untuk redirect umum jika perlu)
// Pastikan $_SESSION['role'] diset saat login
if (isset($_SESSION['role'])) {
    $dashboard_link = $_SESSION['role'] === 'kepala_lab' ? 'dashboard_admin.php' : 'dashboard_user.php';
} else {
    // Fallback jika role tidak ada di session, mungkin redirect ke login untuk perbaikan sesi
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Role pengguna tidak ditemukan. Silakan login kembali.'
    ];
    header('Location: login.php');
    exit;
}


// 2. Hanya proses jika metode request adalah POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kd_kegiatan = $_POST['kd'] ?? ''; // Ambil kd_kegiatan dari form (pastikan nama field 'kd')

    // 3. Validasi Kode Kegiatan
    if (empty($kd_kegiatan)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Kode kegiatan tidak valid atau tidak ditemukan.'
        ];
        // Redirect ke halaman dashboard atau halaman daftar kegiatan jika kd_kegiatan kosong
        header('Location: ' . $dashboard_link);
        exit;
    }

    // 4. Validasi File Upload
    if (!isset($_FILES['proposal_file']) || $_FILES['proposal_file']['error'] != UPLOAD_ERR_OK) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'File gagal diupload. Pastikan Anda memilih file dengan benar.'
        ];
        header('Location: detail_kegiatan.php?kd=' . urlencode($kd_kegiatan));
        exit;
    }

    $file = $_FILES['proposal_file'];

    // 5. Validasi Tipe File
    $allowed_types = [
        'application/msword', // .doc
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' // .docx
    ];
    if (!in_array($file['type'], $allowed_types)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Tipe file tidak valid. File harus berupa .doc atau .docx.'
        ];
        header('Location: detail_kegiatan.php?kd=' . urlencode($kd_kegiatan));
        exit;
    }

    // 6. Validasi Ukuran File (max 30MB)
    $max_file_size = 30 * 1024 * 1024; // 30 MB
    if ($file['size'] > $max_file_size) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Ukuran file terlalu besar. Maksimal ukuran file adalah 30MB.'
        ];
        header('Location: detail_kegiatan.php?kd=' . urlencode($kd_kegiatan));
        exit;
    }

    // 7. Persiapan Direktori dan Nama File
    $upload_dir = 'uploads/proposal/';
    if (!is_dir($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true) && !is_dir($upload_dir)) { // Cek lagi setelah mkdir
             $_SESSION['flash_message'] = [
                'type' => 'error',
                'message' => 'Gagal membuat direktori upload. Hubungi administrator.'
            ];
            header('Location: detail_kegiatan.php?kd=' . urlencode($kd_kegiatan));
            exit;
        }
    }

    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION)); // Ambil ekstensi file
    $file_name_only = pathinfo($file['name'], PATHINFO_FILENAME); // Ambil nama file tanpa ekstensi
    $judul_proposal = $file_name_only; // Judul proposal diambil dari nama file
    $file_name_unik = 'proposal_' . preg_replace('/[^a-zA-Z0-9_.-]/', '_', $kd_kegiatan) . '_' . time() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name_unik;

    // 8. Pindahkan File
    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Gagal memindahkan file yang diupload. Periksa izin folder.'
        ];
        header('Location: detail_kegiatan.php?kd=' . urlencode($kd_kegiatan));
        exit;
    }

    // 9. Generate Kode Proposal Baru
    $result = $conn->query("SELECT MAX(kd_proposal) AS max_kd FROM proposal");
    $row = $result->fetch_assoc();
    $last_kd = $row['max_kd'];

    if ($last_kd) {
        $last_number = intval(substr($last_kd, 1)); // Ambil angka setelah 'P'
        $next_number = $last_number + 1;
    } else {
        $next_number = 1;
    }
    $kd_proposal = 'P' . str_pad($next_number, 3, '0', STR_PAD_LEFT);

    // 10. Masukkan ke Database
    $stmt = $conn->prepare("INSERT INTO proposal (kd_proposal, kd_kegiatan, judul, file_path, tgl_upload, status) VALUES (?, ?, ?, ?, NOW(), 'Proses')");
    // Judul proposal sudah diambil dari nama file di atas
    $stmt->bind_param("ssss", $kd_proposal, $kd_kegiatan, $judul_proposal, $file_path);

    if ($stmt->execute()) {
        $_SESSION['flash_message'] = [
            'type' => 'success',
            'message' => 'Proposal berhasil diupload!'
        ];
        header('Location: detail_kegiatan.php?kd=' . urlencode($kd_kegiatan));
        exit;
    } else {
        // Jika gagal simpan ke DB, hapus file yang sudah terupload
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        $_SESSION['flash_message'] = [
            'type' => 'error',
            'message' => 'Gagal menyimpan data proposal ke database. Error: ' . $stmt->error
        ];
        header('Location: detail_kegiatan.php?kd=' . urlencode($kd_kegiatan));
        exit;
    }
    $stmt->close();
    $conn->close();

} else {
    // Jika metode request bukan POST
    $_SESSION['flash_message'] = [
        'type' => 'error',
        'message' => 'Metode request tidak valid.'
    ];
    header('Location: ' . $dashboard_link); // Redirect ke dashboard
    exit;
}
?>
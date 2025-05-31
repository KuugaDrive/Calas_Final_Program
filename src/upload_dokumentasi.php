<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['kd_user']) || $_SESSION['role'] !== 'asisten_lab') {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kd_kegiatan = $_POST['kd'] ?? '';

    if (!$kd_kegiatan) {
        die('Kode kegiatan tidak ditemukan.');
    }

    if (!isset($_FILES['dokumentasi_file']) || $_FILES['dokumentasi_file']['error'] != UPLOAD_ERR_OK) {
        die('File gagal diupload.');
    }

    $file = $_FILES['dokumentasi_file'];

    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png'];

    if (!in_array($file['type'], $allowed_types)) {
        die('File harus berupa JPG/JPEG/PNG.');
    }

    if ($file['size'] > 15 * 1024 * 1024) { // max 15MB
        die('File terlalu besar, maksimal 15MB.');
    }

    $upload_dir = 'uploads/dokumentasi/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $file_name = 'dokumentasi_' . $kd_kegiatan . '_' . time() . '.' . $file_ext;
    $file_path = $upload_dir . $file_name;

    if (!move_uploaded_file($file['tmp_name'], $file_path)) {
        die('Gagal menyimpan file.');
    }

    // Generate kd_dokumentasi unik
    $result = $conn->query("SELECT MAX(kd_dokumentasi) AS max_kd FROM dokumentasi");
    $row = $result->fetch_assoc();
    $last_kd = $row['max_kd'];

    if ($last_kd) {
        $last_number = intval(substr($last_kd, 1)); // hapus prefix 'D'
        $next_number = $last_number + 1;
    } else {
        $next_number = 1;
    }

    $kd_dokumentasi = 'D' . str_pad($next_number, 3, '0', STR_PAD_LEFT);

    $judul = pathinfo($file['name'], PATHINFO_FILENAME);

    $stmt = $conn->prepare("INSERT INTO dokumentasi (kd_dokumentasi, kd_kegiatan, judul, file_path, tgl_upload, status) VALUES (?, ?, ?, ?, NOW(), 'Proses')");
    if (!$stmt) {
        die("Prepare statement gagal: " . $conn->error);
    }
    $stmt->bind_param("ssss", $kd_dokumentasi, $kd_kegiatan, $judul, $file_path);

    if ($stmt->execute()) {
        header("Location: detail_kegiatan.php?kd=" . urlencode($kd_kegiatan));
        exit;
    } else {
        unlink($file_path);
        die('Gagal menyimpan data ke database. Error: ' . $stmt->error);
    }
} else {
    die('Metode request tidak valid.');
}

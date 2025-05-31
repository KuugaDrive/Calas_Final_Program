<?php
session_start();
include 'koneksi.php';

require '../vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

if (!isset($_SESSION['kd_user']) || $_SESSION['role'] !== 'kepala_lab') {
    header('Location: login.php');
    exit;
}

$jenis = $_POST['jenis'] ?? '';
$kd = $_POST['kd_item'] ?? '';
$status = $_POST['status'] ?? '';

if (!$jenis || !$kd || !$status) {
    die('Data tidak lengkap.');
}

$allowed_status = ['Proses', 'Terima', 'Setuju', 'Tolak'];
if (!in_array($status, $allowed_status)) {
    die('Status tidak valid.');
}

switch ($jenis) {
    case 'proposal':
        $table = 'proposal';
        $id_column = 'kd_proposal';
        $placeholderQR = 'TTD_Kepala_Lab';
        $verifikasiBaseURL = 'http://localhost/calas/src/verifikasi_proposal.php?kd_kegiatan=';
        break;
    case 'lpj':
        $table = 'lpj';
        $id_column = 'kd_lpj';
        $placeholderQR = 'TTD_Kepala_Lab';
        $verifikasiBaseURL = 'http://localhost/calas/src/verifikasi_lpj.php?kd_kegiatan=';
        break;
    case 'sertifikat':
        $table = 'sertifikat';
        $id_column = 'kd_sertifikat';
        break;
    case 'banner':
        $table = 'banner';
        $id_column = 'kd_banner';
        break;
    case 'dokumentasi':
        $table = 'dokumentasi';
        $id_column = 'kd_dokumentasi';
        break;
    default:
        die('Jenis data tidak valid.');
}

// Tentukan tanggal setuju jika status Terima atau Setuju
if ($status === 'Terima' || $status === 'Setuju') {
    $tgl_setuju = date('Y-m-d');
} else {
    $tgl_setuju = null;
}

// Update status dan tanggal setuju
if ($tgl_setuju !== null) {
    $sql = "UPDATE $table SET status = ?, tgl_setuju = ? WHERE $id_column = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Error prepare statement: " . $conn->error);
    $stmt->bind_param("sss", $status, $tgl_setuju, $kd);
} else {
    $sql = "UPDATE $table SET status = ?, tgl_setuju = NULL WHERE $id_column = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Error prepare statement: " . $conn->error);
    $stmt->bind_param("ss", $status, $kd);
}

if (!$stmt->execute()) {
    die("Gagal update status: " . $stmt->error);
}

// Ambil kd_kegiatan dari tabel yang sesuai
$sql2 = "SELECT kd_kegiatan FROM $table WHERE $id_column = ?";
$stmt2 = $conn->prepare($sql2);
if (!$stmt2) die("Error prepare statement: " . $conn->error);
$stmt2->bind_param("s", $kd);
$stmt2->execute();
$result2 = $stmt2->get_result();
$row2 = $result2->fetch_assoc();
$kd_kegiatan = $row2['kd_kegiatan'] ?? '';

if (($jenis === 'proposal' || $jenis === 'lpj') && $status === 'Terima') {
    include('../phpqrcode/qrlib.php');

    // Generate URL verifikasi berdasarkan kd_kegiatan
    $url = $verifikasiBaseURL . urlencode($kd_kegiatan);

    $tempDir = __DIR__ . '/temp/';
    $generatedDir = __DIR__ . '/generated/';

    if (!is_dir($tempDir)) mkdir($tempDir, 0777, true);
    if (!is_dir($generatedDir)) mkdir($generatedDir, 0777, true);

    $qrFile = $tempDir . 'qr_' . $kd . '.png';

    QRcode::png($url, $qrFile, QR_ECLEVEL_L, 5);

    // Ambil file template dari database
    $sqlFile = "SELECT file_path FROM $table WHERE $id_column = ?";
    $stmtFile = $conn->prepare($sqlFile);
    $stmtFile->bind_param('s', $kd);
    $stmtFile->execute();
    $resultFile = $stmtFile->get_result();
    $rowFile = $resultFile->fetch_assoc();
    $templateFile = $rowFile['file_path'];

    if (!$templateFile || !file_exists($templateFile)) {
        die('File template tidak ditemukan di server.');
    }

    $templateProcessor = new TemplateProcessor($templateFile);

    $templateProcessor->setImageValue($placeholderQR, [
        'path' => $qrFile,
        'width' => 120,
        'height' => 120,
        'ratio' => false,
    ]);

    $outputFile = $generatedDir . $jenis . '_' . $kd . '_acc.docx';
    $templateProcessor->saveAs($outputFile);

    // Update file_path ke file baru
    $sqlUpdateFile = "UPDATE $table SET file_path = ? WHERE $id_column = ?";
    $stmtUpdateFile = $conn->prepare($sqlUpdateFile);
    if (!$stmtUpdateFile) die("Error prepare statement: " . $conn->error);
    $stmtUpdateFile->bind_param('ss', $outputFile, $kd);
    $stmtUpdateFile->execute();
    $stmtUpdateFile->close();

    // (Opsional) hapus file QR sementara
    // unlink($qrFile);
}

$stmt->close();
$stmt2->close();

header("Location: detail_kegiatan.php?kd=" . urlencode($kd_kegiatan));
exit;

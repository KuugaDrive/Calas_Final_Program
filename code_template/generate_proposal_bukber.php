<?php
require '../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

function getPostValue($key) {
    return (isset($_POST[$key]) && trim($_POST[$key]) !== '') ? htmlspecialchars(trim($_POST[$key])) : '-';
}

function uploadFile($fieldName, $uploadDir) {
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $filename = uniqid() . '_' . basename($_FILES[$fieldName]['name']);
        $target = $uploadDir . $filename;
        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $target)) {
            return $target;
        }
    }
    return null;
}

$uploadDir = 'uploads/';

// Upload tanda tangan
$ttdKetuaPath = uploadFile('TTD_Ketua_Pelaksana', $uploadDir);
if (!$ttdKetuaPath) {
    die('Error: File TTD Ketua Pelaksana wajib diupload.');
}

$ttdPenanggungPath = uploadFile('TTD_Penanggung_Jawab', $uploadDir);
if (!$ttdPenanggungPath) {
    die('Error: File TTD Penanggung Jawab wajib diupload.');
}

// Path template file (sesuaikan dengan file template kamu)
$templatePath = "../template/template_proposal_bukber.docx";

// Cek file template ada atau tidak
if (!file_exists($templatePath)) {
    die('Error: Template file tidak ditemukan di ' . $templatePath);
}

// Load template Word
$templateProcessor = new TemplateProcessor($templatePath);

// Set nilai ke template
$templateProcessor->setValue('Nama_Kegiatan', getPostValue('Nama_Kegiatan'));
$templateProcessor->setValue('Judul_Kegiatan', getPostValue('Judul_Kegiatan'));
$templateProcessor->setValue('Periode_Aslab', getPostValue('Periode_Aslab'));
$templateProcessor->setValue('Tahun_Kegiatan', getPostValue('Tahun_Kegiatan'));
$templateProcessor->setValue('Hari_Kegiatan', getPostValue('Hari_Kegiatan'));
$templateProcessor->setValue('Tanggal_Kegiatan', getPostValue('Tanggal_Kegiatan'));
$templateProcessor->setValue('Waktu_Kegiatan', getPostValue('Waktu_Kegiatan'));
$templateProcessor->setValue('Tempat_Kegiatan', getPostValue('Tempat_Kegiatan'));
$templateProcessor->setValue('Total_Biaya_Kegiatan', getPostValue('Total_Biaya_Kegiatan'));

$templateProcessor->setValue('Nama_Ketua_Pelaksana', getPostValue('Nama_Ketua_Pelaksana'));
$templateProcessor->setValue('NIMKetuaPelaksana', getPostValue('NIMKetuaPelaksana'));
$templateProcessor->setValue('Nama_Penanggung_Jawab', getPostValue('Nama_Penanggung_Jawab'));
$templateProcessor->setValue('NIMPenanggungJawab', getPostValue('NIMPenanggungJawab'));
$templateProcessor->setValue('Tempat_tanggal_pengesahan', getPostValue('Tempat_tanggal_pengesahan'));

$templateProcessor->setValue('Waktu_Kegiatan_Bermulai', getPostValue('Waktu_Kegiatan_Bermulai'));
$templateProcessor->setValue('Waktu_Acara_Berakhir', getPostValue('Waktu_Acara_Berakhir'));

$templateProcessor->setValue('Nama_Koordinator_Sekretaris', getPostValue('Nama_Koordinator_Sekretaris'));
$templateProcessor->setValue('Nama_Koordinator_Acara', getPostValue('Nama_Koordinator_Acara'));
$templateProcessor->setValue('Nama_Koordinator_Konsumsi', getPostValue('Nama_Koordinator_Konsumsi'));
$templateProcessor->setValue('Nama_Koordinator_PDD', getPostValue('Nama_Koordinator_PDD'));

for ($i = 1; $i <= 7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_Sekretaris_{$i}", getPostValue("Nama_Anggota_Sekretaris_{$i}"));
    $templateProcessor->setValue("Nama_Anggota_Acara_{$i}", getPostValue("Nama_Anggota_Acara_{$i}"));
    $templateProcessor->setValue("Nama_Anggota_Konsumsi_{$i}", getPostValue("Nama_Anggota_Konsumsi_{$i}"));
    $templateProcessor->setValue("Nama_Anggota_PDD_{$i}", getPostValue("Nama_Anggota_PDD_{$i}"));
}

// Set gambar tanda tangan
$templateProcessor->setImageValue('TTD_Ketua_Pelaksana', [
    'path' => $ttdKetuaPath,
    'width' => 100,
    'height' => 50,
    'ratio' => false
]);

$templateProcessor->setImageValue('TTD_Penanggung_Jawab', [
    'path' => $ttdPenanggungPath,
    'width' => 100,
    'height' => 50,
    'ratio' => false
]);

// Simpan file hasil generate
$safeNamaKegiatan = preg_replace('/[^a-zA-Z0-9]/', '_', getPostValue('Nama_Kegiatan'));
$outputFilename = $uploadDir . 'Proposal_' . $safeNamaKegiatan . '_' . date('Ymd_His') . '.docx';

$templateProcessor->saveAs($outputFilename);

// Kirim file ke browser untuk di-download
if (file_exists($outputFilename)) {
    // Bersihkan buffer output sebelumnya jika ada
    if (ob_get_length()) ob_end_clean();

    header('Content-Description: File Transfer');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Disposition: attachment; filename="' . basename($outputFilename) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($outputFilename));

    flush();
    readfile($outputFilename);

    // Hapus file sementara dan file tanda tangan
    unlink($outputFilename);
    unlink($ttdKetuaPath);
    unlink($ttdPenanggungPath);

    exit;
} else {
    die('Gagal membuat file proposal.');
}

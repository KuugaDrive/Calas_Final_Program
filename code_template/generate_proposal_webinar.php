<?php
require '../vendor/autoload.php';

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Upload TTD Ketua Pelaksana
if (isset($_FILES['TTD_Ketua_Pelaksana']) && $_FILES['TTD_Ketua_Pelaksana']['error'] === UPLOAD_ERR_OK) {
    $ttdKetuaPath = $uploadDir . basename($_FILES['TTD_Ketua_Pelaksana']['name']);
    move_uploaded_file($_FILES['TTD_Ketua_Pelaksana']['tmp_name'], $ttdKetuaPath);
} else {
    die('Error: File TTD Ketua Pelaksana tidak terupload dengan benar.');
}

// Upload TTD Penanggung Jawab
if (isset($_FILES['TTD_Penanggung_Jawab']) && $_FILES['TTD_Penanggung_Jawab']['error'] === UPLOAD_ERR_OK) {
    $ttdPenanggungPath = $uploadDir . basename($_FILES['TTD_Penanggung_Jawab']['name']);
    move_uploaded_file($_FILES['TTD_Penanggung_Jawab']['tmp_name'], $ttdPenanggungPath);
} else {
    die('Error: File TTD Penanggung Jawab tidak terupload dengan benar.');
}

use PhpOffice\PhpWord\TemplateProcessor;
$templateProcessor = new TemplateProcessor("../template/template_proposal_webinar.docx");

// Helper function untuk ambil nilai POST, jika kosong kembalikan '-'
function getPostValue($key) {
    return (isset($_POST[$key]) && trim($_POST[$key]) !== '') ? htmlspecialchars($_POST[$key]) : '-';
}

// Isi placeholder biasa
$templateProcessor->setValue('Nama_Kegiatan', getPostValue('Nama_Kegiatan'));
$templateProcessor->setValue('Judul_Kegiatan', getPostValue('Judul_Kegiatan'));
$templateProcessor->setValue('Periode_Aslab', getPostValue('Periode_Aslab'));
$templateProcessor->setValue('Tahun', getPostValue('Tahun'));
$templateProcessor->setValue('Waktu_Pelaksanaan', getPostValue('Waktu_Pelaksanaan'));
$templateProcessor->setValue('Tempat_Kegiatan', getPostValue('Tempat_Kegiatan'));
$templateProcessor->setValue('Total_Biaya', getPostValue('Total_Biaya'));
$templateProcessor->setValue('Tanggal_Penulisan_Proposal', getPostValue('Tanggal_Penulisan_Proposal'));
$templateProcessor->setValue('Nama_Ketua_Pelaksana', getPostValue('Nama_Ketua_Pelaksana_Form'));
$templateProcessor->setValue('NIM_Ketua_Pelaksana', getPostValue('NIM_Ketua_Pelaksana_Form'));
$templateProcessor->setValue('Nama_Penanggung_Jawab', getPostValue('Nama_PJ_Form'));
$templateProcessor->setValue('NIM_Penanggung_Jawab', getPostValue('NIM_PJ_Form'));
$templateProcessor->setValue('Tempat_tanggal_surat_ditulis', getPostValue('Tempat_tanggal_surat_ditulis'));
$templateProcessor->setValue('Penulis', getPostValue('Penulis'));
$templateProcessor->setValue('Tujuan_Kegiatan', getPostValue('Tujuan_Kegiatan'));
$templateProcessor->setValue('Latar_Belakang', getPostValue('Latar_Belakang'));
$templateProcessor->setValue('Target_Peserta', getPostValue('Target_Peserta'));
$templateProcessor->setValue('Tanggal_Kegiatan', getPostValue('Tanggal_Kegiatan'));
$templateProcessor->setValue('Waktu_Kegiatan', getPostValue('Waktu_Kegiatan'));
$templateProcessor->setValue('Jumlah_Anggota', getPostValue('Jumlah_Anggota'));
$templateProcessor->setValue('Durasi_Acara', getPostValue('Durasi_Acara'));
$templateProcessor->setValue('Waktu_Acara_Bermulai', getPostValue('Waktu_Acara_Bermulai'));
$templateProcessor->setValue('Waktu_Acara_Berakhir', getPostValue('Waktu_Acara_Berakhir'));

// Set image tanda tangan
$templateProcessor->setImageValue('TTD_Ketua_Pelaksana', [
    'path' => $ttdKetuaPath,
    'width' => 100,
    'height' => 50
]);
$templateProcessor->setImageValue('TTD_Penanggung_Jawab', [
    'path' => $ttdPenanggungPath,
    'width' => 100,
    'height' => 50
]);

// Daftar semua placeholder anggota panitia untuk dicek satu-satu
$anggotaFields = [
    'Nama_Penanggung_Jawab',
    'Nama_Ketua_Pelaksana',
    'Nama_Koordinator_Sekretaris',
    'Nama_Anggota_Sekretaris_1',
    'Nama_Anggota_Sekretaris_2',
    'Nama_Anggota_Sekretaris_3',
    'Nama_Anggota_Sekretaris_4',
    'Nama_Anggota_Sekretaris_5',
    'Nama_Anggota_Sekretaris_6',
    'Nama_Anggota_Sekretaris_7',
    'Nama_Koordinator_Acara',
    'Nama_Anggota_Acara_1',
    'Nama_Anggota_Acara_2',
    'Nama_Anggota_Acara_3',
    'Nama_Anggota_Acara_4',
    'Nama_Anggota_Acara_5',
    'Nama_Anggota_Acara_6',
    'Nama_Anggota_Acara_7',
    'Nama_Koordinator_Humas',
    'Nama_Anggota_Humas_1',
    'Nama_Anggota_Humas_2',
    'Nama_Anggota_Humas_3',
    'Nama_Anggota_Humas_4',
    'Nama_Anggota_Humas_5',
    'Nama_Anggota_Humas_6',
    'Nama_Anggota_Humas_7',
    'Nama_Koordinator_PDD',
    'Nama_Anggota_PDD_1',
    'Nama_Anggota_PDD_2',
    'Nama_Anggota_PDD_3',
    'Nama_Anggota_PDD_4',
    'Nama_Anggota_PDD_5',
    'Nama_Anggota_PDD_6',
    'Nama_Anggota_PDD_7',
];

foreach ($anggotaFields as $field) {
    $templateProcessor->setValue($field, getPostValue($field));
}

// Simpan file hasil generate
$outputFile = 'proposal_generated.docx';
$templateProcessor->saveAs($outputFile);

// Download file
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
header('Content-Length: ' . filesize($outputFile));
readfile($outputFile);
unlink($outputFile);
exit;

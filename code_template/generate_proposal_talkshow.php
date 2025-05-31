<?php
require '../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Fungsi bantu ambil POST, default '-'
function getPostValue($key) {
    return (isset($_POST[$key]) && trim($_POST[$key]) !== '') ? htmlspecialchars(trim($_POST[$key])) : '-';
}

// Upload TTD Ketua Pelaksana (wajib)
if (isset($_FILES['TTD_Ketua_Pelaksana']) && $_FILES['TTD_Ketua_Pelaksana']['error'] === UPLOAD_ERR_OK) {
    $ttdKetuaPath = $uploadDir . basename($_FILES['TTD_Ketua_Pelaksana']['name']);
    move_uploaded_file($_FILES['TTD_Ketua_Pelaksana']['tmp_name'], $ttdKetuaPath);
} else {
    die('Error: File TTD Ketua Pelaksana tidak terupload dengan benar.');
}

// Upload TTD Penanggung Jawab (wajib)
if (isset($_FILES['TTD_Penanggung_Jawab']) && $_FILES['TTD_Penanggung_Jawab']['error'] === UPLOAD_ERR_OK) {
    $ttdPenanggungPath = $uploadDir . basename($_FILES['TTD_Penanggung_Jawab']['name']);
    move_uploaded_file($_FILES['TTD_Penanggung_Jawab']['tmp_name'], $ttdPenanggungPath);
} else {
    die('Error: File TTD Penanggung Jawab tidak terupload dengan benar.');
}

$templateProcessor = new TemplateProcessor("../template/template_lpj_talkshow.docx");

// Isi teks
$templateProcessor->setValue('Nama_Kegiatan', getPostValue('Nama_Kegiatan'));
$templateProcessor->setValue('Judul_Kegiatan', getPostValue('Judul_Kegiatan'));
$templateProcessor->setValue('Periode_Aslab', getPostValue('Periode_Aslab'));
$templateProcessor->setValue('Tahun_Kegiatan', getPostValue('Tahun_Kegiatan'));
$templateProcessor->setValue('Waktu_Kegiatan', getPostValue('Waktu_Kegiatan'));
$templateProcessor->setValue('Tempat_Kegiatan', getPostValue('Tempat_Kegiatan'));
$templateProcessor->setValue('Total_Biaya', getPostValue('Total_Biaya'));
$templateProcessor->setValue('Tanggal_Penulisan_Proposal', getPostValue('Tanggal_Penulisan_Proposal'));

$templateProcessor->setValue('Nama_Ketua_Pelaksana', getPostValue('Nama_Ketua_Pelaksana'));
$templateProcessor->setValue('NIM_Ketua_Pelaksana', getPostValue('NIM_Ketua_Pelaksana'));
$templateProcessor->setValue('Nama_Penanggung_Jawab', getPostValue('Nama_Penanggung_Jawab'));
$templateProcessor->setValue('NIM_Penanggung_Jawab', getPostValue('NIM_Penanggung_Jawab'));

$templateProcessor->setValue('Nama_Koordinator_Sekretaris', getPostValue('Nama_Koordinator_Sekretaris'));
for ($i=1; $i<=7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_Sekretaris_{$i}", getPostValue("Nama_Anggota_Sekretaris_{$i}"));
}

$templateProcessor->setValue('Nama_Koordinator_Acara', getPostValue('Nama_Koordinator_Acara'));
for ($i=1; $i<=7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_Acara_{$i}", getPostValue("Nama_Anggota_Acara_{$i}"));
}

$templateProcessor->setValue('Nama_Koordinator_Humas', getPostValue('Nama_Koordinator_Humas'));
for ($i=1; $i<=7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_Humas_{$i}", getPostValue("Nama_Anggota_Humas_{$i}"));
}

$templateProcessor->setValue('Nama_Koordinator_PDD', getPostValue('Nama_Koordinator_PDD'));
for ($i=1; $i<=7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_PDD_{$i}", getPostValue("Nama_Anggota_PDD_{$i}"));
}

$templateProcessor->setValue('Nama_Pemateri', getPostValue('Nama_Pemateri'));
$templateProcessor->setValue('Nama_Moderator', getPostValue('Nama_Moderator'));
$templateProcessor->setValue('Nama_MC', getPostValue('Nama_MC'));
$templateProcessor->setValue('Poin_Talkshow', getPostValue('Poin_Talkshow'));
$templateProcessor->setValue('Tema_Talkshow', getPostValue('Tema_Talkshow'));
$templateProcessor->setValue('Poin_Poin_Talkshow', getPostValue('Poin_Poin_Talkshow'));
$templateProcessor->setValue('Penanya_1', getPostValue('Penanya_1'));
$templateProcessor->setValue('Pertanyaan_1', getPostValue('Pertanyaan_1'));
$templateProcessor->setValue('Penanya_2', getPostValue('Penanya_2'));
$templateProcessor->setValue('Perttanyaan_2', getPostValue('Perttanyaan_2'));
$templateProcessor->setValue('Kesimpulan_Talkshow', getPostValue('Kesimpulan_Talkshow'));

for ($q=1; $q<=10; $q++) {
    $templateProcessor->setValue("Quiz_Nomor_{$q}", getPostValue("Quiz_Nomor_{$q}"));
}

$templateProcessor->setValue('Nama_Juara_1', getPostValue('Nama_Juara_1'));
$templateProcessor->setValue('Nama_Juara_2', getPostValue('Nama_Juara_2'));
$templateProcessor->setValue('Nama_Juara_3', getPostValue('Nama_Juara_3'));

$templateProcessor->setValue('Waktu_Acara_Bermulai', getPostValue('Waktu_Acara_Bermulai'));
$templateProcessor->setValue('Waktu_Acara_Berakhir', getPostValue('Waktu_Acara_Berakhir'));

// Set image tanda tangan
$templateProcessor->setImageValue('TTD_Nama_Ketua_Pelaksana', [
    'path' => $ttdKetuaPath,
    'width' => 100,
    'height' => 50
]);
$templateProcessor->setImageValue('TTD_Nama_Penanggung_Jawab', [
    'path' => $ttdPenanggungPath,
    'width' => 100,
    'height' => 50
]);

// Sertifikat opsional
$sertifikatFields = ['Sertifikat_Pemateri', 'Sertifikat_Moderator', 'Sertifikat_Panitia', 'Sertifikat_Peserta'];
foreach ($sertifikatFields as $field) {
    if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
        $path = $uploadDir . basename($_FILES[$field]['name']);
        move_uploaded_file($_FILES[$field]['tmp_name'], $path);
        $templateProcessor->setImageValue($field, [
            'path' => $path,
            'width' => 100,
            'height' => 50,
        ]);
    } else {
        $templateProcessor->setValue($field, '-');
    }
}

// Simpan dan kirim file ke browser
$outputFile = 'lpj_talkshow_' . date('Ymd_His') . '.docx';
$templateProcessor->saveAs($outputFile);

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
header('Content-Length: ' . filesize($outputFile));
readfile($outputFile);
unlink($outputFile);
exit;

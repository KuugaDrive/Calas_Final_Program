<?php
require '../vendor/autoload.php';

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Fungsi bantu ambil POST, jika kosong kembalikan '-'
function getPostValue($key) {
    return (isset($_POST[$key]) && trim($_POST[$key]) !== '') ? htmlspecialchars($_POST[$key]) : '-';
}

// Fungsi upload dan set image di template
function uploadAndSetImage($fieldName, $uploadDir, $templateProcessor, $placeholderName) {
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $path = $uploadDir . basename($_FILES[$fieldName]['name']);
        move_uploaded_file($_FILES[$fieldName]['tmp_name'], $path);
        $templateProcessor->setImageValue($placeholderName, [
            'path' => $path,
            'width' => 600,
            'height' => 400,
        ]);
        return $path;
    }
    $templateProcessor->setValue($placeholderName, '-'); // kosongkan jika tidak upload
    return null;
}

// Upload TTD Ketua Pelaksana
if (isset($_FILES['TTD_Ketua_Pelaksana']) && $_FILES['TTD_Ketua_Pelaksana']['error'] === UPLOAD_ERR_OK) {
    $ttdKetuaPath = $uploadDir . basename($_FILES['TTD_Ketua_Pelaksana']['name']);
    move_uploaded_file($_FILES['TTD_Ketua_Pelaksana']['tmp_name'], $ttdKetuaPath);
} else {
    die('Error: File TTD Ketua Pelaksana tidak terupload dengan benar.');
}
// Upload TTD Sekretaris Pelaksana
if (isset($_FILES['TTD_Sekretaris_Pelaksana']) && $_FILES['TTD_Sekretaris_Pelaksana']['error'] === UPLOAD_ERR_OK) {
    $ttdSekretarisPath = $uploadDir . basename($_FILES['TTD_Sekretaris_Pelaksana']['name']);
    move_uploaded_file($_FILES['TTD_Sekretaris_Pelaksana']['tmp_name'], $ttdSekretarisPath);
} else {
    die('Error: File TTD Sekretaris Pelaksana tidak terupload dengan benar.');
}
// Upload TTD Penanggung Jawab
if (isset($_FILES['TTD_Penanggung_Jawab']) && $_FILES['TTD_Penanggung_Jawab']['error'] === UPLOAD_ERR_OK) {
    $ttdPenanggungPath = $uploadDir . basename($_FILES['TTD_Penanggung_Jawab']['name']);
    move_uploaded_file($_FILES['TTD_Penanggung_Jawab']['tmp_name'], $ttdPenanggungPath);
} else {
    die('Error: File TTD Penanggung Jawab tidak terupload dengan benar.');
}

use PhpOffice\PhpWord\TemplateProcessor;
$templateProcessor = new TemplateProcessor("../template/template_lpj_workshop.docx");

// Isi teks
$templateProcessor->setValue('Nama_Kegiatan', getPostValue('Nama_Kegiatan'));
$templateProcessor->setValue('Judul_Kegiatan', getPostValue('Judul_Kegiatan'));
$templateProcessor->setValue('Periode_Aslab', getPostValue('Periode_Aslab'));
$templateProcessor->setValue('Tahun_Kegiatan', getPostValue('Tahun_Kegiatan'));
$templateProcessor->setValue('Hari_Tanggal_Kegiatan', getPostValue('Hari_Tanggal_Kegiatan'));
$templateProcessor->setValue('Tempat_Kegiatan', getPostValue('Tempat_Kegiatan'));
$templateProcessor->setValue('Anggaran_Dana', getPostValue('Anggaran_Dana'));
$templateProcessor->setValue('Tanggal_Pengesahan', getPostValue('Tanggal_Pengesahan'));

$templateProcessor->setValue('Nama_Penanggung_Jawab', getPostValue('Nama_Penanggung_Jawab_Form'));
$templateProcessor->setValue('Nama_Ketua_Pelaksana', getPostValue('Nama_Ketua_Pelaksana_Form'));
$templateProcessor->setValue('Nama_Sekretaris_Pelaksana', getPostValue('Nama_Sekretaris_Pelaksana_Form'));
$templateProcessor->setValue('Nama_Bendahara', getPostValue('Nama_Bendahara_Form'));
$templateProcessor->setValue('NIM_Ketua_Pelaksana', getPostValue('NIM_Ketua_Pelaksana_Form'));
$templateProcessor->setValue('NIM_Sekretaris_Pelaksana', getPostValue('NIM_Sekretaris_Pelaksana_Form'));
$templateProcessor->setValue('NIM_Penanggung_Jawab', getPostValue('NIM_Penanggung_Jawab_Form'));

$templateProcessor->setValue('Waktu_Acara_Bermulai', getPostValue('Waktu_Acara_Bermulai'));
$templateProcessor->setValue('Waktu_Acara_Berakhir', getPostValue('Waktu_Acara_Berakhir'));
$templateProcessor->setValue('Tempat_Acara', getPostValue('Tempat_Acara'));

$templateProcessor->setValue('Hasil_Yang_Dicapai', getPostValue('Hasil_Yang_Dicapai'));

// Materi dan Instruktur
for ($i = 1; $i <= 3; $i++) {
    $templateProcessor->setValue("Materi_{$i}", getPostValue("Materi_{$i}"));
    $templateProcessor->setValue("Nama_Instruktur_{$i}", getPostValue("Nama_Instruktur_{$i}"));
    $templateProcessor->setValue("Nama_Asisten_Instruktur_{$i}", getPostValue("Nama_Asisten_Instruktur_{$i}"));
}

// Panitia per sie
$anggotaFields = [
    'Nama_Anggota_Acara_1','Nama_Anggota_Acara_2','Nama_Anggota_Acara_3','Nama_Anggota_Acara_4','Nama_Anggota_Acara_5','Nama_Anggota_Acara_6','Nama_Anggota_Acara_7',
    'Nama_Anggota_Humas_1','Nama_Anggota_Humas_2','Nama_Anggota_Humas_3','Nama_Anggota_Humas_4','Nama_Anggota_Humas_5','Nama_Anggota_Humas_6','Nama_Anggota_Humas_7',
    'Nama_Anggota_PDD_1','Nama_Anggota_PDD_2','Nama_Anggota_PDD_3','Nama_Anggota_PDD_4','Nama_Anggota_PDD_5','Nama_Anggota_PDD_6','Nama_Anggota_PDD_7',
    'Nama_Anggota_Konsumsi_1','Nama_Anggota_Konsumsi_2','Nama_Anggota_Konsumsi_3','Nama_Anggota_Konsumsi_4','Nama_Anggota_Konsumsi_5','Nama_Anggota_Konsumsi_6','Nama_Anggota_Konsumsi_7',
    'Nama_Anggota_Registrasi_1','Nama_Anggota_Registrasi_2','Nama_Anggota_Registrasi_3','Nama_Anggota_Registrasi_4','Nama_Anggota_Registrasi_5','Nama_Anggota_Registrasi_6','Nama_Anggota_Registrasi_7'
];
foreach ($anggotaFields as $field) {
    $templateProcessor->setValue($field, getPostValue($field));
}

// Set tanda tangan
$templateProcessor->setImageValue('TTD_Ketua_Pelaksana', [
    'path' => $ttdKetuaPath,
    'width' => 100,
    'height' => 50
]);
$templateProcessor->setImageValue('TTD_Sekretaris_Pelaksana', [
    'path' => $ttdSekretarisPath,
    'width' => 100,
    'height' => 50
]);
$templateProcessor->setImageValue('TTD_Penanggung_Jawab', [
    'path' => $ttdPenanggungPath,
    'width' => 100,
    'height' => 50
]);

// Upload dan set gambar lampiran
$lampiranImages = [
    'Daftar_Hadir_Materi_1', 'Daftar_Hadir_Materi_2', 'Daftar_Hadir_Materi_3',
    'Lampiran_Sertifikat_Materi_1', 'Lampiran_Sertifikat_Materi_2', 'Lampiran_Sertifikat_Materi_3'
];

foreach ($lampiranImages as $imgField) {
    if (isset($_FILES[$imgField]) && $_FILES[$imgField]['error'] === UPLOAD_ERR_OK) {
        $path = $uploadDir . basename($_FILES[$imgField]['name']);
        move_uploaded_file($_FILES[$imgField]['tmp_name'], $path);
        $templateProcessor->setImageValue($imgField, [
            'path' => $path,
            'width' => 600,
            'height' => 400,
        ]);
    } else {
        $templateProcessor->setValue($imgField, '-');
    }
}

// Simpan dan kirim file ke browser
$outputFile = 'lpj_workshop_' . date('Ymd_His') . '.docx';
$templateProcessor->saveAs($outputFile);

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
header('Content-Length: ' . filesize($outputFile));
readfile($outputFile);
unlink($outputFile);
exit;

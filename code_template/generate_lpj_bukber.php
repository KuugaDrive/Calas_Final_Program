<?php
require '../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Fungsi ambil POST dengan sanitize, default '-'
function getPostValue($key) {
    return (isset($_POST[$key]) && trim($_POST[$key]) !== '') ? htmlspecialchars(trim($_POST[$key])) : '-';
}

// Fungsi upload file, kembalikan path atau null
function uploadFile($fieldName, $uploadDir) {
    if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
        $filename = basename($_FILES[$fieldName]['name']);
        $target = $uploadDir . $filename;
        if (move_uploaded_file($_FILES[$fieldName]['tmp_name'], $target)) {
            return $target;
        }
    }
    return null;
}

// Upload file tanda tangan
$ttdKetuaPath = uploadFile('TTD_Ketua_Pelaksana', $uploadDir);
$ttdPenanggungPath = uploadFile('TTD_Penanggung_Jawab', $uploadDir);
$ttdSekretarisPath = uploadFile('TTD_Sekretaris_Pelaksana', $uploadDir);

$templateProcessor = new TemplateProcessor("../template/template_lpj_bukber.docx");

// Isi data teks
$templateProcessor->setValue('Nama_Kegiatan', getPostValue('Nama_Kegiatan'));
$templateProcessor->setValue('Judul_Kegiatan', getPostValue('Judul_Kegiatan'));
$templateProcessor->setValue('Periode_Aslab', getPostValue('Periode_Aslab'));
$templateProcessor->setValue('Tahun_Kegiatan', getPostValue('Tahun_Kegiatan'));
$templateProcessor->setValue('Waktu_Pelaksanaan', getPostValue('Waktu_Pelaksanaan'));
$templateProcessor->setValue('Tempat_Kegiatan', getPostValue('Tempat_Kegiatan'));
$templateProcessor->setValue('Total_Biaya', getPostValue('Total_Biaya'));
$templateProcessor->setValue('Tanggal_Penulisan_Proposal', getPostValue('Tanggal_Penulisan_Proposal'));

$templateProcessor->setValue('Nama_Penanggung_Jawab', getPostValue('Nama_Penanggung_Jawab'));
$templateProcessor->setValue('NIM_Penanggung_Jawab', getPostValue('NIM_Penanggung_Jawab'));
$templateProcessor->setValue('Nama_Ketua_Pelaksana', getPostValue('Nama_Ketua_Pelaksana'));
$templateProcessor->setValue('NIM_Ketua_Pelaksana', getPostValue('NIM_Ketua_Pelaksana'));

$templateProcessor->setValue('Nama_Koordinator_Sekretaris', getPostValue('Nama_Koordinator_Sekretaris'));
for ($i = 1; $i <= 7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_Sekretaris_{$i}", getPostValue("Nama_Anggota_Sekretaris_{$i}"));
}

$templateProcessor->setValue('Nama_Koordinator_Acara', getPostValue('Nama_Koordinator_Acara'));
for ($i = 1; $i <= 7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_Acara_{$i}", getPostValue("Nama_Anggota_Acara_{$i}"));
}

$templateProcessor->setValue('Nama_Konsumsi_Humas', getPostValue('Nama_Konsumsi_Humas'));
for ($i = 1; $i <= 7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_Konsumsi_{$i}", getPostValue("Nama_Anggota_Konsumsi_{$i}"));
}

$templateProcessor->setValue('Nama_Koordinator_PDD', getPostValue('Nama_Koordinator_PDD'));
for ($i = 1; $i <= 7; $i++) {
    $templateProcessor->setValue("Nama_Anggota_PDD_{$i}", getPostValue("Nama_Anggota_PDD_{$i}"));
}

$templateProcessor->setValue('Hasil_Yang_Dicapai', getPostValue('Hasil_Yang_Dicapai'));

// Upload file lampiran opsional
$lampiranFields = [
    'Lampiran_Konfirmasi_Kehadiran',
    'Dokumentasi_Panti_Asuhan',
    'Dokumentasi_Transfer_Santunan'
];

foreach ($lampiranFields as $field) {
    $path = uploadFile($field, $uploadDir);
    if ($path) {
        // Jika ingin tampilkan gambar di dokumen (jika template support image)
        // Atau tampilkan path sebagai teks
        $templateProcessor->setValue($field, basename($path));
    } else {
        $templateProcessor->setValue($field, '-');
    }
}

// Set tanda tangan gambar (jika ada)
if ($ttdKetuaPath) {
    $templateProcessor->setImageValue('TTD_Nama_Ketua_Pelaksana', [
        'path' => $ttdKetuaPath,
        'width' => 100,
        'height' => 50,
    ]);
} else {
    $templateProcessor->setValue('TTD_Nama_Ketua_Pelaksana', '-');
}

if ($ttdPenanggungPath) {
    $templateProcessor->setImageValue('TTD_Nama_Penanggung_Jawab', [
        'path' => $ttdPenanggungPath,
        'width' => 100,
        'height' => 50,
    ]);
} else {
    $templateProcessor->setValue('TTD_Nama_Penanggung_Jawab', '-');
}

if ($ttdSekretarisPath) {
    $templateProcessor->setImageValue('TTD_Sekretaris_Pelaksana', [
        'path' => $ttdSekretarisPath,
        'width' => 100,
        'height' => 50,
    ]);
} else {
    $templateProcessor->setValue('TTD_Sekretaris_Pelaksana', '-');
}

// Generate file output
$outputFile = 'lpj_bukber_' . date('Ymd_His') . '.docx';
$templateProcessor->saveAs($outputFile);

// Download ke browser
header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
header('Content-Length: ' . filesize($outputFile));
readfile($outputFile);
unlink($outputFile);
exit;

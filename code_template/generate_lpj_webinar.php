<?php
require '../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Fungsi ambil POST, default '-'
function getPostValue($key) {
    return (isset($_POST[$key]) && trim($_POST[$key]) !== '') ? htmlspecialchars(trim($_POST[$key])) : '-';
}

// Fungsi upload file, kembalikan path jika sukses, else null
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

// Upload file tanda tangan (harus ada)
$ttdKetuaPath = uploadFile('TTD_Nama_Ketua_Pelaksana', $uploadDir);
if (!$ttdKetuaPath) die('Error: File TTD Ketua Pelaksana tidak terupload dengan benar.');
$ttdPenanggungPath = uploadFile('TTD_Nama_Penanggung_Jawab', $uploadDir);
if (!$ttdPenanggungPath) die('Error: File TTD Penanggung Jawab tidak terupload dengan benar.');
$ttdSekretarisPath = uploadFile('TTD_Sekretaris_Pelaksana', $uploadDir);
if (!$ttdSekretarisPath) die('Error: File TTD Sekretaris Pelaksana tidak terupload dengan benar.');

$templateProcessor = new TemplateProcessor("../template/template_lpj_webinar.docx");

// Set teks utama
$fieldsText = [
    'Nama_Kegiatan', 'Judul_Kegiatan', 'Periode_Aslab', 'Tahun_Kegiatan', 'Waktu_Pelaksanaan',
    'Tempat_Kegiatan', 'Total_Biaya', 'Tanggal_Penulisan_Proposal',
    'Nama_Penanggung_Jawab', 'NIM_Penanggung_Jawab', 'Nama_Ketua_Pelaksana', 'NIM_Ketua_Pelaksana',
    'Nama_Koordinator_Sekretaris', 'Nama_Koordinator_Acara', 'Nama_Koordinator_Humas', 'Nama_Koordinator_PDD',
    'Nama_Pemateri', 'Nama_Moderator', 'Nama_MC', 'Poin_Webinar', 'Tema_Webinar', 'Poin_Poin_Webinar',
    'Penanya_1', 'Pertanyaan_1', 'Penanya_2', 'Perttanyaan_2', 'Kesimpulan_Webinar',
    'Waktu_Acara_Bermulai', 'Waktu_Acara_Berakhir',
    'Nama_Juara_1', 'Nama_Juara_2', 'Nama_Juara_3'
];

foreach ($fieldsText as $field) {
    $templateProcessor->setValue($field, getPostValue($field));
}

// Set anggota per divisi
$anggotaFields = [
    'Nama_Anggota_Sekretaris_1','Nama_Anggota_Sekretaris_2','Nama_Anggota_Sekretaris_3','Nama_Anggota_Sekretaris_4','Nama_Anggota_Sekretaris_5','Nama_Anggota_Sekretaris_6','Nama_Anggota_Sekretaris_7',
    'Nama_Anggota_Acara_1','Nama_Anggota_Acara_2','Nama_Anggota_Acara_3','Nama_Anggota_Acara_4','Nama_Anggota_Acara_5','Nama_Anggota_Acara_6','Nama_Anggota_Acara_7',
    'Nama_Anggota_Humas_1','Nama_Anggota_Humas_2','Nama_Anggota_Humas_3','Nama_Anggota_Humas_4','Nama_Anggota_Humas_5','Nama_Anggota_Humas_6','Nama_Anggota_Humas_7',
    'Nama_Anggota_PDD_1','Nama_Anggota_PDD_2','Nama_Anggota_PDD_3','Nama_Anggota_PDD_4','Nama_Anggota_PDD_5','Nama_Anggota_PDD_6','Nama_Anggota_PDD_7'
];
foreach ($anggotaFields as $field) {
    $templateProcessor->setValue($field, getPostValue($field));
}

// Set quiz nomor 1 sampai 10
for ($q = 1; $q <= 10; $q++) {
    $templateProcessor->setValue("Quiz_Nomor_{$q}", getPostValue("Quiz_Nomor_{$q}"));
}

// Upload file sertifikat opsional
$sertifikatFields = ['Sertifikat_Pemateri', 'Sertifikat_Moderator', 'Sertifikat_Panitia', 'Sertifikat_Peserta'];
foreach ($sertifikatFields as $field) {
    $path = uploadFile($field, $uploadDir);
    if ($path) {
        $templateProcessor->setImageValue($field, [
            'path' => $path,
            'width' => 100,
            'height' => 50,
        ]);
    } else {
        $templateProcessor->setValue($field, '-');
    }
}

// Set tanda tangan (harus ada)
$templateProcessor->setImageValue('TTD_Nama_Ketua_Pelaksana', [
    'path' => $ttdKetuaPath,
    'width' => 100,
    'height' => 50,
]);
$templateProcessor->setImageValue('TTD_Nama_Penanggung_Jawab', [
    'path' => $ttdPenanggungPath,
    'width' => 100,
    'height' => 50,
]);
$templateProcessor->setImageValue('TTD_Sekretaris_Pelaksana', [
    'path' => $ttdSekretarisPath,
    'width' => 100,
    'height' => 50,
]);

// Simpan dan kirim file ke browser
$outputFile = 'lpj_webinar_' . date('Ymd_His') . '.docx';
$templateProcessor->saveAs($outputFile);

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
header('Content-Length: ' . filesize($outputFile));
readfile($outputFile);
unlink($outputFile);
exit;

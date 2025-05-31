<?php
require '../vendor/autoload.php';

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

if (isset($_FILES['TTD_Ketua_Pelaksana']) && $_FILES['TTD_Ketua_Pelaksana']['error'] === UPLOAD_ERR_OK) {
    $ttdKetuaPath = $uploadDir . basename($_FILES['TTD_Ketua_Pelaksana']['name']);
    move_uploaded_file($_FILES['TTD_Ketua_Pelaksana']['tmp_name'], $ttdKetuaPath);
} else {
    die('Error: File TTD Ketua Pelaksana tidak terupload dengan benar.');
}

if (isset($_FILES['TTD_Penanggung_Jawab']) && $_FILES['TTD_Penanggung_Jawab']['error'] === UPLOAD_ERR_OK) {
    $ttdPenanggungPath = $uploadDir . basename($_FILES['TTD_Penanggung_Jawab']['name']);
    move_uploaded_file($_FILES['TTD_Penanggung_Jawab']['tmp_name'], $ttdPenanggungPath);
} else {
    die('Error: File TTD Penanggung Jawab tidak terupload dengan benar.');
}


use PhpOffice\PhpWord\TemplateProcessor;

$templateProcessor = new TemplateProcessor("../template/template_proposal_webinar.docx");

$templateProcessor->setValue('Nama_Kegiatan', $_POST['Nama_Kegiatan']);
$templateProcessor->setValue('Judul_Kegiatan', $_POST['Judul_Kegiatan']);
$templateProcessor->setValue('Periode_Aslab', $_POST['Periode_Aslab']);
$templateProcessor->setValue('Tahun', $_POST['Tahun']);
$templateProcessor->setValue('Waktu_Pelaksanaan', $_POST['Waktu_Pelaksanaan']);
$templateProcessor->setValue('Tempat_Kegiatan', $_POST['Tempat_Kegiatan']);
$templateProcessor->setValue('Total_Biaya', $_POST['Total_Biaya']);
$templateProcessor->setValue('Tanggal_Penulisan_Proposal', $_POST['Tanggal_Penulisan_Proposal']);
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
$templateProcessor->setValue('Nama_Ketua_Pelaksana', $_POST['Nama_Ketua_Pelaksana']);
$templateProcessor->setValue('NIM_Ketua_Pelaksana', $_POST['NIM_Ketua_Pelaksana']);
$templateProcessor->setValue('Nama_Penanggung_Jawab', $_POST['Nama_Penanggung_Jawab']);
$templateProcessor->setValue('NIM_Penanggung_Jawab', $_POST['NIM_Penanggung_Jawab']);
$templateProcessor->setValue('Tempat_tanggal_surat_ditulis', $_POST['Tempat_tanggal_surat_ditulis']);
$templateProcessor->setValue('Penulis', $_POST['Penulis']);
$templateProcessor->setValue('Tujuan_Kegiatan', $_POST['Tujuan_Kegiatan']);
$templateProcessor->setValue('Latar_Belakang', $_POST['Latar_Belakang']);
$templateProcessor->setValue('Target_Peserta', $_POST['Target_Peserta']);
$templateProcessor->setValue('Tanggal_Kegiatan', $_POST['Tanggal_Kegiatan']);
$templateProcessor->setValue('Waktu_Kegiatan', $_POST['Waktu_Kegiatan']);
$templateProcessor->setValue('Jumlah_Anggota', $_POST['Jumlah_Anggota']);
$templateProcessor->setValue('Durasi_Acara', $_POST['Durasi_Acara']);
$templateProcessor->setValue('Waktu_Acara_Bermulai', $_POST['Waktu_Acara_Bermulai']);
$templateProcessor->setValue('Waktu_Acara_Berakhir', $_POST['Waktu_Acara_Berakhir']);

$templateProcessor->setValue('Nama_Penanggung_Jawab', $_POST['Nama_Penanggung_Jawab']);
$templateProcessor->setValue('Nama_Ketua_Pelaksana', $_POST['Nama_Ketua_Pelaksana']);
$templateProcessor->setValue('Nama_Koordinator_Sekretaris', $_POST['Nama_Koordinator_Sekretaris']);
$templateProcessor->setValue('Nama_Anggota_Sekretaris_1', $_POST['Nama_Anggota_Sekretaris_1']);
$templateProcessor->setValue('Nama_Anggota_Sekretaris_2', $_POST['Nama_Anggota_Sekretaris_2']);
$templateProcessor->setValue('Nama_Anggota_Sekretaris_3', $_POST['Nama_Anggota_Sekretaris_3']);
$templateProcessor->setValue('Nama_Koordinator_Acara', $_POST['Nama_Koordinator_Acara']);
$templateProcessor->setValue('Nama_Anggota_Acara_1', $_POST['Nama_Anggota_Acara_1']);
$templateProcessor->setValue('Nama_Anggota_Acara_2', $_POST['Nama_Anggota_Acara_2']);
$templateProcessor->setValue('Nama_Anggota_Acara_3', $_POST['Nama_Anggota_Acara_3']);
$templateProcessor->setValue('Nama_Anggota_Acara_4', $_POST['Nama_Anggota_Acara_4']);
$templateProcessor->setValue('Nama_Anggota_Acara_5', $_POST['Nama_Anggota_Acara_5']);
$templateProcessor->setValue('Nama_Anggota_Acara_6', $_POST['Nama_Anggota_Acara_6']);
$templateProcessor->setValue('Nama_Koordinator_Humas', $_POST['Nama_Koordinator_Humas']);
$templateProcessor->setValue('Nama_Anggota_Humas_1', $_POST['Nama_Anggota_Humas_1']);
$templateProcessor->setValue('Nama_Anggota_Humas_2', $_POST['Nama_Anggota_Humas_2']);
$templateProcessor->setValue('Nama_Anggota_Humas_3', $_POST['Nama_Anggota_Humas_3']);
$templateProcessor->setValue('Nama_Koordinator_PDD', $_POST['Nama_Koordinator_PDD']);
$templateProcessor->setValue('Nama_Anggota_PDD_1', $_POST['Nama_Anggota_PDD_1']);
$templateProcessor->setValue('Nama_Anggota_PDD_2', $_POST['Nama_Anggota_PDD_2']);
$templateProcessor->setValue('Nama_Anggota_PDD_3', $_POST['Nama_Anggota_PDD_3']);
$templateProcessor->setValue('Nama_Anggota_PDD_4', $_POST['Nama_Anggota_PDD_4']);
$templateProcessor->setValue('Nama_Anggota_PDD_5', $_POST['Nama_Anggota_PDD_5']);
$templateProcessor->setValue('Nama_Anggota_PDD_6', $_POST['Nama_Anggota_PDD_6']);
$templateProcessor->setValue('Nama_Anggota_PDD_7', $_POST['Nama_Anggota_PDD_7']);

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

header("location: dashboard_user.php");
?>
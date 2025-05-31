<?php
require '../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

// Upload tanda tangan Kepala Lab
$ttdKepalaLabPath = null;
if (isset($_FILES['TTD_Kepala_Lab']) && $_FILES['TTD_Kepala_Lab']['error'] === UPLOAD_ERR_OK) {
    $ttdKepalaLabPath = $uploadDir . basename($_FILES['TTD_Kepala_Lab']['name']);
    move_uploaded_file($_FILES['TTD_Kepala_Lab']['tmp_name'], $ttdKepalaLabPath);
}

// Load template
$templateProcessor = new TemplateProcessor("../template/template_proposal_bukber.docx");

// Set semua value dari POST
$templateProcessor->setValue('Nama_Kegiatan', $_POST['Nama_Kegiatan']);
$templateProcessor->setValue('Judul_Kegiatan', $_POST['Judul_Kegiatan']);
$templateProcessor->setValue('Periode_Aslab', $_POST['Periode_Aslab']);
$templateProcessor->setValue('Tahun_Kegiatan', $_POST['Tahun_Kegiatan']);
$templateProcessor->setValue('Waktu_Kegiatan', $_POST['Waktu_Kegiatan']);
$templateProcessor->setValue('Tempat_Kegiatan', $_POST['Tempat_Kegiatan']);
$templateProcessor->setValue('Total_Biaya_Kegiatan', $_POST['Total_Biaya_Kegiatan']);
$templateProcessor->setValue('Nama_Ketua_Pelaksana', $_POST['Nama_Ketua_Pelaksana']);
$templateProcessor->setValue('Nama_Penanggung_Jawab', $_POST['Nama_Penanggung_Jawab']);
$templateProcessor->setValue('NIMKetuaPelaksana', $_POST['NIMKetuaPelaksana']);
$templateProcessor->setValue('NIMPenanggungJawab', $_POST['NIMPenanggungJawab']);
$templateProcessor->setValue('Tanggal_Kegiatan', $_POST['Tanggal_Kegiatan']);
$templateProcessor->setValue('Tempat_tanggal_pengesahan', $_POST['Tempat_tanggal_pengesahan']);
$templateProcessor->setValue('Tangga_Kegiatan', $_POST['Tangga_Kegiatan']); // typo di "Tangga", tapi ikut aja sesuai placeholder Word
$templateProcessor->setValue('Nama_Anggota_Sekben_1', $_POST['Nama_Anggota_Sekben_1']);
$templateProcessor->setValue('Nama_Anggota_Sekben_2', $_POST['Nama_Anggota_Sekben_2']);
$templateProcessor->setValue('Nama_Anggota_Acara_1', $_POST['Nama_Anggota_Acara_1']);
$templateProcessor->setValue('Nama_Anggota_Acara_2', $_POST['Nama_Anggota_Acara_2']);
$templateProcessor->setValue('Nama_Anggota_PDD_1', $_POST['Nama_Anggota_PDD_1']);
$templateProcessor->setValue('Nama_Anggota_PDD_2', $_POST['Nama_Anggota_PDD_2']);
$templateProcessor->setValue('Nama_Anggota_PDD_3', $_POST['Nama_Anggota_PDD_3']);
$templateProcessor->setValue('Nama_Anggota_PDD_4', $_POST['Nama_Anggota_PDD_4']);
$templateProcessor->setValue('Nama_Anggota_Konsumsi_1', $_POST['Nama_Anggota_Konsumsi_1']);
$templateProcessor->setValue('Nama_Anggota_Konsumsi_2', $_POST['Nama_Anggota_Konsumsi_2']);
$templateProcessor->setValue('Nama_Anggota_Konsumsi_3', $_POST['Nama_Anggota_Konsumsi_3']);
$templateProcessor->setValue('Nama_Anggota_Konsumsi_4', $_POST['Nama_Anggota_Konsumsi_4']);
$templateProcessor->setValue('Hari_Kegiatan', $_POST['Hari_Kegiatan']);
$templateProcessor->setValue('Rundown_Kegiatan', $_POST['Rundown_Kegiatan']);
$templateProcessor->setValue('Anggaran_Kegiatan_Buka_Bersama', $_POST['Anggaran_Kegiatan_Buka_Bersama']);
$templateProcessor->setValue('Pemasukan_Kegiatan_Buka_Bersama', $_POST['Pemasukan_Kegiatan_Buka_Bersama']);

// Tanda tangan Kepala Lab
if ($ttdKepalaLabPath) {
    $templateProcessor->setImageValue('TTD_Kepala_Lab', [
        'path' => $ttdKepalaLabPath,
        'width' => 100,
        'height' => 50,
        'ratio' => true
    ]);
} else {
    $templateProcessor->setValue('TTD_Kepala_Lab', '');
}

// Simpan dan kirim ke user
$outputFile = 'proposal_bukabersama_generated.docx';
$templateProcessor->saveAs($outputFile);

header('Content-Description: File Transfer');
header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
header('Content-Disposition: attachment; filename="' . basename($outputFile) . '"');
header('Content-Length: ' . filesize($outputFile));
readfile($outputFile);
unlink($outputFile);
exit;
?>

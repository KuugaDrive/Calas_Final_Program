<?php
include 'koneksi.php';

$kd_kegiatan = $_GET['kd_kegiatan'] ?? '';

if (!$kd_kegiatan) {
    die("Kode kegiatan tidak diberikan.");
}

$sql = "
SELECT p.judul, k.nama_kegiatan, k.tgl_kegiatan, p.tgl_setuju
FROM proposal p
JOIN kegiatan k ON p.kd_kegiatan = k.kd_kegiatan
WHERE k.kd_kegiatan = ?
ORDER BY p.tgl_setuju DESC
LIMIT 1
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $kd_kegiatan);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Proposal terkait kegiatan tidak ditemukan.");
}

$data = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <title>Verifikasi Proposal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .signature { margin-top: 40px; }
    </style>
</head>
<body>
    <p>
        Tanda tangan digital ini digunakan untuk proposal kegiatan <strong><?= htmlspecialchars($data['nama_kegiatan']) ?></strong> yang akan diselenggarakan pada <strong><?= htmlspecialchars(date('d M Y', strtotime($data['tgl_kegiatan']))) ?></strong>.
    </p>
    <p>
        <?= htmlspecialchars(date('d M Y', strtotime($data['tgl_setuju']))) ?>
    </p>
    <div class="signature">
        Achmad Syarif, S.T., M.Kom.<br />
        Kepala Lab ICT<br />
        Universitas Budi Luhur
    </div>
</body>
</html>

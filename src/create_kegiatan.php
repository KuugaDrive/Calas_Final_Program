<?php
session_start();
include 'koneksi.php'; // Pastikan file koneksi.php ada dan berfungsi dengan baik

// Fungsi generate kd_kegiatan otomatis berformat K + 3 digit angka
function generateKdKegiatan($conn) {
    $sql = "SELECT kd_kegiatan FROM kegiatan ORDER BY kd_kegiatan DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        error_log("Error preparing generateKdKegiatan query: " . $conn->error);
        return 'K001'; // Fallback jika query gagal
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result && $row = $result->fetch_assoc()) {
        $lastKd = $row['kd_kegiatan']; // contoh K023
        $num = (int)substr($lastKd, 1); // ambil angka 023 -> 23
        $num++;
        $stmt->close(); // Tutup statement
        return 'K' . str_pad($num, 3, '0', STR_PAD_LEFT);
    } else {
        $stmt->close(); // Tutup statement
        return 'K001'; // Jika belum ada data atau query gagal
    }
}

// Cek user login dan role
// Tambahkan 'kepala_lab' jika mereka juga bisa membuat kegiatan
if (!isset($_SESSION['kd_user']) || !in_array($_SESSION['role'], ['asisten_lab', 'spv', 'kepala_lab'])) {
    header('Location: login.php');
    exit;
}

// Tentukan link dashboard sesuai role (diambil dari dashboard_user.php)
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'kepala_lab') {
        $dashboard_link = 'dashboard_admin.php';
    } elseif ($_SESSION['role'] === 'asisten_lab') {
        $dashboard_link = 'dashboard_user.php';
    } elseif ($_SESSION['role'] === 'spv') {
        $dashboard_link = 'dashboard_user.php'; // Sesuaikan jika ada dashboard spv berbeda
    } else {
        $dashboard_link = 'login.php'; // Default jika role tidak dikenal
    }
} else {
    $dashboard_link = 'login.php'; // Default jika role tidak diset
}


$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kd_user = $_SESSION['kd_user'];
    $nama_kegiatan = trim($_POST['nama_kegiatan'] ?? '');
    $judul_kegiatan = trim($_POST['judul_kegiatan'] ?? '');
    $tgl_kegiatan = $_POST['tgl_kegiatan'] ?? '';

    if (empty($nama_kegiatan) || empty($judul_kegiatan) || empty($tgl_kegiatan)) {
        $error = "Nama kegiatan, judul kegiatan, dan tanggal wajib diisi.";
    } else {
        $kd_kegiatan = generateKdKegiatan($conn);

        $sql = "INSERT INTO kegiatan (kd_kegiatan, kd_user, nama_kegiatan, judul_kegiatan, tgl_kegiatan, status) 
                VALUES (?, ?, ?, ?, ?, 'Proses')";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            $error = "Gagal menyiapkan statement SQL: " . htmlspecialchars($conn->error);
            error_log("SQL Prepare Error in create_kegiatan.php: " . $conn->error);
        } else {
            $stmt->bind_param("sssss", $kd_kegiatan, $kd_user, $nama_kegiatan, $judul_kegiatan, $tgl_kegiatan);

            if ($stmt->execute()) {
                $success = "Kegiatan berhasil dibuat!";
                header("Location: dashboard_user.php");
                exit;
            } else {
                $error = "Gagal menyimpan data kegiatan: " . htmlspecialchars($stmt->error);
                error_log("SQL Execute Error in create_kegiatan.php: " . $stmt->error);
            }
            $stmt->close();
        }
    }
}
$conn->close(); 
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Buat Kegiatan Baru</title>
    <link rel="stylesheet" href="css/style_dashboard.css" />
    <link rel="stylesheet" href="css/style_upload.css" />
    <link rel="stylesheet" href="css/style.css" />
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <script>
        $( function() {
            $("#tgl_kegiatan").datepicker({
                dateFormat: "yy-mm-dd"
            });
        });
    </script>
    <style>
        /* Styling tambahan untuk menyesuaikan form di dalam main-content */
        .main-content .form-box {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            max-width: 600px; /* Batasi lebar form agar tidak terlalu lebar */
            margin: 20px auto; /* Pusatkan form di main-content */
        }

        .main-content .form-box label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .main-content .form-box input[type="text"] {
            width: calc(100% - 20px); /* Kurangi padding dari lebar total */
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        /* Menggunakan warna btn-primary dari dashboard untuk tombol submit */
        .main-content .form-box button[type="submit"] {
            background-color: #6f42c1; /* Warna biru-ungu yang konsisten dengan dashboard */
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            margin-right: 10px; /* Jarak dengan tombol kembali */
            transition: background-color 0.3s ease;
        }

        .main-content .form-box button[type="submit"]:hover {
            background-color: #5a34a8; /* Sedikit lebih gelap saat hover */
        }

        /* Styling untuk tombol "Kembali ke Dashboard" */
        .main-content .btn-kembali-dashboard {
            display: inline-block;
            background-color: #6c757d; /* Warna abu-abu yang konsisten */
            color: white;
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .main-content .btn-kembali-dashboard:hover {
            background-color: #5a6268;
        }

        /* Styling untuk pesan error/sukses */
        .error-message {
            color: #dc3545; /* Merah untuk error */
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .success-message {
            color: #28a745; /* Hijau untuk sukses */
            background-color: #d4edda;
            border: 1px solid #c3e6cb;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <nav class="sidebar">
            <img src="img/logo_lab.png" alt="Logo Lab ICT" class="logo"/>
            <a href="<?= htmlspecialchars($dashboard_link) ?>" class="active">Dashboard</a>
            <a href="proposal.php">Proposal</a>
            <a href="lpj.php">LPJ</a>
            <a href="sertifikat.php">Sertifikat</a>
            <a href="banner.php">Banner</a>
            <a href="dokumentasi.php">Dokumentasi</a>
        </nav>
        <main class="main-content">
        <div class="header-top">
        <div class="welcome">
        <?php
          if (!isset($_SESSION['logged_in']) || ($_SESSION['role'] !== 'asisten_lab' && $_SESSION['role'] !== 'spv')) {
            header('Location: login.php');
            exit;
        }
        ?>
        <h2>Selamat Datang, <?php echo $_SESSION['nama'] ?></h2>
      </div>
        <a href="logout.php" class="btn-logout">Logout</a>
        </div>
            <h1>Buat Kegiatan Baru</h1>
            <?php if ($error): ?>
                <p class="error-message"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <?php if ($success): ?>
                <p class="success-message"><?= htmlspecialchars($success) ?></p>
            <?php endif; ?>

            <form method="post" class="form-box">
                <label for="nama_kegiatan">Nama Kegiatan:</label>
                <input type="text" id="nama_kegiatan" name="nama_kegiatan" placeholder="Masukkan Nama Kegiatan" required />

                <label for="judul_kegiatan">Judul Kegiatan:</label>
                <input type="text" id="judul_kegiatan" name="judul_kegiatan" placeholder="Masukkan Judul Kegiatan" required />

                <label for="tgl_kegiatan">Tanggal Kegiatan:</label>
                <input type="text" id="tgl_kegiatan" name="tgl_kegiatan" placeholder="Pilih Tanggal Kegiatan" required />

                <button type="submit">Simpan</button>
            </form>
        </main>
    </div>
</body>
</html>
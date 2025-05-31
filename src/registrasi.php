<?php
require 'koneksi.php';
session_start();

$error = '';
$registration_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kd_user = $_POST['kd_user'];
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Validasi kd_user: tepat 10 digit angka
    if (!preg_match('/^\d{10}$/', $kd_user)) {
        $_SESSION['error_message'] = "Kode user harus berupa tepat 10 digit angka!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Validasi nama: hanya huruf dan spasi
    if (!preg_match('/^[a-zA-Z\s]+$/', $nama)) {
        $_SESSION['error_message'] = "Nama hanya boleh berisi huruf dan spasi!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    // Validasi password: minimal 6 karakter, harus ada huruf dan angka
    if (strlen($password) < 6 || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/\d/', $password)) {
        $_SESSION['error_message'] = "Password minimal 6 karakter dan harus mengandung huruf serta angka!";
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $role = 'asisten_lab';

    // Cek apakah kd_user sudah terdaftar
    $cekQuery = "SELECT kd_user FROM user WHERE kd_user = ?";
    $cekStmt = $conn->prepare($cekQuery);
    if ($cekStmt === false) {
        $_SESSION['error_message'] = "Terjadi kesalahan pada server: " . $conn->error;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    $cekStmt->bind_param("s", $kd_user);
    $cekStmt->execute();
    $cekStmt->store_result();

    if ($cekStmt->num_rows > 0) {
        $_SESSION['error_message'] = "Kode user sudah terdaftar!";
        $cekStmt->close();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    $cekStmt->close();

    $query = "INSERT INTO user (kd_user, nama, email, role, password) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    if ($stmt === false) {
        $_SESSION['error_message'] = "Terjadi kesalahan pada server: " . $conn->error;
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
    $stmt->bind_param("sssss", $kd_user, $nama, $email, $role, $password_hash);

    if ($stmt->execute()) {
        $registration_success = true;
        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['error_message'] = "Gagal membuat user: " . $stmt->error;
        $stmt->close();
        $conn->close();
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Registrasi User Baru</title>
<link rel="stylesheet" href="css/style.css" />
<style>
.modal-custom {
  display: none;
  position: fixed;
  z-index: 9999;
  inset: 0;
  background-color: rgba(0,0,0,0.5);
  display: flex;
  justify-content: center;
  align-items: center;
  animation: fadeInBg 0.3s ease forwards;
}
.modal-content-custom {
  background: white;
  padding: 30px 40px;
  border-radius: 16px;
  text-align: center;
  box-shadow: 0 12px 28px rgba(0,0,0,0.2);
  max-width: 320px;
  width: 90%;
  animation: popIn 0.4s ease forwards;
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  color: #2c3e50;
  user-select: none;
}
.icon-checkmark {
  font-size: 50px;
  color: #4BB543;
  margin-bottom: 15px;
  animation: bounce 0.6s ease infinite alternate;
}
@keyframes fadeInBg {
  from { background-color: rgba(0,0,0,0); }
  to { background-color: rgba(0,0,0,0.5); }
}
@keyframes popIn {
  0% {
    transform: scale(0.6);
    opacity: 0;
  }
  100% {
    transform: scale(1);
    opacity: 1;
  }
}
@keyframes bounce {
  0% { transform: translateY(0); }
  100% { transform: translateY(-10px); }
}
.error-message {
  background-color: #f8d7da;
  color: #721c24;
  padding: 10px;
  border: 1px solid #f5c6cb;
  border-radius: 4px;
  margin-bottom: 15px;
  text-align: center;
}
</style>
<script>
function validateKdUser(event) {
  const input = event.target;
  input.value = input.value.replace(/[^0-9]/g, '').slice(0, 10);
}
function validateNama(event) {
  const input = event.target;
  input.value = input.value.replace(/[^a-zA-Z\s]/g, '');
}
function validatePassword(event) {
  const input = event.target;
  // Optional: batasi karakter tertentu jika ingin (boleh dihapus)
}
function validateFormClientSide() {
  const kdUser = document.querySelector('input[name="kd_user"]').value.trim();
  const nama = document.querySelector('input[name="nama"]').value.trim();
  const password = document.querySelector('input[name="password"]').value;

  if (!/^\d{10}$/.test(kdUser)) {
    alert('Kode user harus berupa tepat 10 digit angka!');
    return false;
  }
  if (!/^[a-zA-Z\s]+$/.test(nama)) {
    alert('Nama hanya boleh berisi huruf dan spasi!');
    return false;
  }
  if (password.length < 6) {
    alert('Password minimal 6 karakter!');
    return false;
  }
  if (!/[a-zA-Z]/.test(password) || !/\d/.test(password)) {
    alert('Password harus mengandung huruf dan angka!');
    return false;
  }
  return true;
}
</script>
</head>
<body>

<div class="container">
  <div class="left-panel">
    <img src="img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
  </div>
  <div class="right-panel">
    <h1>Registrasi User Baru</h1>

    <?php if (!empty($_SESSION['error_message'])): ?>
      <p class="error-message"><?= htmlspecialchars($_SESSION['error_message']) ?></p>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form method="POST" class="form-box" action="<?= htmlspecialchars($_SERVER['PHP_SELF']); ?>" onsubmit="return validateFormClientSide()">
      <label><b>NIM atau NIP</b></label><br>
      <input
        type="text"
        name="kd_user"
        placeholder="Masukkan NIM atau NIP (10 digit)"
        required
        maxlength="10"
        oninput="validateKdUser(event)"
      /><br />

      <label><b>Nama</b></label><br>
      <input type="text" name="nama" placeholder="Masukkan Nama" required oninput="validateNama(event)" /><br />

      <label><b>Email</b></label><br>
      <input type="email" name="email" placeholder="Masukkan Email" required /><br />

      <label><b>Password</b></label><br>
      <input type="password" name="password" placeholder="Masukkan Password" required /><br />

      <button type="submit">Buat User</button>
    </form>
  </div>
</div>

<?php if ($registration_success): ?>
<div id="successModal" class="modal-custom" style="display: flex;">
  <div class="modal-content-custom">
    <div class="icon-checkmark">&#10004;</div>
    <h3>User berhasil dibuat!</h3>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('successModal');
  setTimeout(() => {
    modal.style.display = 'none';
    window.location.href = 'dashboard_admin.php';
  }, 1500);
});
</script>
<?php endif; ?>

</body>
</html>

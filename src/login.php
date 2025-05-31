<?php
require 'koneksi.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kd_user = $_POST['kd_user'];
    $password = $_POST['password'];

    // Validasi server-side: hanya angka max 10 digit
    if (!preg_match('/^\d{1,10}$/', $kd_user)) {
        $error = "Kode User harus berupa angka dengan maksimal 10 digit!";
    } else {
        $query = "SELECT * FROM user WHERE kd_user = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $kd_user);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['kd_user'] = $user['kd_user'];
                $_SESSION['nama'] = $user['nama'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['logged_in'] = true;

                if ($user['role'] === 'kepala_lab') {
                    header('Location: dashboard_admin.php');
                } elseif ($user['role'] === 'spv' || $user['role'] === 'asisten_lab') {
                    header('Location: dashboard_user.php');
                } else {
                    session_destroy();
                    $error = "Role user tidak dikenali.";
                }
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "User tidak ditemukan!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css" />
    <script>
    // Hanya angka max 10 digit di input kd_user
    function validateInput(event) {
        const input = event.target;
        input.value = input.value.replace(/[^0-9]/g, '').slice(0, 10);
    }
    </script>
</head>
<body>
    <div class="container">
        <div class="left-panel">
            <img src="img/logo_lab.png" alt="Logo Lab ICT" class="logo" />
        </div>
        <div class="right-panel">
            <h1>Login</h1>
            <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
            <form method="POST" class="form-box">
                <label><b>NIM atau NIP</b></label><br>
                <input
                    type="text"
                    name="kd_user"
                    placeholder="Masukkan NIM atau NIP"
                    required
                    pattern="\d{1,10}"
                    maxlength="10"
                    oninput="validateInput(event)"
                /><br />

                <label><b>Password</b></label><br>
                <input type="password" name="password" placeholder="Masukkan password" required /><br />

                <button type="submit">Login</button>
            </form>
        </div>
    </div>
</body>
</html>

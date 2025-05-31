<?php
session_start();
include 'db.php';

$error = ""; // untuk menyimpan pesan error

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = isset($_POST['email']) ? $_POST['email'] : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if (!empty($email) && !empty($password)) {
        $sql = "SELECT * FROM user WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows == 1) {
            $user = $result->fetch_assoc();

            if ($password == $user['password']) {
                $_SESSION['user_id'] = $user['kd_user'];
                $_SESSION['user_name'] = $user['nama'];
                $_SESSION['user_role'] = $user['role'];

                header("Location: index.php");
                exit;
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Akun tidak ditemukan!";
        }
    } else {
        $error = "Email dan password harus diisi!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login event lab </title>
    <style>
        body {
            background-color: lightblue;
            font-family: Arial, sans-serif;
        }

        .login-container {
            width: 350px;
            margin: 100px auto;
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.2);
            text-align: center;
        }

        input[type="email"], input[type="password"] {
            width: 90%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button {
            padding: 15px 15px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .error {
            color: red;
            margin-bottom: 10px;
        }

        /* Styling untuk logo */
        .logo {
            width: 100px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<div class="login-container">
    <img src="logo.png" alt="Logo" class="logo">
    <h2>Login event lab ICT</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST">
        <input type="email" name="email" placeholder="Email" required><br>
        <input type="password" name="password" placeholder="Password" required><br>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>

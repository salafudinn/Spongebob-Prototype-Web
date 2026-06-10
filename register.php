<?php
require_once 'core/auth.php';

if (isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username        = bersihkanInput($_POST['username']);
    $password        = $_POST['password'];
    $role            = $_POST['role'];

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $query = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error: Gagal mendaftar.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - The Krusty Krab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: #1a1a1a;
        }
        .auth-container {
            width: 100%;
            max-width: 800px;
            padding: 40px;
        }
        .badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            border: 1px solid #dcdcdc;
            border-radius: 30px;
            padding: 8px 18px;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 40px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.03);
            color: #333;
        }
        .badge svg {
            width: 18px;
            height: 18px;
        }
        .radio-group {
            display: flex;
            gap: 20px;
            margin-bottom: 24px;
            font-size: 14px;
            font-weight: 600;
        }
        .radio-group label {
            display: flex;
            align-items: center;
            gap: 6px;
            cursor: pointer;
        }
        .form-group {
            margin-bottom: 24px;
        }
        .form-group label {
            display: block;
            margin-bottom: 10px;
            font-weight: 600;
            font-size: 15px;
        }
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 14px 20px;
            border: 1px solid #d1d1d1;
            border-radius: 40px;
            font-size: 14px;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.2s;
        }
        .form-group input[type="text"]::placeholder,
        .form-group input[type="password"]::placeholder {
            color: #a0a0a0;
        }
        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            border-color: #2b4c3e;
        }
        .footer-row {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            margin-top: 50px;
        }
        .footer-row a {
            color: #555;
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 10px;
            display: block;
        }
        .footer-row a:hover {
            color: #000;
        }
        .footer-right {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 11px;
            font-weight: 600;
            color: #888;
        }
        .btn-continue {
            background-color: #2b4c3e;
            color: white;
            border: none;
            border-radius: 30px;
            padding: 12px 28px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.2s;
            box-shadow: 0 4px 10px rgba(43,76,62,0.2);
        }
        .btn-continue:hover {
            background-color: #1e362c;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="badge">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                <circle cx="9" cy="7" r="4"></circle>
                <line x1="19" y1="8" x2="19" y2="14"></line>
                <line x1="22" y1="11" x2="16" y2="11"></line>
            </svg>
            Register
        </div>

        <form action="register.php" method="POST">
            <div class="radio-group">
                <label>
                    <input type="radio" name="role" value="pelanggan" required checked>
                    Customer
                </label>
                <label>
                    <input type="radio" name="role" value="karyawan" required>
                    Worker
                </label>
            </div>

            <div class="form-group">
                <label>Username:</label>
                <input type="text" name="username" placeholder="Enter your username here" required>
            </div>

            <div class="form-group">
                <label>Password:</label>
                <input type="password" name="password" placeholder="Enter your password here" required>
            </div>

            <div class="footer-row">
                <a href="login.php">Already have an account? Sign in here.</a>
                <div class="footer-right">
                    Continuing opening account for the Krusty Krab Database...
                    <button type="submit" class="btn-continue">Continue</button>
                </div>
            </div>
        </form>
    </div>

</body>
</html>
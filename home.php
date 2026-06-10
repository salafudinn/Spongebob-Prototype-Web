<?php
require_once 'core/auth.php';
cekLogin();

// Hanya pelanggan yang lihat homepage ini; karyawan langsung ke dashboard
if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    header("Location: dashboard_karyawan.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Krusty Krab - Home</title>
    <meta name="description" content="Welcome to The Krusty Krab! Order the best Krabby Patty in Bikini Bottom.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #f0f0f0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: absolute;
            top: 20px;
            right: 28px;
            z-index: 100;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(10px);
            border-radius: 50px;
            padding: 8px 16px;
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.18);
        }

        .navbar img.nav-logo {
            height: 36px;
            width: 36px;
            object-fit: contain;
            border-radius: 50%;
        }

        .nav-divider {
            width: 1px;
            height: 22px;
            background: #ddd;
            margin: 0 4px;
        }

        .nav-icon {
            width: 34px;
            height: 34px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            text-decoration: none;
            transition: background 0.2s;
            cursor: pointer;
        }

        .nav-icon:hover {
            background: rgba(0,0,0,0.08);
        }

        .nav-icon img {
            width: 22px;
            height: 22px;
            object-fit: contain;
        }

        /* ── HERO ── */
        .hero {
            position: relative;
            width: 100%;
            height: 78vh;
            min-height: 520px;
            background-image: url('public/img/kk_interior.png');
            background-size: cover;
            background-position: center top;
            overflow: hidden;
        }

        .hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(
                to right,
                rgba(0, 0, 0, 0.60) 0%,
                rgba(0, 0, 0, 0.20) 45%,
                rgba(0, 0, 0, 0.0) 100%
            );
            z-index: 1;
        }

        /* teks: kiri, ~40% dari atas */
        .hero-content {
            position: absolute;
            top: 44%;
            transform: translateY(-50%);
            left: 52px;
            z-index: 2;
            color: white;
        }

        .hero-content h1 {
            font-size: 2rem;
            font-weight: 400;
            line-height: 1.3;
            text-shadow: 0 2px 10px rgba(0,0,0,0.55);
        }

        .hero-content h1 span {
            font-weight: 800;
        }

        .btn-order {
            display: inline-block;
            margin-top: 14px;
            color: white;
            text-decoration: none;
            font-size: 0.92rem;
            font-weight: 500;
            letter-spacing: 0.3px;
            padding: 4px 0;
            border-bottom: 1.5px solid rgba(255,255,255,0.85);
            transition: opacity 0.2s;
        }

        .btn-order:hover {
            opacity: 0.75;
        }

        /* sign: posisi referensi Figma */
        .hero-right {
            position: absolute;
            left: 84%;
            top: 67%;
            transform: translate(-50%, -50%);
            z-index: 2;
        }

        .hero-right img {
            height: 62vh;
            width: auto;
            object-fit: contain;
            display: block;
            filter: drop-shadow(0 6px 24px rgba(0,0,0,0.25));
        }

        /* ── FOOTER ── */
        footer {
            background: #efefef;
            border-top: 1px solid #ddd;
            padding: 32px 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
        }

        .footer-nav {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .footer-nav a {
            color: #222;
            text-decoration: none;
            font-size: 0.95rem;
            font-weight: 500;
            transition: color 0.2s;
        }

        .footer-nav a:hover {
            color: #c0392b;
        }

        .footer-info {
            text-align: center;
        }

        .footer-info p {
            font-size: 0.85rem;
            color: #555;
            font-weight: 600;
            margin-bottom: 6px;
        }

        .footer-info .hours {
            font-size: 1rem;
            color: #222;
            font-weight: 600;
            margin-bottom: 12px;
        }

        .social-icons {
            display: flex;
            gap: 14px;
            justify-content: center;
        }

        .social-icons a {
            color: #333;
            transition: color 0.2s;
        }

        .social-icons a:hover {
            color: #c0392b;
        }

        .social-icons svg {
            width: 22px;
            height: 22px;
        }

        .footer-logo img {
            height: 80px;
            width: 80px;
            object-fit: contain;
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="navbar">
        <a href="switch_role.php" title="Toggle Employee/Customer Role">
            <img src="public/img/kk_logo.png" alt="Krusty Krab Logo" class="nav-logo">
        </a>
        <div class="nav-divider"></div>
        <a href="logout.php" class="nav-icon" title="Logout" onclick="return confirm('Yakin ingin keluar?')">
            <img src="public/img/logout 1.png" alt="Logout">
        </a>
        <div class="nav-divider"></div>
        <a href="dashboard_pelanggan.php" class="nav-icon" title="Order">
            <img src="public/img/checkout 1.png" alt="Order">
        </a>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="hero-content">
            <h1>Hi, <span><?= htmlspecialchars($_SESSION['username']) ?>.</span><br>Welcome to KrustyKrab!</h1>
            <a href="dashboard_pelanggan.php" class="btn-order">Orders now &gt;&gt;</a>
        </div>
        <div class="hero-right">
            <img src="public/img/kk_sign.png" alt="The Krusty Krab Sign">
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <nav class="footer-nav">
            <a href="home.php">Home</a>
            <a href="dashboard_pelanggan.php">Orders</a>
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
        </nav>

        <div class="footer-info">
            <p>Operational Time:</p>
            <div class="hours">09.00 - 20.00</div>
            <div class="social-icons">
                <!-- Instagram -->
                <a href="#" title="Instagram">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5"/>
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/>
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"/>
                    </svg>
                </a>
                <!-- YouTube -->
                <a href="#" title="YouTube">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"/>
                        <polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"/>
                    </svg>
                </a>
                <!-- X / Twitter -->
                <a href="#" title="X (Twitter)">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
            </div>
        </div>

        <div class="footer-logo">
            <img src="public/img/kk_logo.png" alt="The Krusty Krab">
        </div>
    </footer>

</body>
</html>

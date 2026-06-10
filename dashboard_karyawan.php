<?php
require_once 'core/auth.php';
cekLogin();
if ($_SESSION['role'] !== 'karyawan' && $_SESSION['role'] !== 'admin') {
    header("Location: dashboard_pelanggan.php");
    exit;
}
require_once 'core/functions.php';

// Proses update status pesanan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $id = (int)$_POST['pesanan_id'];
    $status = bersihkanInput($_POST['status']);
    
    // update db
    $query = "UPDATE pesanan SET status = '$status' WHERE id = $id";
    mysqli_query($conn, $query);
    
    echo "<script>alert('Status pesanan berhasil diupdate!'); window.location='dashboard_karyawan.php';</script>";
}

// Ambil data pesanan (urutkan dari yang terbaru)
$pesanan_result = mysqli_query($conn, "SELECT * FROM pesanan ORDER BY created_at DESC");
$list_pesanan = [];
if ($pesanan_result) {
    while ($r = mysqli_fetch_assoc($pesanan_result)) {
        $list_pesanan[] = $r;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Dashboard - The Krusty Krab</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-image: url('public/img/Background.png');
            background-size: cover;
            background-attachment: fixed;
            margin: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* NAVBAR */
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
        .navbar img.nav-logo { height: 36px; width: 36px; object-fit: contain; border-radius: 50%; }
        .nav-divider { width: 1px; height: 22px; background: #ddd; margin: 0 4px; }
        .nav-icon { width: 34px; height: 34px; display: flex; align-items: center; justify-content: center; border-radius: 50%; text-decoration: none; transition: background 0.2s; cursor: pointer; }
        .nav-icon:hover { background: rgba(0,0,0,0.08); }
        .nav-icon img { width: 22px; height: 22px; object-fit: contain; }

        /* MAIN */
        .main-content {
            flex: 1;
            padding: 100px 40px 40px;
            display: flex;
            justify-content: center;
        }

        .card {
            background: white;
            border-radius: 20px;
            padding: 40px;
            width: 100%;
            max-width: 900px;
            box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        }

        .card h3 {
            font-size: 1.25rem;
            color: #111;
            margin-bottom: 16px;
            font-weight: 700;
        }

        .table-wrapper {
            margin-bottom: 40px;
            border-radius: 12px;
            overflow: hidden;
            border: 1px solid #ddd;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: center;
            font-size: 0.9rem;
        }

        thead {
            background-color: #2b4c3e;
            color: white;
        }

        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #ddd;
        }

        th {
            font-weight: 600;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .status-select {
            padding: 4px 8px;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 0.85rem;
        }

        .btn-update {
            background: #2b4c3e;
            color: white;
            border: none;
            padding: 6px 12px;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
        }

        .btn-update:hover {
            background: #1e362c;
        }

        /* FOOTER */
        footer {
            background: #efefef;
            border-top: 1px solid #ddd;
            padding: 32px 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            margin-top: auto;
        }
        .footer-nav { display: flex; flex-direction: column; gap: 8px; }
        .footer-nav a { color: #222; text-decoration: none; font-size: 0.95rem; font-weight: 500; transition: color 0.2s; }
        .footer-nav a:hover { color: #c0392b; }
        .footer-info { text-align: center; }
        .footer-info p { font-size: 0.85rem; color: #555; font-weight: 600; margin-bottom: 6px; }
        .footer-info .hours { font-size: 1rem; color: #222; font-weight: 600; margin-bottom: 12px; }
        .social-icons { display: flex; gap: 14px; justify-content: center; }
        .social-icons a { color: #333; transition: color 0.2s; }
        .social-icons a:hover { color: #c0392b; }
        .social-icons svg { width: 22px; height: 22px; }
        .footer-logo img { height: 80px; width: 80px; object-fit: contain; }

    </style>
</head>
<body>

    <nav class="navbar">
        <a href="switch_role.php" title="Toggle Employee/Customer Role">
            <img src="public/img/kk_logo.png" alt="Logo" class="nav-logo">
        </a>
        <div class="nav-divider"></div>
        <a href="logout.php" class="nav-icon" title="Logout" onclick="return confirm('Yakin ingin keluar?')">
            <img src="public/img/logout 1.png" alt="Logout">
        </a>
    </nav>

    <div class="main-content">
        <div class="card">
            <h3>Menu Orders List</h3>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Date</th>
                            <th>Customer Name</th>
                            <th>Notes</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($list_pesanan)): ?>
                            <tr>
                                <td colspan="7">Belum ada pesanan.</td>
                            </tr>
                        <?php else: ?>
                            <?php $no = 1; foreach ($list_pesanan as $p): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= date('Y-m-d', strtotime($p['created_at'])) ?></td>
                                <td><?= htmlspecialchars($p['nama_pelanggan']) ?></td>
                                <td><?= htmlspecialchars($p['catatan']) ?></td>
                                <td>Rp <?= number_format($p['total_harga'], 0, ',', '.') ?></td>
                                <td><?= htmlspecialchars($p['status']) ?></td>
                                <td>
                                    <form action="dashboard_karyawan.php" method="POST" style="display:inline; display: flex; gap: 4px; justify-content: center; align-items: center;">
                                        <input type="hidden" name="pesanan_id" value="<?= $p['id'] ?>">
                                        <select name="status" class="status-select">
                                            <option value="menunggu" <?= $p['status'] == 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                                            <option value="dibuat" <?= $p['status'] == 'dibuat' ? 'selected' : '' ?>>Dibuat</option>
                                            <option value="disajikan" <?= $p['status'] == 'disajikan' ? 'selected' : '' ?>>Disajikan</option>
                                        </select>
                                        <button type="submit" name="update_status" class="btn-update">Save</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <footer>
        <nav class="footer-nav">
            <a href="dashboard_karyawan.php">Home</a>
            <a href="dashboard_karyawan.php">Orders</a>
            <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout</a>
        </nav>
        <div class="footer-info">
            <p>Operational Time:</p>
            <div class="hours">09.00 - 20.00</div>
            <div class="social-icons">
                <a href="#" title="Instagram"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg></a>
                <a href="#" title="YouTube"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.95-1.96C18.88 4 12 4 12 4s-6.88 0-8.59.46A2.78 2.78 0 0 0 1.46 6.42 29 29 0 0 0 1 12a29 29 0 0 0 .46 5.58 2.78 2.78 0 0 0 1.95 1.96C5.12 20 12 20 12 20s6.88 0 8.59-.46a2.78 2.78 0 0 0 1.95-1.96A29 29 0 0 0 23 12a29 29 0 0 0-.46-5.58z"></path><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon></svg></a>
                <a href="#" title="X (Twitter)"><svg viewBox="0 0 24 24" fill="currentColor"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"></path></svg></a>
            </div>
        </div>
        <div class="footer-logo">
            <img src="public/img/kk_logo.png" alt="The Krusty Krab">
        </div>
    </footer>

</body>
</html>

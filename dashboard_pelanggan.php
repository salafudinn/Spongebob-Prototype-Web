<?php
require_once 'core/auth.php';
cekLogin();
if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    header("Location: dashboard_karyawan.php");
    exit;
}
require_once 'core/functions.php';

// Proses pesanan
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pesan'])) {
    $catatan = bersihkanInput($_POST['catatan']);
    $nama_pelanggan = $_SESSION['username'];
    
    $total_harga = 0;
    $pesanan_items = [];
    
    // hitung menu
    if (isset($_POST['menu'])) {
        foreach ($_POST['menu'] as $id => $item) {
            if ($item['jumlah'] > 0) {
                // ambil detail menu
                $id = (int)$id;
                $res = mysqli_query($conn, "SELECT nama, harga FROM menu WHERE id = $id");
                if ($row = mysqli_fetch_assoc($res)) {
                    $subtotal = $row['harga'] * (int)$item['jumlah'];
                    $total_harga += $subtotal;
                    
                    $pesanan_items[] = [
                        'tipe' => 'menu',
                        'item_id' => $id,
                        'nama_item' => $row['nama'],
                        'harga_satuan' => $row['harga'],
                        'jumlah' => (int)$item['jumlah']
                    ];
                }
            }
        }
    }
    
    // hitung merch
    if (isset($_POST['merch'])) {
        foreach ($_POST['merch'] as $id => $item) {
            if ($item['jumlah'] > 0) {
                // ambil detail merch
                $id = (int)$id;
                $res = mysqli_query($conn, "SELECT nama, harga FROM merchandise WHERE id = $id");
                if ($row = mysqli_fetch_assoc($res)) {
                    $subtotal = $row['harga'] * (int)$item['jumlah'];
                    $total_harga += $subtotal;
                    
                    $pesanan_items[] = [
                        'tipe' => 'merchandise',
                        'item_id' => $id,
                        'nama_item' => $row['nama'],
                        'harga_satuan' => $row['harga'],
                        'jumlah' => (int)$item['jumlah']
                    ];
                }
            }
        }
    }
    
    if ($total_harga > 0) {
        $q_pesanan = "INSERT INTO pesanan (nama_pelanggan, catatan, total_harga, status) VALUES ('$nama_pelanggan', '$catatan', $total_harga, 'menunggu')";
        if (mysqli_query($conn, $q_pesanan)) {
            $pesanan_id = mysqli_insert_id($conn);
            foreach ($pesanan_items as $pi) {
                $q_detail = "INSERT INTO pesanan_detail (pesanan_id, tipe, item_id, nama_item, harga_satuan, jumlah) 
                             VALUES ($pesanan_id, '{$pi['tipe']}', {$pi['item_id']}, '{$pi['nama_item']}', {$pi['harga_satuan']}, {$pi['jumlah']})";
                mysqli_query($conn, $q_detail);
            }
            echo "<script>alert('Pesanan berhasil dibuat! Menunggu diproses.'); window.location='dashboard_pelanggan.php';</script>";
            exit;
        }
    } else {
        echo "<script>alert('Pilih minimal satu pesanan!');</script>";
    }
}

$menu_list = getAllData('menu');
$merch_list = getAllData('merchandise');

// ambil info pesanan pengguna ini
$q_pesanan_saya = "SELECT * FROM pesanan WHERE nama_pelanggan = '{$_SESSION['username']}' ORDER BY id DESC LIMIT 5";
$res_pesanan_saya = mysqli_query($conn, $q_pesanan_saya);
$pesanan_saya = [];
if ($res_pesanan_saya) {
    while ($r = mysqli_fetch_assoc($res_pesanan_saya)) {
        $pesanan_saya[] = $r;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Dashboard - The Krusty Krab</title>
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
            background: #fdfdfd;
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

        /* ALERTS */
        .info-tab {
            background: #e8f5e9;
            border-left: 4px solid #2b4c3e;
            padding: 16px;
            border-radius: 6px;
            margin-bottom: 24px;
            color: #2e4d3a;
        }
        .info-tab h4 { margin: 0 0 10px 0; font-size: 1rem; }
        .info-tab ul { margin: 0; padding-left: 20px; font-size: 0.9rem; }
        
        /* IMAGE GRID */
        .img-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        .img-grid.merch-grid {
            grid-template-columns: repeat(5, 1fr);
        }
        .img-item {
            background: white;
            border-radius: 12px;
            padding: 8px;
            border: 1px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            aspect-ratio: 4/3;
        }
        .img-item img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            border-radius: 8px;
        }

        /* TABLES */
        .table-wrapper {
            background: white;
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
            padding: 10px 14px;
            border-bottom: 1px solid #ddd;
            vertical-align: middle;
        }
        th { font-weight: 600; }
        tbody tr:last-child td { border-bottom: none; }
        
        /* Action Buttons */
        .count-display {
            width: 30px;
            text-align: center;
            font-weight: 600;
            border: none;
            background: transparent;
            font-size: 1rem;
            outline: none;
        }
        /* remove number arrows */
        .count-display::-webkit-outer-spin-button,
        .count-display::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }
        .action-cell {
            display: flex;
            gap: 6px;
            justify-content: center;
            align-items: center;
        }
        .action-btn {
            width: 24px;
            height: 24px;
            border-radius: 4px;
            border: none;
            color: white;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 14px;
        }
        .btn-plus { background-color: #2b4c3e; }
        .btn-plus:hover { background-color: #1e362c; }
        .btn-minus { background-color: #a02020; }
        .btn-minus:hover { background-color: #7d1717; }

        /* FORM ORDERS DATA */
        .form-section {
            background: #eef3eb;
            padding: 24px;
            border-radius: 12px;
            border: 1px solid #c8d6c5;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 6px;
            font-size: 0.9rem;
            font-weight: 600;
            color: #333;
        }
        .form-group textarea, .form-group input[type="text"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-family: 'Inter', sans-serif;
            font-size: 0.9rem;
            box-sizing: border-box;
            outline: none;
        }
        .form-group textarea:focus, .form-group input[type="text"]:focus {
            border-color: #2b4c3e;
        }
        
        .action-buttons {
            display: flex;
            gap: 12px;
            margin-top: 24px;
        }
        .btn {
            padding: 10px 24px;
            border: none;
            border-radius: 30px;
            font-size: 0.9rem;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .btn:hover { opacity: 0.85; }
        .btn-cancel {
            background-color: #5ab17c;
            color: white;
            text-decoration: none;
        }
        .btn-save {
            background-color: #4a5c54;
            color: white;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        /* STATUS BADGE */
        .badge-status {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }
        .status-menunggu { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .status-dibuat { background-color: #cce5ff; color: #004085; border: 1px solid #b8daff; }
        .status-disajikan { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }

        /* HEADER TOTAL */
        .live-total {
            font-size: 1.1rem;
            font-weight: 700;
            color: #2b4c3e;
            background: #fff;
            padding: 8px 16px;
            border-radius: 30px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.05);
            display: inline-block;
            margin-bottom: 24px;
            border: 1px solid #ddd;
        }

        /* IMG HOVER */
        .img-item {
            background: white;
            border-radius: 12px;
            padding: 8px;
            border: 1px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
            aspect-ratio: 4/3;
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275), box-shadow 0.3s;
        }
        .img-item:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
            z-index: 10;
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
    <script>
        function updateCount(inputId, delta) {
            const input = document.getElementById(inputId);
            let val = parseInt(input.value);
            if (isNaN(val)) val = 0;
            val += delta;
            if (val < 0) val = 0;
            input.value = val;
            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;
            const inputs = document.querySelectorAll('.count-display');
            inputs.forEach(input => {
                let price = parseInt(input.getAttribute('data-price')) || 0;
                let count = parseInt(input.value) || 0;
                total += price * count;
            });
            document.getElementById('live-total-display').innerText = "Total Bayar: Rp " + total.toLocaleString('id-ID');
        }
    </script>
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
        <div class="nav-divider"></div>
        <a href="home.php" class="nav-icon" title="Home">
            <img src="public/img/checkout 1.png" alt="Home">
        </a>
    </nav>

    <div class="main-content">
        <div class="card">
            
            <?php if (!empty($pesanan_saya)): ?>
            <div class="info-tab">
                <h4>Status Pesanan Terbaru Anda:</h4>
                <ul style="list-style: none; padding-left: 0; margin-top: 10px;">
                    <?php foreach($pesanan_saya as $ps): ?>
                        <li style="margin-bottom: 8px; background: rgba(255,255,255,0.7); padding: 8px 12px; border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                            <span>Pesanan #<?= $ps['id'] ?> (Rp <?= number_format($ps['total_harga'], 0, ',', '.') ?>)</span>
                            <span class="badge-status status-<?= strtolower($ps['status']) ?>"><?= htmlspecialchars($ps['status']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <div id="live-total-display" class="live-total">Total Bayar: Rp 0</div>

            <form action="dashboard_pelanggan.php" method="POST">
                
                <h3>Krusty Krab's Menu</h3>
                <div class="img-grid">
                    <div class="img-item"><img src="public/img/burger.jpg" alt="Burger"></div>
                    <div class="img-item"><img src="public/img/sandwich.jpg" alt="Sandwich"></div>
                    <div class="img-item"><img src="public/img/pizza.jpg" alt="Pizza"></div>
                    <div class="img-item"><img src="public/img/spaghetti.webp" alt="Spaghetti"></div>
                    <div class="img-item"><img src="public/img/fishandchips.webp" alt="Fish and Chips"></div>
                    <div class="img-item"><img src="public/img/soup.jpg" alt="Soup"></div>
                    <div class="img-item"><img src="public/img/steak.webp" alt="Steak"></div>
                    <div class="img-item"><img src="public/img/macandcheese.jpg" alt="Mac & Cheese"></div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Menu</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($menu_list)): ?>
                                <tr><td colspan="5">Tidak ada menu.</td></tr>
                            <?php else: ?>
                                <?php foreach ($menu_list as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars(sprintf("MN%03d", $m['id'])) ?></td>
                                    <td><?= htmlspecialchars($m['nama']) ?><br><small style="color:#777;"><?= htmlspecialchars($m['deskripsi']) ?></small></td>
                                    <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <input type="number" name="menu[<?= $m['id'] ?>][jumlah]" id="menu_<?= $m['id'] ?>" value="0" min="0" class="count-display" data-price="<?= $m['harga'] ?>" readonly>
                                    </td>
                                    <td>
                                        <div class="action-cell">
                                            <button type="button" class="action-btn btn-plus" onclick="updateCount('menu_<?= $m['id'] ?>', 1)">+</button>
                                            <button type="button" class="action-btn btn-minus" onclick="updateCount('menu_<?= $m['id'] ?>', -1)">-</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <h3>Krusty Krab's Merchandise</h3>
                <div class="img-grid merch-grid">
                    <div class="img-item"><img src="public/img/tshirt.jpg" alt="T-Shirt"></div>
                    <div class="img-item"><img src="public/img/shirt.jpg" alt="Shirt"></div>
                    <div class="img-item"><img src="public/img/hat.jpg" alt="Hat"></div>
                    <div class="img-item"><img src="public/img/key.webp" alt="Keychain"></div>
                    <div class="img-item"><img src="public/img/backpack.jpg" alt="Backpack"></div>
                </div>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>ID Merch</th>
                                <th>Name</th>
                                <th>Price</th>
                                <th>Count</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($merch_list)): ?>
                                <tr><td colspan="5">Tidak ada merchandise.</td></tr>
                            <?php else: ?>
                                <?php foreach ($merch_list as $m): ?>
                                <tr>
                                    <td><?= htmlspecialchars(sprintf("MC%03d", $m['id'])) ?></td>
                                    <td><?= htmlspecialchars($m['nama']) ?><br><small style="color:#777;"><?= htmlspecialchars($m['deskripsi']) ?></small></td>
                                    <td>Rp <?= number_format($m['harga'], 0, ',', '.') ?></td>
                                    <td>
                                        <input type="number" name="merch[<?= $m['id'] ?>][jumlah]" id="merch_<?= $m['id'] ?>" value="0" min="0" class="count-display" data-price="<?= $m['harga'] ?>" readonly>
                                    </td>
                                    <td>
                                        <div class="action-cell">
                                            <button type="button" class="action-btn btn-plus" onclick="updateCount('merch_<?= $m['id'] ?>', 1)">+</button>
                                            <button type="button" class="action-btn btn-minus" onclick="updateCount('merch_<?= $m['id'] ?>', -1)">-</button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="form-section">
                    <h3>Orders Data</h3>
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" value="<?= htmlspecialchars($_SESSION['username']) ?>" disabled style="background: #ddd; cursor: not-allowed; border: none;">
                    </div>
                    <div class="form-group">
                        <label>Notes (Catatan Pesanan)</label>
                        <textarea name="catatan" rows="3" placeholder="Masukkan catatan tambahan..."></textarea>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="dashboard_pelanggan.php" class="btn btn-cancel" style="display:inline-flex; align-items:center; text-decoration:none;">Cancel</a>
                        <button type="submit" name="pesan" class="btn btn-save">Save</button>
                    </div>
                </div>

            </form>
        </div>
    </div>

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

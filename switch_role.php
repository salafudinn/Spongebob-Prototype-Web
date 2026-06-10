<?php
require_once 'core/auth.php';
cekLogin();

if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    // Switch to pelanggan
    $_SESSION['role'] = 'pelanggan';
    header("Location: dashboard_pelanggan.php");
} else {
    // Switch to karyawan
    $_SESSION['role'] = 'karyawan';
    header("Location: dashboard_karyawan.php");
}
exit;
?>

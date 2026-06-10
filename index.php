<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'core/auth.php';
cekLogin();

if ($_SESSION['role'] === 'karyawan' || $_SESSION['role'] === 'admin') {
    header("Location: dashboard_karyawan.php");
} else {
    header("Location: home.php");
}
exit;
?>
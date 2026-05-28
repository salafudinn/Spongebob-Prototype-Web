<?php
require_once __DIR__ . '/auth.php';
cekLogin();
require_once __DIR__ . '/functions.php';

if (isset($_POST['tambah'])) {
    if (tambahKarakter($_POST['nama'], $_POST['pekerjaan'], $_POST['deskripsi'])) {
        echo "<script>alert('Karakter warga Bikini Bottom berhasil ditambahkan!'); window.location='../index.php';</script>";
    } else {
        echo "<script>alert('Gagal menambah data.'); window.location='../index.php';</script>";
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    if (hapusKarakter($_GET['id'])) {
        echo "<script>alert('Karakter berhasil dihapus dari Bikini Bottom!'); window.location='../index.php';</script>";
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='../index.php';</script>";
    }
}
?>
<?php
require_once 'core/auth.php';
cekLogin();
require_once 'core/functions.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id    = $_GET['id'];
$warga = getKarakterById($id);

if (!$warga) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='index.php';</script>";
    exit;
}

if (isset($_POST['ubah'])) {
    if (ubahKarakter($id, $_POST['nama'], $_POST['pekerjaan'], $_POST['deskripsi'])) {
        echo "<script>alert('Data berhasil diperbarui!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal memperbarui data.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Edit Warga Bikini Bottom</title>
</head>
<body>
    <h2>Edit Data: <?= $warga['nama'] ?></h2>
    <a href="index.php">Kembali ke Dashboard</a>
    <hr>

    <form action="" method="POST">
        <label>Nama Karakter:</label><br>
        <input type="text" name="nama" value="<?= $warga['nama'] ?>" required><br><br>

        <label>Pekerjaan:</label><br>
        <input type="text" name="pekerjaan" value="<?= $warga['pekerjaan'] ?>" required><br><br>

        <label>Deskripsi/Hobi:</label><br>
        <textarea name="deskripsi" required><?= $warga['deskripsi'] ?></textarea><br><br>

        <button type="submit" name="ubah">Simpan Perubahan</button>
    </form>
</body>
</html>
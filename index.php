<?php
require_once 'core/auth.php';
cekLogin();
require_once 'core/functions.php';

if (isset($_POST['tambah'])) {
    if (tambahKarakter($_POST['nama'], $_POST['pekerjaan'], $_POST['deskripsi'])) {
        echo "<script>alert('Karakter warga Bikini Bottom berhasil ditambahkan!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menambah data.'); window.location='index.php';</script>";
        exit;
    }
}

if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus') {
    if (hapusKarakter($_GET['id'])) {
        echo "<script>alert('Karakter berhasil dihapus dari Bikini Bottom!'); window.location='index.php';</script>";
        exit;
    } else {
        echo "<script>alert('Gagal menghapus data.'); window.location='index.php';</script>";
        exit;
    }
}

$warga_bikini = getAllData('karakter');
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <title>Dashboard Bikini Bottom</title>
</head>
<body>
    <h2>Selamat Datang, <?= $_SESSION['username']; ?>! (Role: <?= $_SESSION['role']; ?>)</h2>
    <a href="logout.php" onclick="return confirm('Yakin ingin keluar?')">Logout dari Sistem</a>
    <hr>

    <h3>Tambah Warga Baru Bikini Bottom (Create)</h3>
    <form action="index.php" method="POST">
        <label>Nama Karakter:</label><br>
        <input type="text" name="nama" required><br><br>

        <label>Pekerjaan:</label><br>
        <input type="text" name="pekerjaan" required><br><br>

        <label>Deskripsi/Hobi:</label><br>
        <textarea name="deskripsi" required></textarea><br><br>

        <button type="submit" name="tambah">Simpan Karakter</button>
    </form>

    <hr>

    <h3>Daftar Warga Saat Ini (Read & Delete)</h3>
    <table border="1" cellpadding="10" cellspacing="0">
        <thead>
            <tr>
                <th>No</th>
                <th>Nama</th>
                <th>Pekerjaan</th>
                <th>Deskripsi</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($warga_bikini)): ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Belum ada data warga. Sila tambah di atas!</td>
                </tr>
            <?php else: ?>
                <?php $no = 1; foreach ($warga_bikini as $warga): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $warga['nama'] ?></td>
                    <td><?= $warga['pekerjaan'] ?></td>
                    <td><?= $warga['deskripsi'] ?></td>
                    <td>
                        <a href="index.php?aksi=hapus&id=<?= $warga['id'] ?>"
                           onclick="return confirm('Yakin ingin menghapus <?= $warga['nama'] ?>?')">Hapus</a>
                        |
                        <a href="edit.php?id=<?= $warga['id'] ?>">Edit</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
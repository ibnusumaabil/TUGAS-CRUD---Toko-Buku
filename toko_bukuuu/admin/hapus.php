<?php
session_start();
require_once '../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    // 1. Ambil data nama gambar terlebih dahulu
    $stmt = $pdo->prepare("SELECT nama, gambar FROM produk WHERE id = ?");
    $stmt->execute([$id]);
    $buku = $stmt->fetch();

    if ($buku) {
        // 2. Hapus file gambar dari folder uploads/produk/
        if (!empty($buku['gambar']) && file_exists('../uploads/produk/' . $buku['gambar'])) {
            unlink('../uploads/produk/' . $buku['gambar']);
        }

        // 3. Hapus data dari database MySQL
        $delete = $pdo->prepare("SELECT COUNT(*) FROM produk WHERE id = ?");
        $delete_stmt = $pdo->prepare("DELETE FROM produk WHERE id = ?");
        $delete_stmt->execute([$id]);

        $_SESSION['flash'] = "Buku <strong>" . htmlspecialchars($buku['nama']) . "</strong> berhasil dihapus!";
    } else {
        $_SESSION['flash'] = "Data buku tidak ditemukan!";
    }
}

header('Location: produk.php');
exit;
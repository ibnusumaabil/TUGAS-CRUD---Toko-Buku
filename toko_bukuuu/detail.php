<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku) {
    header('Location: index.php');
    exit;
}

// Format nomor WhatsApp admin (Ganti dengan nomor kamu jika perlu)
$no_wa = "6281234567890";
$pesan_wa = urlencode("Halo Candu Buku, saya mau pesan buku: " . $buku['nama']);
$link_wa = "https://wa.me/{$no_wa}?text={$pesan_wa}";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($buku['nama']) ?> - Candu Buku</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background: #f8f9fa; color: #333; }
        header { background: #1a252f; padding: 15px 0; }
        .nav-container { max-width: 1000px; margin: 0 auto; padding: 0 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #f39c12; text-decoration: none; }
        
        .container { max-width: 1000px; margin: 40px auto; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 4px 10px rgba(0,0,0,0.05); display: flex; gap: 40px; flex-wrap: wrap; }
        .book-cover { width: 300px; border-radius: 8px; object-fit: cover; box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
        .book-details { flex: 1; min-width: 280px; }
        .category { background: #e67e22; color: white; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: bold; display: inline-block; margin-bottom: 10px; }
        h1 { font-size: 28px; color: #2c3e50; margin-bottom: 15px; }
        .price { font-size: 26px; color: #27ae60; font-weight: bold; margin-bottom: 15px; }
        .stock { font-weight: bold; color: #7f8c8d; margin-bottom: 20px; }
        .desc { line-height: 1.6; color: #555; margin-bottom: 30px; background: #f9f9f9; padding: 15px; border-radius: 6px; border-left: 4px solid #3498db; }
        
        .action-buttons { display: flex; gap: 10px; flex-wrap: wrap; }
        .btn-wa { background-color: #25D366; color: white; text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: bold; display: inline-block; transition: background 0.2s; }
        .btn-wa:hover { background-color: #1ebc57; }
        .btn-back { background-color: #7f8c8d; color: white; text-decoration: none; padding: 12px 20px; border-radius: 5px; font-weight: bold; display: inline-block; transition: background 0.2s; }
        .btn-back:hover { background-color: #6c757d; }
    </style>
</head>
<body>

<header>
    <div class="nav-container">
        <a href="index.php" class="logo">📚 Candu Buku</a>
    </div>
</header>

<div class="container">
    <div>
        <?php if (!empty($buku['gambar']) && file_exists('uploads/produk/' . $buku['gambar'])): ?>
            <img src="uploads/produk/<?= htmlspecialchars($buku['gambar']) ?>" class="book-cover" alt="Cover Buku">
        <?php else: ?>
            <div style="width:300px; height:400px; background:#eee; display:flex; align-items:center; justify-content:center; color:#888; border-radius:8px;">Tidak Ada Cover</div>
        <?php endif; ?>
    </div>
    
    <div class="book-details">
        <span class="category"><?= htmlspecialchars($buku['kategori']) ?></span>
        <h1><?= htmlspecialchars($buku['nama']) ?></h1>
        <div class="price">Rp <?= number_format($buku['harga'], 0, ',', '.') ?></div>
        <div class="stock">Tersedia Stok: <?= $buku['stok'] ?> pcs</div>
        
        <div class="desc">
            <strong>Deskripsi Buku:</strong><br><br>
            <?= nl2br(htmlspecialchars($buku['deskripsi'])) ?>
        </div>
        
        <div class="action-buttons">
            <a href="<?= $link_wa ?>" target="_blank" class="btn-wa">📲 Beli via WhatsApp</a>
            <a href="index.php" class="btn-back">← Kembali ke Katalog</a>
        </div>
    </div>
</div>

</body>
</html>
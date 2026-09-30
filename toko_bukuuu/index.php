<?php
require_once 'config/database.php';

// Ambil Filter Kategori & Pencarian jika ada
$keyword  = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

$sql = "SELECT * FROM produk WHERE 1=1";
$params = [];

if ($keyword !== '') {
    $sql .= " AND nama LIKE ?";
    $params[] = "%$keyword%";
}
if ($kategori !== '') {
    $sql .= " AND kategori = ?";
    $params[] = $kategori;
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$buku_list = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Candu Buku - Investasi Leher ke Atas</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 0; }
        body { background-color: #f8f9fa; color: #333; }
        header { background: #1a252f; color: white; padding: 15px 0; }
        .nav-container { max-width: 1100px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #f39c12; text-decoration: none; }
        nav a { color: white; text-decoration: none; margin-left: 20px; font-weight: 500; }
        nav a:hover { color: #f39c12; }
        
        /* Hero Section Copywriting */
        .hero { background: linear-gradient(135deg, #2c3e50, #1a252f); color: white; text-align: center; padding: 50px 20px; }
        .hero h1 { font-size: 36px; margin-bottom: 10px; color: #f1c40f; }
        .hero p { font-size: 18px; max-width: 700px; margin: 0 auto 15px auto; opacity: 0.9; }
        .hero .highlight { background: #e74c3c; padding: 4px 10px; border-radius: 4px; font-weight: bold; display: inline-block; }

        .container { max-width: 1100px; margin: 30px auto; padding: 0 20px; }
        .search-box { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 30px; display: flex; gap: 10px; flex-wrap: wrap; }
        .search-box input, .search-box select { padding: 10px; border: 1px solid #ccc; border-radius: 5px; flex: 1; }
        .btn-search { background: #27ae60; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; font-weight: bold; }
        
        /* Grid Katalog Buku */
        .book-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 25px; }
        .book-card { background: white; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 8px rgba(0,0,0,0.1); transition: transform 0.2s; display: flex; flex-direction: column; justify-content: space-between; }
        .book-card:hover { transform: translateY(-5px); }
        .book-card img { width: 100%; height: 260px; object-fit: cover; }
        .book-info { padding: 15px; }
        .book-category { font-size: 12px; color: #e67e22; font-weight: bold; text-transform: uppercase; }
        .book-title { font-size: 16px; margin: 5px 0 10px 0; font-weight: bold; color: #2c3e50; }
        .book-price { font-size: 18px; color: #27ae60; font-weight: bold; margin-bottom: 15px; }
        .btn-detail { display: block; text-align: center; background: #3498db; color: white; text-decoration: none; padding: 8px; border-radius: 4px; font-weight: bold; }
        footer { text-align: center; padding: 20px; background: #1a252f; color: white; margin-top: 50px; }
    </style>
</head>
<body>

<header>
    <div class="nav-container">
        <a href="index.php" class="logo">📚 Candu Buku</a>
        <nav>
            <a href="index.php">Katalog Buku</a>
            <a href="tentang.php">Tentang Kami</a>
            <a href="admin/produk.php" style="color: #f1c40f;">[ Panel Admin ]</a>
        </nav>
    </div>
</header>

<section class="hero">
    <h1>Investasi Terbaik Adalah Investasi Leher ke Atas.</h1>
    <p>"Mau sekritis Rocky Gerung? Beli dan baca satu buku hari ini biar IQ lu naik!"</p>
    <div class="highlight">🔥 Koleksi Buku Best Seller & Filsafat Pilihan</div>
</section>

<div class="container">
    <!-- Filter Form -->
    <form method="GET" action="index.php" class="search-box">
        <input type="text" name="keyword" placeholder="Cari judul buku favoritmu..." value="<?= htmlspecialchars($keyword) ?>">
        <select name="kategori">
            <option value="">-- Semua Kategori --</option>
            <option value="Self Development" <?= $kategori == 'Self Development' ? 'selected' : '' ?>>Self Development</option>
            <option value="Komunikasi" <?= $kategori == 'Komunikasi' ? 'selected' : '' ?>>Komunikasi</option>
            <option value="Filsafat" <?= $kategori == 'Filsafat' ? 'selected' : '' ?>>Filsafat</option>
            <option value="Biografi" <?= $kategori == 'Biografi' ? 'selected' : '' ?>>Biografi</option>
        </select>
        <button type="submit" class="btn-search">Cari Buku</button>
    </form>

    <!-- Grid Produk -->
    <div class="book-grid">
        <?php if (count($buku_list) > 0): ?>
            <?php foreach ($buku_list as $buku): ?>
                <div class="book-card">
                    <div>
                        <?php if (!empty($buku['gambar']) && file_exists('uploads/produk/' . $buku['gambar'])): ?>
                            <img src="uploads/produk/<?= htmlspecialchars($buku['gambar']) ?>" alt="Cover">
                        <?php else: ?>
                            <div style="height: 260px; background: #eee; display: flex; align-items: center; justify-content: center; color: #888;">No Cover</div>
                        <?php endif; ?>
                        <div class="book-info">
                            <span class="book-category"><?= htmlspecialchars($buku['kategori']) ?></span>
                            <div class="book-title"><?= htmlspecialchars($buku['nama']) ?></div>
                        </div>
                    </div>
                    <div class="book-info" style="padding-top: 0;">
                        <div class="book-price">Rp <?= number_format($buku['harga'], 0, ',', '.') ?></div>
                        <a href="detail.php?id=<?= $buku['id'] ?>" class="btn-detail">Lihat Detail</a>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="grid-column: 1/-1; text-align: center; color: #777;">Buku yang kamu cari tidak ditemukan.</p>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Candu Buku. All rights reserved.</p>
</footer>

</body>
</html>
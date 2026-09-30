<?php
session_start();
require_once '../config/database.php';

// 1. Konfigurasi Paginasi (Pagination)
$per_page = 5; // Jumlah data per halaman
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $per_page;

// 2. Filter & Pencarian
$keyword  = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$kategori = isset($_GET['kategori']) ? trim($_GET['kategori']) : '';

// Menyusun Query SQL Dasar
$sql_where = " WHERE 1=1";
$params = [];

if ($keyword !== '') {
    $sql_where .= " AND nama LIKE ?";
    $params[] = "%$keyword%";
}

if ($kategori !== '') {
    $sql_where .= " AND kategori = ?";
    $params[] = $kategori;
}

// 3. Hitung Total Data (Untuk Paginasi)
$stmt_count = $pdo->prepare("SELECT COUNT(*) FROM produk" . $sql_where);
$stmt_count->execute($params);
$total_data = $stmt_count->fetchColumn();
$total_pages = ceil($total_data / $per_page);

// 4. Ambil Data Produk Berdasarkan LIMIT & OFFSET
$sql_data = "SELECT * FROM produk" . $sql_where . " ORDER BY id DESC LIMIT ? OFFSET ?";
$stmt = $pdo->prepare($sql_data);

// Bind parameter pencarian
$param_index = 1;
foreach ($params as $param) {
    $stmt->bindValue($param_index++, $param);
}
// Bind parameter LIMIT dan OFFSET sebagai INTEGER
$stmt->bindValue($param_index++, $per_page, PDO::PARAM_INT);
$stmt->bindValue($param_index++, $offset, PDO::PARAM_INT);
$stmt->execute();
$produk_list = $stmt->fetchAll();

// Memanggil Tampilan Header
require_once '../includes/header.php';
?>

<h2>Dashboard Admin - Kelola Produk Buku</h2>

<!-- Notifikasi Flash Message -->
<?php require_once '../includes/flash.php'; ?>

<!-- Tombol Tambah Buku -->
<a href="tambah.php" class="btn btn-primary">+ Tambah Buku Baru</a>

<!-- Form Pencarian & Filter Kategori -->
<form method="GET" action="produk.php" class="filter-box">
    <input type="text" name="keyword" placeholder="Cari judul buku..." value="<?= htmlspecialchars($keyword) ?>">
    <select name="kategori">
        <option value="">-- Semua Kategori --</option>
        <option value="Self Development" <?= $kategori == 'Self Development' ? 'selected' : '' ?>>Self Development</option>
        <option value="Komunikasi" <?= $kategori == 'Komunikasi' ? 'selected' : '' ?>>Komunikasi</option>
        <option value="Filsafat" <?= $kategori == 'Filsafat' ? 'selected' : '' ?>>Filsafat</option>
        <option value="Biografi" <?= $kategori == 'Biografi' ? 'selected' : '' ?>>Biografi</option>
    </select>
    <button type="submit" class="btn btn-secondary" style="margin-bottom:0;">Cari / Filter</button>
    <a href="produk.php" class="btn btn-secondary" style="margin-bottom:0; background-color:#17a2b8;">Reset</a>
</form>

<!-- Tabel Tampil Data Buku (Read) -->
<table>
    <thead>
        <tr>
            <th>No</th>
            <th>Cover</th>
            <th>Judul Buku</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (count($produk_list) > 0): ?>
            <?php 
            $no = $offset + 1;
            foreach ($produk_list as $row): 
            ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <?php if (!empty($row['gambar']) && file_exists('../uploads/produk/' . $row['gambar'])): ?>
                            <img src="../uploads/produk/<?= htmlspecialchars($row['gambar']) ?>" class="img-thumb" alt="Cover">
                        <?php else: ?>
                            <small style="color:red;">Tidak Ada Foto</small>
                        <?php endif; ?>
                    </td>
                    <td><strong><?= htmlspecialchars($row['nama']) ?></strong></td>
                    <td><?= htmlspecialchars($row['kategori']) ?></td>
                    <td>Rp <?= number_format($row['harga'], 0, ',', '.') ?></td>
                    <td><?= $row['stok'] ?> pcs</td>
                    <td>
                        <a href="edit.php?id=<?= $row['id'] ?>" class="btn btn-warning" style="padding:4px 8px; font-size:12px;">Edit</a>
                        <a href="hapus.php?id=<?= $row['id'] ?>" class="btn btn-danger" style="padding:4px 8px; font-size:12px;" onclick="return confirm('Yakin ingin menghapus buku ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada data buku yang ditemukan.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Navigasi Paginasi -->
<?php if ($total_pages > 1): ?>
    <div style="margin-top: 20px;">
        <span>Halaman: </span>
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="produk.php?page=<?= $i ?>&keyword=<?= urlencode($keyword) ?>&kategori=<?= urlencode($kategori) ?>" 
               style="padding: 5px 10px; margin-right: 5px; border: 1px solid #ccc; text-decoration: none; <?= $i == $page ? 'background:#007bff; color:white;' : '' ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>
    </div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
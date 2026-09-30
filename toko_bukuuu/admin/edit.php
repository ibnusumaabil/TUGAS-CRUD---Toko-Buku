<?php
session_start();
require_once '../config/database.php';

// Ambil ID dari URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Cek apakah data buku ada di database
$stmt = $pdo->prepare("SELECT * FROM produk WHERE id = ?");
$stmt->execute([$id]);
$buku = $stmt->fetch();

if (!$buku) {
    $_SESSION['flash'] = "Data buku tidak ditemukan!";
    header('Location: produk.php');
    exit;
}

$errors = [];
$nama      = $buku['nama'];
$kategori  = $buku['kategori'];
$deskripsi = $buku['deskripsi'];
$harga     = $buku['harga'];
$stok      = $buku['stok'];
$gambar_lama = $buku['gambar'];

// Proses Perubahan Data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga     = trim($_POST['harga'] ?? '');
    $stok      = trim($_POST['stok'] ?? '');

    // Validasi Input
    if (empty($nama)) $errors[] = "Judul buku wajib diisi!";
    if (empty($kategori)) $errors[] = "Kategori wajib dipilih!";
    if (!is_numeric($harga) || $harga < 0) $errors[] = "Harga harus berupa angka valid!";
    if (!is_numeric($stok) || $stok < 0) $errors[] = "Stok harus berupa angka valid!";

    $nama_file_gambar = $gambar_lama; // Default menggunakan gambar lama

    // Cek jika ada gambar baru yang diunggah
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['gambar'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png'];
        $maxSize  = 2 * 1024 * 1024; // 2MB

        if (!in_array($ext, $allowed)) $errors[] = "Format gambar harus JPG, JPEG, atau PNG!";
        if ($file['size'] > $maxSize) $errors[] = "Ukuran gambar maksimal 2MB!";

        if (empty($errors)) {
            $nama_file_gambar = uniqid('buku_') . '.' . $ext;
            $tujuan_upload    = '../uploads/produk/' . $nama_file_gambar;

            if (move_uploaded_file($file['tmp_name'], $tujuan_upload)) {
                // Hapus gambar lama jika ada dan file-nya eksis
                if (!empty($gambar_lama) && file_exists('../uploads/produk/' . $gambar_lama)) {
                    unlink('../uploads/produk/' . $gambar_lama);
                }
            } else {
                $errors[] = "Gagal mengunggah gambar baru!";
            }
        }
    }

    // Update Data ke Database
    if (empty($errors)) {
        try {
            $sql = "UPDATE produk SET nama = ?, kategori = ?, deskripsi = ?, harga = ?, stok = ?, gambar = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $kategori, $deskripsi, $harga, $stok, $nama_file_gambar, $id]);

            $_SESSION['flash'] = "Buku <strong>" . htmlspecialchars($nama) . "</strong> berhasil diperbarui!";
            header('Location: produk.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}

require_once '../includes/header.php';
?>

<h2>Edit Data Buku</h2>
<a href="produk.php" class="btn btn-secondary">← Batal & Kembali</a>

<?php if (!empty($errors)): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-top: 15px;">
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form action="edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data" style="max-width: 600px; margin-top: 15px;">
    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Judul Buku *</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Kategori *</label>
        <select name="kategori" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            <option value="Self Development" <?= $kategori == 'Self Development' ? 'selected' : '' ?>>Self Development</option>
            <option value="Komunikasi" <?= $kategori == 'Komunikasi' ? 'selected' : '' ?>>Komunikasi</option>
            <option value="Filsafat" <?= $kategori == 'Filsafat' ? 'selected' : '' ?>>Filsafat</option>
            <option value="Biografi" <?= $kategori == 'Biografi' ? 'selected' : '' ?>>Biografi</option>
        </select>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Deskripsi Singkat</label>
        <textarea name="deskripsi" rows="4" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;"><?= htmlspecialchars($deskripsi) ?></textarea>
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Harga (Rp) *</label>
        <input type="number" name="harga" value="<?= htmlspecialchars($harga) ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Jumlah Stok *</label>
        <input type="number" name="stok" value="<?= htmlspecialchars($stok) ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>

    <div style="margin-bottom: 20px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Cover Buku Saat Ini</label>
        <?php if (!empty($gambar_lama) && file_exists('../uploads/produk/' . $gambar_lama)): ?>
            <img src="../uploads/produk/<?= htmlspecialchars($gambar_lama) ?>" class="img-thumb" style="margin-bottom:10px;"><br>
        <?php endif; ?>
        <small style="color:#6c757d;">Biarkan kosong jika tidak ingin mengganti cover buku.</small>
        <input type="file" name="gambar" accept="image/*" style="width:100%; margin-top:5px;">
    </div>

    <button type="submit" class="btn btn-warning">Update Buku</button>
</form>

<?php require_once '../includes/footer.php'; ?>
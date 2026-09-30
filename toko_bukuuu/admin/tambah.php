<?php
session_start();
require_once '../config/database.php';

$errors = [];
$nama = $kategori = $deskripsi = $harga = $stok = '';

// Memproses Form saat tombol Submit diklik
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama      = trim($_POST['nama'] ?? '');
    $kategori  = trim($_POST['kategori'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $harga     = trim($_POST['harga'] ?? '');
    $stok      = trim($_POST['stok'] ?? '');
    
    // 1. Validasi Input (Server-Side)
    if (empty($nama)) {
        $errors[] = "Judul buku wajib diisi!";
    }
    if (empty($kategori)) {
        $errors[] = "Kategori buku wajib dipilih!";
    }
    if (!is_numeric($harga) || $harga < 0) {
        $errors[] = "Harga harus berupa angka yang valid!";
    }
    if (!is_numeric($stok) || $stok < 0) {
        $errors[] = "Stok harus berupa angka yang valid!";
    }

    // 2. Validasi Upload Gambar
    $nama_file_gambar = '';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $file     = $_FILES['gambar'];
        $ext      = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed  = ['jpg', 'jpeg', 'png'];
        $maxSize  = 2 * 1024 * 1024; // Maksimal 2MB

        if (!in_array($ext, $allowed)) {
            $errors[] = "Format gambar harus JPG, JPEG, atau PNG!";
        }
        if ($file['size'] > $maxSize) {
            $errors[] = "Ukuran gambar maksimal 2MB!";
        }

        if (empty($errors)) {
            // Generasi nama file unik agar tidak menimpa file lama
            $nama_file_gambar = uniqid('buku_') . '.' . $ext;
            $tujuan_upload    = '../uploads/produk/' . $nama_file_gambar;

            if (!move_uploaded_file($file['tmp_name'], $tujuan_upload)) {
                $errors[] = "Gagal mengunggah gambar ke server!";
            }
        }
    } else {
        $errors[] = "Cover buku wajib diunggah!";
    }

    // 3. Simpan ke Database jika tidak ada error
    if (empty($errors)) {
        try {
            $sql = "INSERT INTO produk (nama, kategori, deskripsi, harga, stok, gambar) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nama, $kategori, $deskripsi, $harga, $stok, $nama_file_gambar]);

            // Set Flash Message
            $_SESSION['flash'] = "Buku <strong>" . htmlspecialchars($nama) . "</strong> berhasil ditambahkan!";
            header('Location: produk.php');
            exit;
        } catch (PDOException $e) {
            $errors[] = "Terjadi kesalahan database: " . $e->getMessage();
        }
    }
}

require_once '../includes/header.php';
?>

<h2>Tambah Buku Baru</h2>
<a href="produk.php" class="btn btn-secondary">← Kembali ke Daftar Produk</a>

<!-- Tampilkan Pesan Error Validasi -->
<?php if (!empty($errors)): ?>
    <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 5px; margin-bottom: 20px;">
        <ul style="margin: 0; padding-left: 20px;">
            <?php foreach ($errors as $err): ?>
                <li><?= $err ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<!-- Form Tambah Produk (Wajib enctype="multipart/form-data" untuk upload file) -->
<form action="tambah.php" method="POST" enctype="multipart/form-data" style="max-width: 600px; margin-top: 15px;">
    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Judul Buku *</label>
        <input type="text" name="nama" value="<?= htmlspecialchars($nama) ?>" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
    </div>

    <div style="margin-bottom: 15px;">
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Kategori *</label>
        <select name="kategori" style="width:100%; padding:8px; border:1px solid #ccc; border-radius:4px;">
            <option value="">-- Pilih Kategori --</option>
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
        <label style="display:block; font-weight:bold; margin-bottom:5px;">Upload Cover Buku (JPG, JPEG, PNG, max 2MB) *</label>
        <input type="file" name="gambar" accept="image/*" style="width:100%;">
    </div>

    <button type="submit" class="btn btn-primary">Simpan Buku</button>
</form>

<?php require_once '../includes/footer.php'; ?>
<?php
// Konfigurasi Database
$host     = 'localhost';
$dbname   = 'toko_bukuuu'; // Menggunakan nama database kamu
$username = 'root';
$password = ''; // Kosongkan jika menggunakan XAMPP bawaan

try {
    // Membuat koneksi ke database MySQL menggunakan PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    // Set error mode ke Exception agar mudah mendeteksi eror
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
} catch (PDOException $e) {
    // Jika koneksi gagal, tampilkan pesan eror
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
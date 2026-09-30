<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Candu Buku</title>
    <style>
        * { box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f4f6f9; margin: 0; padding: 20px; }
        .container { max-width: 1100px; margin: 0 auto; background: #ffffff; padding: 25px; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        
        /* Navigasi Atas Header Admin */
        .admin-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 2px solid #e9ecef; }
        .admin-nav .btn-katalog { background-color: #17a2b8; color: white; text-decoration: none; padding: 8px 16px; border-radius: 5px; font-weight: bold; font-size: 14px; transition: background 0.2s; }
        .admin-nav .btn-katalog:hover { background-color: #138496; }

        h1, h2 { color: #333; margin: 0; }
        .btn { display: inline-block; padding: 8px 16px; text-decoration: none; border-radius: 4px; color: #fff; font-weight: bold; margin-bottom: 15px; }
        .btn-primary { background-color: #007bff; }
        .btn-warning { background-color: #ffc107; color: #212529; }
        .btn-danger { background-color: #dc3545; }
        .btn-secondary { background-color: #6c757d; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        table th, table td { border: 1px solid #dee2e6; padding: 12px; text-align: left; vertical-align: middle; }
        table th { background-color: #343a40; color: white; }
        table tr:nth-child(even) { background-color: #f8f9fa; }
        .img-thumb { width: 60px; height: 80px; object-fit: cover; border-radius: 4px; }
        .filter-box { background: #e9ecef; padding: 15px; border-radius: 6px; margin-bottom: 20px; display: flex; gap: 10px; flex-wrap: wrap; }
        .filter-box input, .filter-box select { padding: 8px; border: 1px solid #ced4da; border-radius: 4px; }
    </style>
</head>
<body>
<div class="container">
    <!-- Header Navigasi Admin -->
    <div class="admin-nav">
        <h2>Panel Pengelola Admin</h2>
        <!-- Tombol untuk Kembali ke Halaman Utama Pelanggan -->
        <a href="../index.php" class="btn-katalog">🌐 Lihat Website Pelanggan</a>
    </div>
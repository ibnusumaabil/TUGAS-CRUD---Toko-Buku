<?php
// Mencegah session dipanggil ganda
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Menampilkan notifikasi jika ada pesan flash
if (isset($_SESSION['flash'])) {
    echo '<div style="background-color: #d4edda; color: #155724; padding: 12px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">' 
          . $_SESSION['flash'] . 
         '</div>';
    unset($_SESSION['flash']); // Hapus setelah ditampilkan agar tidak muncul lagi
}
?>
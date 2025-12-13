<?php
// File: public/index.php

// 1. Panggil Controller
// Pastikan path ini sesuai dengan struktur foldermu
require_once __DIR__ . '/../app/controllers/ConsultationController.php';

// 2. Inisialisasi Controller
$controller = new ConsultationController();

// 3. Routing Sederhana (Menentukan halaman mana yang dibuka)
$page = $_GET['page'] ?? 'home';

if ($page === 'history') {
    // Tampilkan Halaman Riwayat
    $controller->history();
} 
elseif ($page === 'delete') {
    // Proses Hapus Data
    $id = $_GET['id'] ?? 0;
    $controller->delete($id);
} 
else {
    // DEFAULT: Halaman Utama (Diagnosa)
    // Fungsi index() di Controller sudah otomatis mengatur:
    // - Jika dibuka biasa -> Tampil Form
    // - Jika tombol ditekan (POST) -> Proses CF & Tampil Hasil
    $controller->index();
}
?>
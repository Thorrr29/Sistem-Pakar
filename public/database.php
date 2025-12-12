<?php
// db.php - File Khusus Koneksi Database

$host = '127.0.0.1';
$port = '5432';
$db   = 'sistempakar';
$user = 'postgres';
$pass = ''; // Password default Laragon

try {
    // String koneksi (DSN)
    $dsn = "pgsql:host=$host;port=$port;dbname=$db";
    
    // Membuat objek PDO
    $pdo = new PDO($dsn, $user, $pass);
    
    // Setting agar error muncul jika ada masalah query
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // KONEKSI SUKSES: Tidak perlu echo apa-apa di sini agar bersih

} catch (PDOException $e) {
    // Jika gagal, matikan proses dan tampilkan error
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>
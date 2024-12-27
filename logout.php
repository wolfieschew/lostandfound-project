<?php
// Sertakan remember_token.php untuk memeriksa dan mengaktifkan sesi dari cookie jika ada
include 'remember_token.php';

$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root';
$pass = '';

// Buat koneksi PDO ke database
$pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Jika ada sesi user, hapus token remember me di database
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Hapus semua token remember untuk user ini
    $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
}

// Hapus semua data sesi
session_unset();
session_destroy();

// Hapus cookie remember_token dengan cara meng-set waktu kadaluarsa ke masa lalu
setcookie('remember_token', '', time() - 3600, '/', '', false, true);

// Redirect ke halaman login
header("Location: log_in.php");
exit;

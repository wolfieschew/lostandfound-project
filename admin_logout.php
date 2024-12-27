<?php
session_start();

$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Jika user memiliki sesi aktif, hapus token dari database
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        // Hapus semua token remember me untuk user ini
        $stmt = $pdo->prepare("DELETE FROM remember_tokens WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $user_id]);
    }

    // Hapus sesi
    session_unset();
    session_destroy();

    // Hapus cookie remember_token
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);

    // Redirect ke halaman login
    header("Location: log_in.php");
    exit;
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

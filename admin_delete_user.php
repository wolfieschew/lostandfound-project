<?php
include 'remember_token.php';

// Pastikan pengguna sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: log_in.php");
    exit;
}

// Koneksi ke database
$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = filter_input(INPUT_POST, 'user_id', FILTER_VALIDATE_INT);

        // Validasi ID user
        if (!$userId) {
            header("Location: admin_users.php?error=" . urlencode("Invalid user ID."));
            exit;
        }

        // Hapus user dari database
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: admin_users.php?message=" . urlencode("User successfully deleted."));
            exit;
        } else {
            header("Location: admin_users.php?error=" . urlencode("Failed to delete the user."));
            exit;
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

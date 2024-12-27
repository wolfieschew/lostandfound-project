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
        $itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);

        // Validasi ID
        if (!$itemId) {
            header("Location: admin_items_lost.php?error=" . urlencode("Invalid item ID."));
            exit;
        }

        // Query untuk menghapus item berdasarkan ID
        $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
        $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redirect ke halaman sebelumnya dengan pesan sukses
            $redirectUrl = $_POST['redirect_url'] ?? 'admin_items_lost.php';
            header("Location: $redirectUrl?message=" . urlencode("Item successfully deleted."));
            exit;
        } else {
            $redirectUrl = $_POST['redirect_url'] ?? 'admin_items_lost.php';
            header("Location: $redirectUrl?error=" . urlencode("Failed to delete the item."));
            exit;
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

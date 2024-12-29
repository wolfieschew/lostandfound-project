<?php
include 'remember_token.php';

// Pastikan permintaan menggunakan POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = 'localhost';
    $db = 'lost_and_found_items';
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Siapkan query update tanpa mengubah kolom `type`
        $stmt = $pdo->prepare("UPDATE items SET item_name = :item_name, category = :category, description = :description WHERE id = :id");

        // Bind data dari form
        $stmt->bindParam(':id', $_POST['id'], PDO::PARAM_INT);
        $stmt->bindParam(':item_name', $_POST['item_name']);
        $stmt->bindParam(':category', $_POST['category']);
        $stmt->bindParam(':description', $_POST['description']);

        // Eksekusi query
        if ($stmt->execute()) {
            $redirectUrl = $_POST['redirect_url'] ?? 'admin_items_found.php';
            header("Location: $redirectUrl?message=" . urlencode("Item successfully updated."));
            exit;
        } else {
            $redirectUrl = $_POST['redirect_url'] ?? 'admin_items_found.php';
            header("Location: $redirectUrl?error=" . urlencode("Failed to update the item."));
            exit;
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

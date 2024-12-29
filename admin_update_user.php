<?php
include 'remember_token.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: log_in.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $host = 'localhost';
    $db = 'lost_and_found_items';
    $user = 'root';
    $pass = '';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $userId = $_POST['id'];
        $role = $_POST['role'];

        // Validasi role
        if (!in_array($role, ['user', 'admin'])) {
            header("Location: admin_users.php?error=" . urlencode("Invalid role."));
            exit;
        }

        // Update role user
        $stmt = $pdo->prepare("UPDATE users SET role = :role WHERE id = :id");
        $stmt->bindParam(':role', $role, PDO::PARAM_STR);
        $stmt->bindParam(':id', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: admin_users.php?message=" . urlencode("User role updated successfully."));
        } else {
            header("Location: admin_users.php?error=" . urlencode("Failed to update user role."));
        }
        exit;
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header("Location: admin_users.php");
    exit;
}

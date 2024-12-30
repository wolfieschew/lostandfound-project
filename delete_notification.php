<?php
// Sertakan remember_token.php dan pastikan pengguna login
include 'remember_token.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    http_response_code(401); // Unauthorized
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'lost_and_found_items';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pastikan ada ID notifikasi
if (isset($_POST['notification_id'])) {
    $notificationId = intval($_POST['notification_id']);

    // Query untuk menghapus notifikasi
    $sql = "DELETE FROM notifications WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $notificationId);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to delete notification']);
    }

    $stmt->close();
} else {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Notification ID is required']);
}

$conn->close();

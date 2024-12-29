<?php
require_once('db_connection.php');

// Ambil data dari form
$id = $_POST['id'];
$category = $_POST['category'];
$type = $_POST['type'];
$date_of_event = $_POST['date_of_event'];
$description = $_POST['description'];

// Query untuk memperbarui data
$sql = "UPDATE items 
        SET category = ?, type = ?, date_of_event = ?, description = ? 
        WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssssi", $category, $type, $date_of_event, $description, $id);

if ($stmt->execute()) {
    // Redirect kembali ke halaman activity
    header("Location: activity.php?success=update");
    exit();
} else {
    echo "Gagal memperbarui data: " . $conn->error;
}

$stmt->close();
$conn->close();

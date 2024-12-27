<?php
// Mulai sesi untuk mendapatkan informasi user
session_start();

var_dump($_POST); // Debugging: Mencetak semua data yang diterima dari form

// Pastikan pengguna sudah login dan memiliki user_id dalam sesi
if (!isset($_SESSION['user_id'])) {
    die("User ID is not set. Please log in.");
}
$user_id = $_SESSION['user_id']; // Mendapatkan user_id dari session

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'lost_and_found_items';
$conn = new mysqli($host, $user, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Ambil data dari form
$type = $_POST['type'];  // 'lost' atau 'found'
$item_name = $_POST['item_name'];
$category = $_POST['category'];
$date_of_event = $_POST['date_of_loss'];
$description = $_POST['description'];
$email = $_POST['email'];
$phone_number = $_POST['phone_number'];
$location = $_POST['location'];

// Proses upload gambar
$photo_path = '';
if (isset($_FILES["photo"]) && $_FILES["photo"]["error"] == 0) {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["photo"]["name"]);
    if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
        $photo_path = $target_file;
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

// Debugging untuk memastikan data diterima
echo "Data Type: " . $type . "<br>";

// Query untuk menyimpan data ke tabel `items`
$sql = "INSERT INTO items (type, item_name, category, date_of_event, description, email, phone_number, location, photo_path, user_id)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssssssi", $type, $item_name, $category, $date_of_event, $description, $email, $phone_number, $location, $photo_path, $user_id);

if ($stmt->execute()) {
    // Jika berhasil disimpan ke tabel `items`, tambahkan notifikasi
    $notificationMessage = "$first_name melaporkan barang baru: $item_name.";
    $notif_sql = "INSERT INTO notifications (user_id, message) VALUES (?, ?)";
    $notif_stmt = $conn->prepare($notif_sql);
    $notif_stmt->bind_param("is", $user_id, $notificationMessage);
    $notif_stmt->execute();
    $notif_stmt->close();

    // Redirect ke dashboard
    header("Location: user_dashboard.php");
    exit;
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

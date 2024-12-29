<?php
header("Content-Type: application/json");

$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root'; // Sesuaikan dengan konfigurasi Anda
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil data dari request
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];

    // Cek apakah email ada di database
    $stmt = $pdo->prepare("SELECT email FROM users WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Email not found"]);
    }
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

<?php
header("Content-Type: application/json");

$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root'; // Ganti dengan konfigurasi Anda
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ambil data dari permintaan
    $data = json_decode(file_get_contents("php://input"), true);
    $email = $data['email'];
    $password = password_hash($data['password'], PASSWORD_BCRYPT); // Enkripsi kata sandi

    // Update password di database
    $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE email = :email");
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    echo json_encode(["success" => true]);
} catch (PDOException $e) {
    echo json_encode(["success" => false, "error" => $e->getMessage()]);
}

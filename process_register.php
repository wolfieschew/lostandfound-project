<?php
session_start();
$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone = $_POST['phone'];

        if ($password !== $confirm_password) {
            echo "Password dan Konfirmasi Password tidak cocok!";
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "Email sudah terdaftar!";
            exit;
        }

        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin'");
        $stmt->execute();
        $adminExists = $stmt->rowCount() > 0;

        $role = $adminExists ? 'user' : 'admin';
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $pdo->prepare("INSERT INTO users (email, password, role, first_name, last_name, phone) VALUES (:email, :password, :role, :first_name, :last_name, :phone)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone', $phone);

        if ($stmt->execute()) {
            echo "Pendaftaran berhasil!";
        } else {
            echo "Terjadi kesalahan saat mendaftar!";
        }
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

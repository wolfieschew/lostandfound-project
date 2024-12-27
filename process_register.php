<?php
session_start();
$host = 'localhost'; // Sesuaikan dengan host Anda
$db = 'lost_and_found_items'; // Nama database Anda
$user = 'root'; // Username database Anda
$pass = ''; // Password database Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Ambil data dari form
        $email = $_POST['email'];
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $phone = $_POST['phone'];

        // Validasi password dan konfirmasi password
        if ($password !== $confirm_password) {
            echo "Password dan Konfirmasi Password tidak cocok!";
            exit;
        }

        // Cek apakah email sudah terdaftar
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            echo "Email sudah terdaftar!";
            exit;
        }

        // Cek apakah sudah ada admin di database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE role = 'admin'");
        $stmt->execute();
        $adminExists = $stmt->rowCount() > 0;

        // Tentukan role: jika sudah ada admin, set role menjadi buyer
        $role = $adminExists ? 'user' : 'admin';

        // Enkripsi password
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        // Query untuk memasukkan data pengguna ke dalam tabel users
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role, first_name, last_name, phone) VALUES (:email, :password, :role, :first_name, :last_name, :phone)");
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $hashed_password);
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':first_name', $first_name);
        $stmt->bindParam(':last_name', $last_name);
        $stmt->bindParam(':phone', $phone);

        // Eksekusi query
        if ($stmt->execute()) {
            echo "Pendaftaran berhasil!";
            // Redirect ke halaman login setelah berhasil
            header("Location: log_in.html");
            exit;
        } else {
            echo "Terjadi kesalahan saat mendaftar!";
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

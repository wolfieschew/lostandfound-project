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

        // Ambil user berdasarkan email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Validasi user dan password
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            if (isset($_POST['remember_me'])) {
                $token = bin2hex(random_bytes(16));
                $expires = date('Y-m-d H:i:s', time() + (86400 * 30)); // Berlaku 30 hari

                $stmt = $pdo->prepare("INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
                $stmt->execute([
                    'user_id' => $user['id'],
                    'token' => $token,
                    'expires_at' => $expires
                ]);

                setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
            }

            // Redirect sesuai role
            if ($user['role'] === 'admin') {
                header("Location: admin_dashboard.php");
            } elseif ($user['role'] === 'user') {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            // Simpan pesan kesalahan ke sesi
            $_SESSION['error'] = "Email atau password salah!";
            header("Location: log_in.php");
            exit;
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

<?php
session_start();

// Jika user belum login tapi ada cookie remember_token
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    $token = $_COOKIE['remember_token'];

    $host = 'localhost';
    $db = 'lost_and_found_items';
    $user = 'root';
    $pass = '';

    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cek token di database
    $stmt = $pdo->prepare("SELECT user_id, expires_at FROM remember_tokens WHERE token = :token LIMIT 1");
    $stmt->execute(['token' => $token]);
    $remember = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($remember && strtotime($remember['expires_at']) > time()) {
        // Token valid dan belum expired
        // Dapatkan data user
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $remember['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Set sesi
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // (Opsional) Perbarui token agar tidak statis
            $newToken = bin2hex(random_bytes(16));
            $newExpires = date('Y-m-d H:i:s', time() + (86400 * 30));

            $stmt = $pdo->prepare("UPDATE remember_tokens SET token = :new_token, expires_at = :new_expires WHERE token = :old_token");
            $stmt->execute([
                'new_token' => $newToken,
                'new_expires' => $newExpires,
                'old_token' => $token
            ]);

            // Update cookie dengan token baru
            setcookie(
                'remember_token',
                $newToken,
                time() + (86400 * 30),
                '/',
                '',
                false, // true jika HTTPS
                true    // httponly
            );
        }
    }
}

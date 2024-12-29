<?php
include 'remember_token.php';

// Pastikan pengguna sudah login dan memiliki role admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: log_in.php");
  exit;
}

// Koneksi ke database
$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root';
$pass = '';

try {
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Query untuk mendapatkan data
  // Hitung jumlah item hilang
  $stmt = $pdo->query("SELECT COUNT(*) AS total_lost FROM items WHERE type = 'hilang'");
  $totalLostItems = $stmt->fetch(PDO::FETCH_ASSOC)['total_lost'];

  // Hitung jumlah item ditemukan
  $stmt = $pdo->query("SELECT COUNT(*) AS total_found FROM items WHERE type = 'ditemukan'");
  $totalFoundItems = $stmt->fetch(PDO::FETCH_ASSOC)['total_found'];

  // Total pengguna
  $stmt = $pdo->query("SELECT COUNT(*) AS total_users FROM users");
  $totalUsers = $stmt->fetch(PDO::FETCH_ASSOC)['total_users'];


  // Dapatkan aktivitas terbaru
  $recentActivity = $pdo->query("SELECT id, item_name, type, created_at FROM items ORDER BY created_at DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
  $recentUsers = $pdo->query("SELECT id, email, first_name, last_name, role FROM users ORDER BY id DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Lost and Found</title>
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Konfigurasi -->
  <script>
    // Mencegah pengguna kembali ke halaman sebelumnya
    history.pushState(null, null, location.href);
    window.onpopstate = function() {
      history.go(1);
    };
  </script>
  <!-- BX BX ICONS -->
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">
  <!-- Sidebar -->
  <div class="flex h-screen">
    <div class="w-64 bg-[#124076] text-white">
      <img
        class="h-[5rem] m-auto mt-[1rem]"
        src="Assets/img/lostnfoundlogowhite.png" />
      <ul class="mt-6 space-y-2">
        <li>
          <a href="admin_dashboard.php" class="block px-4 py-2 border border-red-500 hover:bg-gray-700">Dashboard</a>
        </li>
        <li>
          <a href="admin_items_lost.php" class="block px-4 py-2 hover:bg-gray-700">Items Lost</a>
        </li>
        <li>
          <a href="admin_items_found.php" class="block px-4 py-2 hover:bg-gray-700">Items Found</a>
        </li>
        <li>
          <a href="admin_users.php" class="block px-4 py-2 hover:bg-gray-700">Users</a>
        </li>
        <!-- <li>
          <a href="admin_settings.html" class="block px-4 py-2 hover:bg-gray-700">Settings</a>
        </li> -->
      </ul>
      <!-- Logout Button -->
      <div class="mt-6">
        <a
          href="admin_logout.php"
          class="block px-4 py-2 text-red-500 hover:bg-gray-700 hover:text-white">Logout</a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Dashboard</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
          <!-- Total Users -->
          <div class="bg-yellow-100 p-4 rounded-lg shadow-md flex items-center">
            <i class='bx bxs-group text-3xl text-yellow-600 mr-4'></i>
            <div>
              <h3 class="text-lg font-semibold">Total Users</h3>
              <p class="text-xl font-bold"><?= $totalUsers ?></p>
            </div>
          </div>
          <!-- Lost Items -->
          <div class="bg-red-100 p-4 rounded-lg shadow-md flex items-center">
            <i class='bx bxs-help-circle text-3xl text-red-600 mr-4'></i>
            <div>
              <h3 class="text-lg font-semibold">Lost Items</h3>
              <p class="text-xl font-bold"><?= $totalLostItems ?></p>
            </div>
          </div>
          <!-- Found Items -->
          <div class="bg-green-100 p-4 rounded-lg shadow-md flex items-center">
            <i class='bx bxs-check-circle text-3xl text-green-600 mr-4'></i>
            <div>
              <h3 class="text-lg font-semibold">Found Items</h3>
              <p class="text-xl font-bold"><?= $totalFoundItems ?></p>
            </div>
          </div>
        </div>
      </div>


      <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Recent Activity</h2>
        <table class="min-w-full table-auto border-collapse border border-gray-300">
          <thead>
            <tr>
              <th class="border border-gray-300 px-4 py-2">ID</th>
              <th class="border border-gray-300 px-4 py-2">Item Name</th>
              <th class="border border-gray-300 px-4 py-2">Type</th>
              <th class="border border-gray-300 px-4 py-2">Created At</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentActivity as $activity): ?>
              <tr>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($activity['id']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($activity['item_name']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($activity['type']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($activity['created_at']) ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>


    </div>
  </div>
</body>

</html>
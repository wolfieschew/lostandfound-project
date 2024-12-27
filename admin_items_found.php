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

  // Query untuk mendapatkan data items found
  $query = "SELECT id, item_name, category, date_of_event, location, created_at 
              FROM items 
              WHERE type = 'ditemukan'";
  $params = [];

  // Filter kategori
  if (!empty($_GET['category'])) {
    $query .= " AND category = :category";
    $params[':category'] = $_GET['category'];
  }

  // Pencarian nama item
  if (!empty($_GET['search'])) {
    $query .= " AND item_name LIKE :search";
    $params[':search'] = '%' . $_GET['search'] . '%';
  }

  $query .= " ORDER BY created_at DESC";
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $foundItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Items Found - Lost and Found</title>
  <script src="https://cdn.tailwindcss.com"></script>
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
          <a
            href="admin_dashboard.php"
            class="block px-4 py-2 hover:bg-gray-700">Dashboard</a>
        </li>
        <li>
          <a
            href="admin_items_lost.php"
            class="block px-4 py-2 hover:bg-gray-700">Items Lost</a>
        </li>
        <li>
          <a
            href="admin_items_found.php"
            class="block px-4 py-2 hover:bg-gray-700">Items Found</a>
        </li>
        <li>
          <a href="admin_users.php" class="block px-4 py-2 hover:bg-gray-700">Users</a>
        </li>
        <li>
          <a
            href="admin_settings.html"
            class="block px-4 py-2 hover:bg-gray-700">Settings</a>
        </li>
      </ul>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Items Found</h2>
        <div class="flex justify-between mb-4">
          <form method="GET" action="" class="flex space-x-4">
            <!-- Dropdown untuk filter kategori -->
            <select name="category" class="p-2 bg-gray-100 rounded-lg border border-gray-300">
              <option value="">All Categories</option>
              <option value="personal" <?= isset($_GET['category']) && $_GET['category'] === 'personal' ? 'selected' : '' ?>>Personal</option>
              <option value="electronics" <?= isset($_GET['category']) && $_GET['category'] === 'electronics' ? 'selected' : '' ?>>Electronics</option>
              <option value="household" <?= isset($_GET['category']) && $_GET['category'] === 'household' ? 'selected' : '' ?>>Household</option>
            </select>
            <!-- Input untuk pencarian -->
            <input
              type="text"
              name="search"
              class="p-2 bg-gray-100 rounded-lg border border-gray-300"
              placeholder="Search Items"
              value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
            <!-- Tombol Filter -->
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">Filter</button>
          </form>
        </div>

        <?php if (isset($_GET['message'])): ?>
          <div class="bg-green-100 text-green-700 px-4 py-2 rounded-lg mb-4">
            <?= htmlspecialchars($_GET['message']) ?>
          </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
          <div class="bg-red-100 text-red-700 px-4 py-2 rounded-lg mb-4">
            <?= htmlspecialchars($_GET['error']) ?>
          </div>
        <?php endif; ?>


        <table class="min-w-full table-auto border-collapse border border-gray-300">
          <thead>
            <tr>
              <th class="border border-gray-300 px-4 py-2">Item Name</th>
              <th class="border border-gray-300 px-4 py-2">Category</th>
              <th class="border border-gray-300 px-4 py-2">Date of Event</th>
              <th class="border border-gray-300 px-4 py-2">Location</th>
              <th class="border border-gray-300 px-4 py-2">Created At</th>
              <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($foundItems)): ?>
              <tr>
                <td colspan="6" class="border border-gray-300 px-4 py-2 text-center">No items found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($foundItems as $item): ?>
                <tr>
                  <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['item_name']) ?></td>
                  <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['category']) ?></td>
                  <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['date_of_event']) ?></td>
                  <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['location']) ?></td>
                  <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['created_at']) ?></td>
                  <td class="border border-gray-300 px-4 py-2">
                    <form method="POST" action="admin_delete_item.php" onsubmit="return confirm('Are you sure you want to delete this item?');">
                      <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                      <input type="hidden" name="redirect_url" value="admin_items_found.php">
                      <button type="submit" class="px-4 py-2 bg-red-400 text-white rounded-lg hover:bg-red-500">Delete</button>
                    </form>

                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>

        </table>

      </div>
    </div>
  </div>
</body>

</html>
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

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = $_POST['item_id'];

    // Hapus laporan berdasarkan ID
    $stmt = $pdo->prepare("DELETE FROM items WHERE id = :id");
    $stmt->bindParam(':id', $itemId, PDO::PARAM_INT);

    if ($stmt->execute()) {
      // Redirect kembali ke halaman Items Lost dengan pesan sukses
      header("Location: admin_items_lost.php?message=" . urlencode("Item successfully deleted."));
      exit;
    } else {
      header("Location: admin_items_lost.php?error=" . urlencode("Failed to delete the item."));
      exit;
    }
  }
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}

$query = "SELECT id, item_name, category, type, date_of_event, location, created_at 
          FROM items 
          WHERE type = 'hilang'";
$params = [];

// Filter berdasarkan kategori
if (!empty($_GET['category'])) {
  $query .= " AND category = :category";
  $params[':category'] = $_GET['category'];
}

// Filter berdasarkan pencarian
if (!empty($_GET['search'])) {
  $query .= " AND item_name LIKE :search";
  $params[':search'] = '%' . $_GET['search'] . '%';
}

$query .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$lostItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Items Lost - Lost and Found</title>
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
        <h2 class="text-2xl font-semibold mb-4">Items Lost</h2>
        <div class="flex justify-between mb-4">
          <form method="GET" action="" class="flex space-x-4">
            <select name="category" class="p-2 bg-gray-100 rounded-lg border border-gray-300">
              <option value="">All Categories</option>
              <option value="personal">Personal</option>
              <option value="electronics">Electronics</option>
              <option value="household">Household</option>
            </select>
            <input
              type="text"
              name="search"
              class="p-2 bg-gray-100 rounded-lg border border-gray-300"
              placeholder="Search Items" />
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
            <?php foreach ($lostItems as $item): ?>
              <tr>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['item_name']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['category']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['date_of_event']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['location']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['created_at']) ?></td>
                <td class="border border-gray-300 px-4 py-2">
                  <form method="POST" action="admin_delete_item.php" onsubmit="return confirm('Are you sure you want to delete this item?');">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <input type="hidden" name="redirect_url" value="admin_items_lost.php">
                    <button type="submit" class="px-4 py-2 bg-red-400 text-white rounded-lg hover:bg-red-500">Delete</button>
                  </form>

                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>
</body>

</html>
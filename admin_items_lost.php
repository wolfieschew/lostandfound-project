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

$query = "SELECT id, item_name, category, type, date_of_event, description, email, phone_number, location, photo_path, created_at 
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
  <!-- BX BX ICONS -->
  <link href="https://cdn.jsdelivr.net/npm/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">

</head>

<body class="bg-gray-100">
  <!-- Sidebar -->
  <div class="flex h-screen">
    <div class="w-[15rem] bg-[#124076] text-white">
      <img
        class="h-[5rem] m-auto mt-[1rem]"
        src="Assets/img/lostnfoundlogowhite.png" />
      <ul class="mt-6 space-y-2">
        <li>
          <a
            href="admin_dashboard.php"
            class="block px-4 py-2 hover:bg-[#4973b3]"><i class='bx bxs-dashboard'></i> Dashboard</a>
        </li>
        <li>
          <a
            href="admin_items_lost.php"
            class="block px-4 py-2 bg-[#1E5CB8]"><i class='bx bxs-box'></i> Items Lost</a>
        </li>
        <li>
          <a
            href="admin_items_found.php"
            class="block px-4 py-2 hover:bg-[#4973b3]"><i class='bx bxs-box'></i> Items Found</a>
        </li>
        <li>
          <a href="admin_users.php" class="block px-4 py-2 hover:bg-[#4973b3]"><i class='bx bxs-user-circle'></i> Users</a>
        </li>
        <!-- <li>
          <a
            href="admin_settings.html"
            class="block px-4 py-2 hover:bg-gray-700">Settings</a>
        </li> -->
      </ul>
      <!-- Logout Button -->
      <div class="mt-6">
        <a
          href="admin_logout.php"
          class="block px-4 py-2 text-red-500 hover:text-white"><i class='bx bxs-exit'></i> Logout</a>
      </div>
    </div>

    <!-- Main Content -->
    <div class="flex-1 p-6">
      <div class="bg-white rounded-lg shadow-lg p-6">
        <h2 class="text-2xl font-semibold mb-4">Lost Items Reports</h2>
        <div class="flex justify-between mb-4">
          <form method="GET" action="" class="flex space-x-4">
            <select name="category" class="p-2 bg-gray-100 rounded-lg border border-gray-300">
              <option value="">All Categories</option>
              <option value="Perhiasan Khusus">Perhiasan Khusus</option>
              <option value="Elektronik">Elektronik</option>
              <option value="Buku & Dokumen">Buku & Dokumen</option>
              <option value="Aksesoris Pribadi">Aksesoris Pribadi</option>
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
              <th class="border border-gray-300 px-4 py-2">Image</th>
              <th class="border border-gray-300 px-4 py-2">Item Name</th>
              <th class="border border-gray-300 px-4 py-2">Category</th>
              <th class="border border-gray-300 px-4 py-2">Date of Event</th>
              <th class="border border-gray-300 px-4 py-2">Location</th>
              <th class="border border-gray-300 px-4 py-2">Description</th>
              <th class="border border-gray-300 px-4 py-2">Created At</th>
              <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lostItems as $item): ?>
              <tr>
                <td class="border border-gray-300 px-4 py-2">
                  <?php if (!empty($item['photo_path'])): ?>
                    <img src="<?= htmlspecialchars($item['photo_path']) ?>" alt="Image of <?= htmlspecialchars($item['item_name']) ?>" class="w-16 h-16 object-cover rounded-lg">
                  <?php else: ?>
                    <span>No Image</span>
                  <?php endif; ?>
                </td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['item_name']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['category']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['date_of_event']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['location']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['description']) ?></td>
                <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($item['created_at']) ?></td>
                <td class="border border-gray-300 px-4 py-2">
                  <button
                    class="px-4 py-2 bg-yellow-400 text-white rounded-lg hover:bg-yellow-500"
                    onclick="openEditModal(<?= htmlspecialchars(json_encode($item)) ?>)"><i class='bx bxs-edit'></i>Edit</button>
                  <form method="POST" action="admin_delete_item.php" onsubmit="return confirm('Are you sure you want to delete this item?');" style="display:inline;">
                    <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                    <button type="submit" class="px-4 py-2 bg-red-400 text-white rounded-lg hover:bg-red-500"><i class='bx bxs-trash'></i>Delete</button>
                  </form>
                </td>

              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>

      </div>
    </div>
  </div>

  <!-- Modal Edit -->
  <div id="editModal" class="fixed inset-0 bg-gray-800 bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-lg shadow-lg p-6 w-1/3">
      <h2 class="text-2xl font-semibold mb-4">Edit Item</h2>
      <form method="POST" action="admin_update_item.php">
        <!-- ID item (hidden input) -->
        <input type="hidden" id="editItemId" name="id">
        <input type="hidden" name="redirect_url" value="admin_items_lost.php">
        <!-- Item Name -->
        <div class="mb-4">
          <label for="editItemName" class="block text-sm font-medium">Item Name</label>
          <input type="text" id="editItemName" name="item_name" class="p-2 w-full border border-gray-300 rounded-lg">
        </div>

        <!-- Category -->
        <div class="mb-4">
          <label for="editCategory" class="block text-sm font-medium">Category</label>
          <input type="text" id="editCategory" name="category" class="p-2 w-full border border-gray-300 rounded-lg">
        </div>

        <!-- Description -->
        <div class="mb-4">
          <label for="editDescription" class="block text-sm font-medium">Description</label>
          <textarea id="editDescription" name="description" class="p-2 w-full border border-gray-300 rounded-lg"></textarea>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-end space-x-4">
          <button type="button" onclick="closeEditModal()" class="px-4 py-2 bg-gray-300 rounded-lg">Cancel</button>
          <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg">Save</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Scripting JS -->
  <script>
    function openEditModal(item) {
      // Isi form dengan data item yang dipilih
      document.getElementById('editItemId').value = item.id;
      document.getElementById('editItemName').value = item.item_name;
      document.getElementById('editCategory').value = item.category;
      document.getElementById('editDescription').value = item.description;
      document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
      document.getElementById('editModal').classList.add('hidden');
    }
  </script>


</body>

</html>
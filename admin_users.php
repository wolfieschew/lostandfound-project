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

  // Query untuk mengambil data user
  $query = "SELECT id, first_name, last_name, email, role FROM users";
  $params = [];

  // Jika ada parameter pencarian
  if (!empty($_GET['search'])) {
    $query .= " WHERE first_name LIKE :search OR last_name LIKE :search";
    $params[':search'] = '%' . $_GET['search'] . '%';
  }

  $query .= " ORDER BY id DESC";
  $stmt = $pdo->prepare($query);
  $stmt->execute($params);
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Users - Lost and Found</title>
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
        <h2 class="text-2xl font-semibold mb-4">Users</h2>
        <div class="flex justify-between mb-4">
          <form method="GET" action="" class="flex space-x-4">
            <input
              type="text"
              name="search"
              class="p-2 bg-gray-100 rounded-lg border border-gray-300"
              placeholder="Search Users by Name"
              value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-500">Search</button>
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
              <th class="border border-gray-300 px-4 py-2">Name</th>
              <th class="border border-gray-300 px-4 py-2">Email</th>
              <th class="border border-gray-300 px-4 py-2">Role</th>
              <th class="border border-gray-300 px-4 py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($users)): ?>
              <tr>
                <td colspan="4" class="border border-gray-300 px-4 py-2 text-center">No users found.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($users as $user): ?>
                <tr>
                  <td class="border border-gray-300 px-4 py-2">
                    <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?>
                  </td>
                  <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars($user['email']) ?></td>
                  <td class="border border-gray-300 px-4 py-2"><?= htmlspecialchars(ucfirst($user['role'])) ?></td>
                  <td class="border border-gray-300 px-4 py-2">
                    <form method="POST" action="admin_delete_user.php" onsubmit="return confirm('Are you sure you want to delete this user?');">
                      <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
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
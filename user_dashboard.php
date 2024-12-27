<?php
// Sertakan remember_token.php
include 'remember_token.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: log_in.html");
  exit;
}

// Koneksi ke database
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'lost_and_found_items';

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Ambil parameter pencarian
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : 'all';

// Query SQL
$sql = "SELECT * FROM items";
$conditions = [];

// Pencarian berdasarkan kata kunci
if (!empty($search)) {
  $searchTerms = explode(' ', $search);
  foreach ($searchTerms as $term) {
    $conditions[] = "(LOWER(item_name) LIKE LOWER('%$term%') OR LOWER(description) LIKE LOWER('%$term%'))";
  }
}

// Pencarian berdasarkan kategori
if ($category !== 'all') {
  $conditions[] = "LOWER(category) = LOWER('$category')";
}

// Tambahkan kondisi ke query
if (count($conditions) > 0) {
  $sql .= " WHERE " . implode(" AND ", $conditions);
}

$sql .= " ORDER BY date_of_event DESC";

// Eksekusi query
$result = $conn->query($sql);

// Hitung jumlah laporan
$totalReports = $result->num_rows;

// Tentukan keterangan berdasarkan kondisi pencarian
if (!empty($search) || $category !== 'all') {
  $statusMessage = $totalReports > 1
    ? "Showing $totalReports items"
    : ($totalReports === 1
      ? "Showing 1 item"
      : "No items match your search.");
} else {
  $statusMessage = $totalReports > 1
    ? "Total Reports: $totalReports"
    : ($totalReports === 1
      ? "Total Reports: 1"
      : "No reports found.");
}

// Ambil notifikasi dengan nama depan pengguna
$notificationsResult = $conn->query("
    SELECT 
        notifications.message, 
        notifications.created_at, 
        users.first_name
    FROM 
        notifications
    JOIN 
        users ON notifications.user_id = users.id
    WHERE 
        notifications.is_read = 0
    ORDER BY 
        notifications.created_at DESC
");

$unreadCount = $notificationsResult->num_rows;

?>



<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- Google Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Playpen+Sans:wght@300;500&display=swap"
    rel="stylesheet" />
  <!-- Tailwind JS -->
  <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
  <!-- bx bx-icons -->
  <link
    href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
    rel="stylesheet" />
  <!-- flowbite -->
  <link
    href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css"
    rel="stylesheet" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <title>user-dasboard</title>
</head>

<!-- Header Section -->

<body class="font-[Lato] h-screen">
  <header class="bg-white">
    <!-- Hanya header yang diberi sticky -->
    <nav class="flex justify-between items-center w-[90%] xl:w-[70%] mx-auto">
      <div>
        <img
          class="mb-3 mt-3 h-[4rem] sm:h-20 cursor-pointer"
          src="Assets/img/lostnfoundlogo.png"
          alt="..." />
      </div>
      <!-- Menu navigasi ini tetap absolute, tanpa kelas sticky -->
      <div
        class="nav-links duration-500 md:static absolute bg-white md:min-h-fit min-h-[60vh] left-0 top-[-100%] md:w-auto w-full flex items-center px-2 lg:px-5 z-10 text-xs lg:text-base z-20">
        <ul
          class="flex md:flex-row flex-col md:items-center md:gap-[4vw] gap-8">
          <li>
            <a
              class="hover:text-gray-500 border-b-4 border-[#124076] pb-2"
              href="#">Home</a>
          </li>
          </li>
          <!-- <li>
            <a class="hover:text-gray-500" href="static_menu.html">Static</a>
          </li> -->
          <li>
            <a class="hover:text-gray-500" href="message.php">Message</a>
          </li>
          <li>
            <a class="hover:text-gray-500" href="profile.php">Profile</a>
          </li>
          <li>
            <a class="hover:text-gray-500" href="activity.php">Activity</a>
          </li>
          <li>
            <a class="hover:text-gray-500" href="about-us.php">About us</a>
          </li>
        </ul>
      </div>
      <div class="flex items-center gap-6 relative">
        <!-- Tombol Notifikasi -->
        <button id="notification-icon" type="button" class="relative text-[#124076] p-0 w-full h-full items-center rounded-[20%]">
          <i class="bx bxs-bell text-3xl"></i>
          <!-- Badge -->
          <?php if ($unreadCount > 0): ?>
            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full px-2">
              <?= $unreadCount ?>
            </span>
          <?php endif; ?>
        </button>

        <!-- Dropdown Modal -->
        <div id="notification-dropdown" class="absolute top-full mt-2 right-0 w-80 bg-white shadow-lg rounded-lg hidden z-20">
          <ul class="divide-y divide-gray-200">
            <?php if ($unreadCount > 0): ?>
              <?php while ($notif = $notificationsResult->fetch_assoc()): ?>
                <li class="p-4 hover:bg-gray-100">
                  <p class="text-sm font-medium text-gray-700">
                    <?= htmlspecialchars($notif['first_name']) ?>: <?= htmlspecialchars($notif['message']) ?>
                  </p>
                  <span class="block text-xs text-gray-500"><?= $notif['created_at'] ?></span>
                </li>
              <?php endwhile; ?>
            <?php else: ?>
              <li class="p-4 text-center text-gray-500">No new notifications</li>
            <?php endif; ?>
          </ul>
        </div>


        <!-- Menu (Tetap) -->
        <ion-icon
          onclick="onToggleMenu(this)"
          name="menu"
          class="text-5xl cursor-pointer md:hidden"></ion-icon>
      </div>

    </nav>
    <!-- Announcement Text -->
    <div class="text-center">
      <p class=" text-sm md:text-lg text-black bg-blue-100">
        Kehilangan atau Menemukan Barang? Laporkan Sekarang Melalui Form Ini! <a class="underline text-[#124076] font-semibold ml-2" href="form-pelaporan-hilang.html">Klik Disini</a>
      </p>
    </div>
  </header>
  <!-- Header Section -->

  <!-- Search Section -->
  <section class="bg-[#91B0D3] h-[15rem] flex flex-col items-center justify-center">
    <!-- Dropdown Buttons and Search -->
    <div class="w-full max-w-4xl px-4">
      <form class="flex flex-col sm:flex-row items-center gap-4 w-full" method="GET" action="">
        <!-- Dropdown (Select) -->
        <div class="relative w-full sm:w-auto">
          <select
            id="category-dropdown"
            name="category"
            class="w-full sm:w-auto flex-shrink-0 z-10 inline-flex items-center py-2.5 px-4 text-sm font-medium text-black bg-gray-100 border border-gray-300 rounded-lg sm:rounded-l-lg sm:rounded-r-none focus:ring-2 focus:outline-none focus:ring-blue-500">
            <option value="all" <?= $category === 'all' ? 'selected' : '' ?>>Semua Kategori</option>
            <option value="Perhiasan Khusus" <?= $category === 'Perhiasan Khusus' ? 'selected' : '' ?>>Perhiasan Khusus</option>
            <option value="Elektronik" <?= $category === 'Elektronik' ? 'selected' : '' ?>>Elektronik</option>
            <option value="Buku & Dokumen" <?= $category === 'Buku & Dokumen' ? 'selected' : '' ?>>Buku & Dokumen</option>
            <option value="Aksesoris Pribadi" <?= $category === 'Aksesoris Pribadi' ? 'selected' : '' ?>>Aksesoris Pribadi</option>
          </select>
        </div>

        <!-- Search input and button -->
        <div class="relative w-full">
          <input
            type="search"
            id="search-dropdown"
            name="search"
            class="block p-2.5 w-full z-20 text-sm text-black bg-white rounded-lg sm:rounded-none sm:rounded-r-lg border border-gray-300 focus:text-black focus:ring-blue-500 focus:border-blue-500 placeholder-gray-400"
            placeholder="Search"
            value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>" />
          <button
            type="submit"
            class="absolute top-0 right-0 p-2.5 text-sm font-medium h-full text-white bg-[#124076] rounded-lg sm:rounded-none sm:rounded-r-lg focus:ring-4 focus:outline-none focus:ring-blue-300">
            <svg class="w-4 h-4" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
              <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
            </svg>
            <span class="sr-only">Search</span>
          </button>
        </div>
      </form>
    </div>
  </section>


  <section class="bg-[#91B0D3] h-[60rem] sm:h-[50rem] px-4 sm:px-6 lg:px-8">
    <div class="container mx-auto h-full">
      <!-- Wrapper untuk Keterangan dan Grid -->
      <div class="px-4 sm:px-6 lg:px-8">
        <!-- Display Total Reports -->
        <div class="text-gray-700 text-left text-lg font-medium mb-4">
          <?= $totalReports > 0 ? "Total Reports: $totalReports" : "No reports found." ?>
        </div>

        <!-- Pembungkus dengan opsi scroll -->
        <div class="overflow-y-auto h-[44rem] scrollbar-thin scrollbar-thumb-[#124076] scrollbar-track-[#e5e7eb] scrollbar-rounded">
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <?php
            while ($row = $result->fetch_assoc()) {
              echo '<div class="bg-white shadow-md rounded-lg overflow-hidden">';
              echo '<div class="relative">';
              echo '<div class="absolute top-2 left-2 ' . ($row['type'] == 'hilang' ? 'bg-red-500' : 'bg-green-500') . ' text-white text-xs uppercase font-semibold px-2 py-1 rounded">';
              echo $row['type'] == 'hilang' ? 'Lost' : 'Found';
              echo '</div>';
              echo '<img src="' . $row['photo_path'] . '" alt="' . htmlspecialchars($row['item_name']) . '" class="w-full h-48 object-cover" />';
              echo '</div>';
              echo '<div class="p-4">';
              echo '<h3 class="text-lg font-semibold text-gray-800">' . htmlspecialchars($row['item_name']) . '</h3>';
              echo '<p class="text-sm text-gray-500 mt-2 flex items-center">';
              echo '<i class="bx bx-calendar-alt mr-1"></i> ' . htmlspecialchars($row['date_of_event']);
              echo '</p>';
              echo '<button class="mt-4 w-full bg-[#124076] text-white text-sm py-2 px-4 rounded hover:bg-[#2e64a1]" onclick="showItemDetails(' . $row['id'] . ')">';
              echo 'Details';
              echo '</button>';
              echo '</div>';
              echo '</div>';
            }
            ?>
          </div>
        </div>
      </div>
    </div>
  </section>




  <!-- Modal -->
  <!-- Modal -->
  <div id="itemModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-50">
    <div class="flex justify-center items-center h-full">
      <div class="bg-white p-6 rounded-lg max-w-lg w-full">
        <!-- Nama Item -->
        <h2 class="text-xl font-semibold text-gray-800" id="modalItemName">Item Name</h2>
        <p class="text-sm text-gray-500 mt-2" id="modalItemDate">Date: Event Date</p>
        <p class="text-sm text-gray-500 mt-2" id="modalItemCategory">Category: Item Category</p>
        <p class="text-sm text-gray-500 mt-2" id="modalItemLocation">Location: Item Location</p>

        <!-- Deskripsi Item -->
        <p class="mt-4 text-gray-700" id="modalItemDescription">Item Description</p>

        <!-- Gambar dengan `object-contain` -->
        <div class="mt-4 bg-gray-100 rounded-lg overflow-hidden">
          <img
            id="modalItemImage"
            src=""
            alt="Item Image"
            class="w-full h-48 object-contain" />
        </div>

        <!-- Kontak -->
        <p class="text-sm text-gray-500 mt-2" id="modalItemContact">Contact: Contact Info</p>

        <!-- Tombol Tutup -->
        <button
          id="closeModalBtn"
          class="mt-4 w-full bg-red-500 text-white py-2 px-4 rounded hover:bg-red-600">
          Close
        </button>
      </div>
    </div>
  </div>




  <!-- Footer Section -->

  <footer class="bg-[#124076]">
    <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
      <div class="md:flex md:justify-between">
        <div class="mb-6 md:mb-0">
          <a href="#" class="flex items-center">
            <img
              src="Assets/img/lostnfoundlogowhite.png"
              class="h-28 me-3"
              alt="FlowBite Logo" />
          </a>
        </div>
        <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
          <div>
            <h2
              class="mb-6 text-sm font-semibold text-white uppercase dark:text-white">
              About
            </h2>
            <ul class="text-white font-medium">
              <li class="mb-4">
                <a href="#" class="hover:underline">About Lost and Found Items</a>
              </li>
            </ul>
          </div>
          <div>
            <h2
              class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">
              Lost and Found Items
            </h2>
            <ul class="text-white font-medium">
              <li class="mb-4">
                <a
                  href="#"
                  class="hover:underline">Lost Items</a>
              </li>
              <li>
                <a
                  href="#"
                  class="hover:underline">Found Items</a>
              </li>
              <li>
                <a
                  href="#"
                  class="hover:underline">Information about Lost and Found Items</a>
              </li>
            </ul>
          </div>
          <div>
            <h2
              class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">
              Legal
            </h2>
            <ul class="text-white font-medium">
              <li class="mb-4">
                <a href="#" class="hover:underline">Feedback</a>
              </li>
              <li>
                <a href="#" class="hover:underline">Terms &amp; Conditions Lost and Found Items</a>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <hr class="my-6 border-white sm:mx-auto" />
      <div class="text-center sm:flex sm:items-center sm:justify-between">
        <span class="text-sm text-white text-center">© 2024 <a href="/" class="hover:underline">Lost and Found Team</a>.
          All Rights Reserved.
        </span>
      </div>
    </div>
  </footer>

  <!-- Script JS  -->

  <script>
    const navLinks = document.querySelector(".nav-links");

    function onToggleMenu(e) {
      e.name = e.name === "menu" ? "close" : "menu";
      navLinks.classList.toggle("top-[11%]");
    }
  </script>

  <script>
    document.addEventListener("click", (e) => {
      // Dropdown 1
      const dropdownMenu1 = document.getElementById("dropdownMenu1");
      const dropdownButton1 = document.querySelector(
        'button[onclick="toggleDropdown1()"]'
      );

      if (
        !dropdownButton1.contains(e.target) &&
        !dropdownMenu1.contains(e.target)
      ) {
        dropdownMenu1.classList.add("hidden");
      }

      // Dropdown 2
      const dropdownMenu2 = document.getElementById("dropdownMenu2");
      const dropdownButton2 = document.querySelector(
        'button[onclick="toggleDropdown2()"]'
      );

      if (
        !dropdownButton2.contains(e.target) &&
        !dropdownMenu2.contains(e.target)
      ) {
        dropdownMenu2.classList.add("hidden");
      }
    });

    function toggleDropdown1() {
      const dropdownMenu1 = document.getElementById("dropdownMenu1");
      dropdownMenu1.classList.toggle("hidden");
    }

    function toggleDropdown2() {
      const dropdownMenu2 = document.getElementById("dropdownMenu2");
      dropdownMenu2.classList.toggle("hidden");
    }
  </script>
  <!-- Report Link -->
  <script>
    function toggleDropdown() {
      const dropdown = document.getElementById('dropdownContent');
      dropdown.classList.toggle('hidden'); // Menampilkan atau menyembunyikan dropdown
    }
  </script>

  <!-- JavaScript for Modal -->
  <script>
    // Fungsi untuk menampilkan modal dan mengisi data
    function showItemDetails(itemId) {
      // Ambil data detail item berdasarkan ID (gunakan AJAX untuk mengambil data dari server)
      fetch('get_item_details.php?id=' + itemId)
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            alert('Item not found!');
          } else {
            // Isi modal dengan data item
            document.getElementById('modalItemName').innerText = data.item_name;
            document.getElementById('modalItemDate').innerText = 'Date: ' + data.date_of_event;
            document.getElementById('modalItemCategory').innerText = 'Category: ' + data.category;
            document.getElementById('modalItemLocation').innerText = 'Location: ' + data.location;
            document.getElementById('modalItemDescription').innerText = data.description;
            document.getElementById('modalItemImage').src = data.photo_path;
            document.getElementById('modalItemContact').innerText = 'Contact: ' + data.email + ' / ' + data.phone_number;

            // Tampilkan modal dengan menghapus class hidden
            document.getElementById('itemModal').classList.remove('hidden');
          }
        })
        .catch(error => console.error('Error fetching item details:', error));
    }

    // Fungsi untuk menutup modal
    document.getElementById('closeModalBtn').addEventListener('click', function() {
      document.getElementById('itemModal').classList.add('hidden');
    });
  </script>

  <script>
    // Toggle Dropdown
    document.getElementById('notification-icon').addEventListener('click', function() {
      const dropdown = document.getElementById('notification-dropdown');
      dropdown.classList.toggle('hidden');
    });

    // Tutup dropdown saat klik di luar
    document.addEventListener('click', function(e) {
      const icon = document.getElementById('notification-icon');
      const dropdown = document.getElementById('notification-dropdown');
      if (!icon.contains(e.target) && !dropdown.contains(e.target)) {
        dropdown.classList.add('hidden');
      }
    });
  </script>


  <!-- Flowbite -->
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
</body>

</html>
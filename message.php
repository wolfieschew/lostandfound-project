<?php
// Mulai sesi
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
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

// Ambil notifikasi yang belum dibaca
$userId = $_SESSION['user_id'];
$notificationsQuery = "SELECT n.*, u.first_name 
                       FROM notifications n 
                       JOIN users u ON n.user_id = u.id 
                       WHERE n.is_read = 0 
                       ORDER BY n.created_at DESC";
$notificationsResult = $conn->query($notificationsQuery);
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
  <title>message</title>
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
              class="hover:text-gray-500"
              href="user_dashboard.php">Home</a>
          </li>
          </li>
          <!-- <li>
              <a class="hover:text-gray-500" href="static_menu.html">Static</a>
            </li> -->
          <li>
            <a class="hover:text-gray-500 border-b-4 border-[#124076] pb-2" href="#">Message</a>
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
  </header>
  <!-- Header Section -->

  <!-- message Section -->
  <section class="bg-[#91B0D3] h-[55rem] z-10"></section>


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

  <script>
    const notificationIcon = document.getElementById('notification-icon');
    const notificationDropdown = document.getElementById('notification-dropdown');

    notificationIcon.addEventListener('click', () => {
      notificationDropdown.classList.toggle('hidden');
    });

    document.addEventListener('click', (e) => {
      if (!notificationIcon.contains(e.target) && !notificationDropdown.contains(e.target)) {
        notificationDropdown.classList.add('hidden');
      }
    });
  </script>

  <!-- Flowbite -->
  <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
</body>

</html>
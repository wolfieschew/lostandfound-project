<?php
session_start();

// Pastikan pengguna sudah login
if (!isset($_SESSION['user_id'])) {
  header("Location: log_in.html");
  exit;
}

// Koneksi ke database
$host = 'localhost';
$dbname = 'lost_and_found_items';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $dbname);

// Periksa koneksi database
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Query untuk mengambil notifikasi
$notificationsQuery = "
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
        notifications.created_at DESC";

$notificationsResult = $conn->query($notificationsQuery);
$unreadCount = $notificationsResult ? $notificationsResult->num_rows : 0;
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>about-us</title>
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
</head>

<body>
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
          class="nav-links duration-500 md:static absolute bg-white md:min-h-fit min-h-[60vh] left-0 top-[-100%] md:w-auto w-full flex items-center px-2 lg:px-5 z-10 text-xs lg:text-base">
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
            <!-- <li>
              <a class="hover:text-gray-500" href="message.php">Message</a>
            </li> -->
            <li>
              <a class="hover:text-gray-500" href="profile.php">Profile</a>
            </li>
            <li>
              <a class="hover:text-gray-500" href="activity.php">Activity</a>
            </li>
            <li>
              <a class="hover:text-gray-500 border-b-4 border-[#124076] pb-2" href="about-us.php">About us</a>
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


    <!-- About us Section -->
    <div class="bg-[#91B0D3] min-h-screen py-10 px-20">
      <!-- About Us Section -->
      <section class="max-w-4xl mx-auto bg-white rounded-lg shadow-md overflow-hidden">
        <div class="relative">
          <img
            src="Assets/img/gambaritems.jpg"
            alt="Background Image"
            class="w-full h-64 object-cover" />
          <div class="absolute inset-0 bg-gray-800 bg-opacity-30 flex items-center justify-center">
            <h1 class="text-white text-4xl font-bold">About Us</h1>
          </div>
        </div>
        <div class="p-8">
          <p class="text-gray-600">
            Welcome to Lost & Found Items Connect, your reliable partner in reuniting students with
            their lost belongings. Founded in 2024, our mission is to create a seamless and efficient
            platform that helps individuals and communities around Telkom University to recover lost
            items quickly and effortlessly.
          </p>
        </div>
      </section>

      <!-- Feedback Section -->
      <section class="max-w-4xl mx-auto mt-8 space-y-8">
        <!-- Give Feedback Card -->
        <div class="bg-white rounded-lg shadow-md p-6">
          <h2 class="text-2xl font-semibold mb-2">Give Feedback</h2>
          <p class="text-gray-600 mb-4">
            How would you describe your experience after using our products for helping you find out your lost item?
          </p>
          <div class="flex space-x-2">
            <!-- Star Ratings -->
            <span class="text-yellow-400 text-2xl">&#9733;</span>
            <span class="text-yellow-400 text-2xl">&#9733;</span>
            <span class="text-yellow-400 text-2xl">&#9733;</span>
            <span class="text-yellow-400 text-2xl">&#9733;</span>
            <span class="text-gray-300 text-2xl">&#9733;</span>
          </div>
        </div>

        <!-- Feedback Section -->
        <section class="max-w-4xl grid grid-cols-2 gap-2 mx-auto mt-8">
          <!-- Give Feedback Card -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-2 mt-3">Feedback Description</h2>
            <p class="text-gray-600 mb-4">
              How would you describe your experience after using our products for helping you find out your lost item?
            </p>
            <textarea
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="4"
              placeholder="Write your feedback"></textarea>
          </div>

          <!-- Comments and Suggestions Card -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-2xl font-semibold mb-2">Comments and Suggestions</h2>
            <p class="text-gray-600 mb-4">
              How would you describe your experience after using our products for helping you find out your lost item?
            </p>
            <textarea
              class="w-full p-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
              rows="4"
              placeholder="Write your comment"></textarea>

            <div class="mt-3"></div>
            <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Submit</button>
          </div>
    </div>

    </section>
    </div>


    <!-- Footer Section -->
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
                  <a href="about-us-non-log.html" class="hover:underline">About Lost and Found Items</a>
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
              </ul>
            </div>
            <div>
              <h2
                class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">
                Legal
              </h2>
              <ul class="text-white font-medium">
                <li class="mb-4">
                  <a href="about-us.php" class="hover:underline">Feedback</a>
                </li>
                <li>
                  <a href="terms-condition.html" class="hover:underline">Terms &amp; Conditions Lost and Found Items</a>
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

    <!-- Javascript -->
    <script>
      const navLinks = document.querySelector(".nav-links");

      function onToggleMenu(e) {
        e.name = e.name === "menu" ? "close" : "menu";
        navLinks.classList.toggle("top-[11%]");
      }
    </script>

    <script>
      document.getElementById("notification-icon").addEventListener("click", function() {
        const dropdown = document.getElementById("notification-dropdown");
        dropdown.classList.toggle("hidden");
      });

      // Menutup dropdown jika klik di luar area
      document.addEventListener("click", function(event) {
        const dropdown = document.getElementById("notification-dropdown");
        const button = document.getElementById("notification-icon");
        if (!dropdown.contains(event.target) && !button.contains(event.target)) {
          dropdown.classList.add("hidden");
        }
      });
    </script>
  </body>

</html>
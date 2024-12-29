<?php
session_start();

// Periksa apakah user sudah login
if (!isset($_SESSION['user_id'])) {
  header("Location: log_in.html");
  exit;
}

$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root';
$pass = '';

// Koneksi ke database
try {
  $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $conn = new mysqli($host, $user, $pass, $db);
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Ambil user_id dari session
  $userId = $_SESSION['user_id'];

  // Query data pengguna
  $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
  $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$user) {
    echo "Pengguna tidak ditemukan!";
    exit;
  }

  // Query notifikasi
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
  if (!$notificationsResult) {
    die("Query Error: " . $conn->error);
  }

  $unreadCount = $notificationsResult->num_rows;
} catch (PDOException $e) {
  die("Error: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>my-profile</title>
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
            <li>
              <a class="hover:text-gray-500" href="message.php">Message</a>
            </li>
            <li>
              <a class="hover:text-gray-500 border-b-4 border-[#124076] pb-2" href="profile.php">Profile</a>
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

    <!-- Profile Section -->

    <div class="bg-[#91B0D3] min-h-screen py-10">
      <!-- Profile Section -->
      <section class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-4">
            <img
              src="<?php echo htmlspecialchars($user['profile_picture'] ? 'uploads/' . $user['profile_picture'] : 'https://via.placeholder.com/100'); ?>"
              alt="Profile Picture"
              class="w-36 h-36 rounded-full border-2 border-[#124076] shadow-lg object-cover" /><span data-modal-target="default-modal-profile" data-modal-toggle="default-modal-profile" class="absolute mt-[6rem] text-white p-[1] border-2 border-[#124076] bg-[#124076] rounded-md cursor-pointer">✎</span>


            <!-- Modal untuk Edit Profile -->
            <div id="default-modal-profile" tabindex="-1" aria-hidden="true" class="hidden fixed top-0 right-0 left-0 z-50 flex justify-center items-center w-full h-[calc(100%-1rem)] max-h-full md:inset-0 overflow-y-auto overflow-x-hidden">
              <div class="relative p-4 w-full max-w-2xl max-h-full mx-0">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow">
                  <!-- Modal header -->
                  <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                    <h3 class="text-xl font-semibold text-black">Edit Profile</h3>
                    <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal-profile">
                      <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                      </svg>
                      <span class="sr-only">Close modal</span>
                    </button>
                  </div>
                  <!-- Modal body -->
                  <div class="p-4 md:p-5 space-y-4">
                    <form action="upload_picture.php" method="POST" enctype="multipart/form-data">
                      <div class="mb-4 flex justify-center items-center">
                        <img
                          src="<?php echo htmlspecialchars($user['profile_picture'] ? 'uploads/' . $user['profile_picture'] : 'https://via.placeholder.com/100'); ?>"
                          alt="Profile Picture"
                          class="w-52 h-52 object-cover rounded-full" />
                      </div>

                      <label class="block mb-2 text-sm font-medium text-gray-900" for="file">Upload file</label>
                      <input
                        class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50"
                        id="file"
                        type="file"
                        name="profile_picture"
                        accept="image/*" />

                      <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>" />
                      <div class="flex items-center justify-end mt-4">
                        <button
                          type="submit"
                          class="text-white bg-[#124076] hover:bg-[#255386] font-medium rounded-lg text-sm px-5 py-2.5 mr-6 text-center">
                          Upload
                        </button>
                      </div>
                    </form>
                    <!-- Tombol Hapus Gambar Profil -->
                    <form action="delete_profile_picture.php" method="POST">
                      <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>" />
                      <button type="submit" class="bg-red-100 text-red-600 px-4 py-2 rounded hover:bg-red-200">
                        Hapus Gambar Profil
                      </button>
                    </form>

                  </div>
                </div>
              </div>
            </div>
            <!-- End of Modal -->

            <div>
              <h2 class="text-2xl font-semibold"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></h2>
              <p class="text-gray-500"><?php echo htmlspecialchars($user['nim']); ?></p>
              <p class="text-gray-500">Mahasiswa</p>
            </div>
          </div>
          <div class="flex space-x-2">
            <a href="logout.php">
              <button class="bg-red-100 text-red-600 px-4 py-2 rounded hover:bg-red-200 flex items-center">
                Sign out
              </button>
            </a>
          </div>
        </div>
      </section>


      <!-- Personal Information Section -->
      <section class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-8 mb-6">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Personal Information</h3>
          <!-- Modal toggle -->
          <button data-modal-target="default-modal-1" data-modal-toggle="default-modal-1" class="bg-gray-200 text-gray-600 px-4 py-2 rounded hover:bg-gray-300 flex items-center">
            Edit <span class="ml-2">✎</span>
          </button>

          <!-- Main modal Personal Information -->
          <div id="default-modal-1" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                  <h3 class="text-xl font-semibold text-black">
                    Personal Information
                  </h3>
                  <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-hide="default-modal-1">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                  </button>
                </div>
                <!-- Modal content -->
                <div class="p-4 md:p-5 space-y-4">
                  <form action="update_user.php" method="POST">
                    <div class="mb-4">
                      <label for="firstName" class="block text-gray-600">First Name</label>
                      <input type="text" id="firstName" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="mb-4">
                      <label for="lastName" class="block text-gray-600">Last Name</label>
                      <input type="text" id="lastName" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="mb-4">
                      <label for="nim" class="block text-gray-600">Nim</label>
                      <input placeholder="-- Tambahkan Nim --" type="text" id="nim" name="nim" value="<?php echo htmlspecialchars($user['nim']); ?>" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="mb-4">
                      <label for="email" class="block text-gray-600">Email</label>
                      <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <div class="mb-4">
                      <label for="phone" class="block text-gray-600">Phone</label>
                      <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone']); ?>" class="w-full px-4 py-2 mt-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    <!-- Modal footer -->
                    <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                      <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>" />
                      <button type="submit" class="text-white bg-[#124076] hover:bg-[#255386] font-medium rounded-lg text-sm px-5 py-2.5 text-center">Update</button>
                    </div>
                </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
          <div>
            <p class="text-gray-600 font-medium">First Name</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($user['first_name']); ?></p>
          </div>
          <div>
            <p class="text-gray-600 font-medium">Last Name</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($user['last_name']); ?></p>
          </div>
          <div>
            <p class="text-gray-600 font-medium">NIM</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($user['nim']); ?></p>
          </div>
          <div>
            <p class="text-gray-600 font-medium">Email address</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($user['email']); ?></p>
          </div>
          <div>
            <p class="text-gray-600 font-medium">Phone</p>
            <p class="text-gray-800"><?php echo htmlspecialchars($user['phone']); ?></p>
          </div>
        </div>
      </section>

      <!-- Address Information Section -->
      <section class="max-w-5xl mx-auto bg-white rounded-lg shadow-md p-8">
        <div class="flex justify-between items-center mb-4">
          <h3 class="text-xl font-semibold">Address Information</h3>
          <button data-modal-target="default-modal-2" data-modal-toggle="default-modal-2" class="bg-gray-200 text-gray-600 px-4 py-2 rounded hover:bg-gray-300 flex items-center">
            Edit <span class="ml-2">✎</span>
          </button>

          <!-- Main modal Personal Information -->
          <div id="default-modal-2" tabindex="-1" aria-hidden="true" class="hidden overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
              <!-- Modal content -->
              <div class="relative bg-white rounded-lg shadow">
                <!-- Modal header -->
                <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t">
                  <h3 class="text-xl font-semibold text-black">
                    Address Information
                  </h3>
                  <button type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center" data-modal-hide="default-modal-2">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                      <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                    <span class="sr-only">Close modal</span>
                  </button>
                </div>
                <!-- Modal body -->
                <div class="p-4 md:p-5 space-y-4">
                  <form action="update_address.php" method="POST">
                    <div>
                      <label for="address" class="block text-gray-600 font-medium">Address</label>
                      <input placeholder="-- Tambahkan Address --" type="text" id="address" name="address" class="w-full p-2 border rounded mt-1" value="<?php echo htmlspecialchars($user['address']); ?>" />
                    </div>
                </div>

                <!-- Modal footer -->
                <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b">
                  <button type="submit" class="text-white bg-[#124076] hover:bg-[#255386] font-medium rounded-lg text-sm px-5 py-2.5 text-center">Update</button>
                </div>
                </form>
              </div>
            </div>
          </div>
        </div>
        <div>
          <p class="text-gray-600 font-medium">Address</p>
          <p class="text-gray-800">
            <?php echo htmlspecialchars($user['address']); ?>
          </p>
        </div>
      </section>
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

    <!-- Flowbite CDN JS -->
    <script src="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.js"></script>
  </body>

</html>
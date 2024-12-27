<?php
session_start(); // Mulai session untuk mengambil ID pengguna yang sedang login
require_once('db_connection.php'); // Pastikan Anda menghubungkan ke database

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    echo "Anda harus login terlebih dahulu.";
    exit();
}

$user_id = $_SESSION['user_id']; // Ambil ID pengguna dari session

// Buka koneksi baru untuk pengambilan data
$conn = new mysqli($servername, $username, $password, $dbname);

// Periksa koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data dari tabel items berdasarkan user_id
$sql = "SELECT * FROM items WHERE user_id = $user_id"; // Sesuaikan nama tabel dan kolom
$result = $conn->query($sql);

// Handle request untuk menghapus laporan
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];

    // Cek apakah id valid
    if (is_numeric($delete_id)) {
        // Query untuk menghapus item berdasarkan item_id
        $delete_sql = "DELETE FROM items WHERE id = $delete_id AND user_id = $user_id";
        if ($conn->query($delete_sql) === TRUE) {
            // Redirect ke halaman activity setelah berhasil menghapus
            header('Location: activity.php');
            exit();
        } else {
            echo "Gagal menghapus laporan.";
        }
    }
}

// Ambil notifikasi
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

// Menutup koneksi setelah semua operasi selesai
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Activity</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Playpen+Sans:wght@300;500&display=swap" rel="stylesheet" />
    <!-- Icon -->
    <script src="https://unpkg.com/ionicons@4.5.10-0/dist/ionicons.js"></script>
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/flowbite@2.5.2/dist/flowbite.min.css" rel="stylesheet" />
    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>

<body class="font-[Lato] h-screen">
    <!-- Header Section -->
    <header class="bg-white">
        <nav class="flex justify-between items-center w-[90%] xl:w-[70%] mx-auto">
            <div>
                <img class="mb-3 mt-3 h-[4rem] sm:h-20 cursor-pointer" src="Assets/img/lostnfoundlogo.png" alt="..." />
            </div>
            <div class="nav-links duration-500 md:static absolute bg-white md:min-h-fit min-h-[60vh] left-0 top-[-100%] md:w-auto w-full flex items-center px-2 lg:px-5 z-10 text-xs lg:text-base">
                <ul class="flex md:flex-row flex-col md:items-center md:gap-[4vw] gap-8">
                    <li><a class="hover:text-gray-500" href="user_dashboard.php">Home</a></li>
                    <!-- <li><a class="hover:text-gray-500" href="static_menu.html">Static</a></li> -->
                    <li><a class="hover:text-gray-500" href="message.php">Message</a></li>
                    <li><a class="hover:text-gray-500" href="profile.php">Profile</a></li>
                    <li><a class="hover:text-gray-500 border-b-4 border-[#124076] pb-2" href="activity.php">Activity</a></li>
                    <li><a class="hover:text-gray-500" href="about-us.php">About us</a></li>
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

    <!-- Activity Section -->
    <div class="bg-[#91B0D3] min-h-screen py-10">
        <!-- My Activity Section -->
        <section class="max-w-5xl mx-auto bg-white rounded-md shadow-md p-8">
            <h2 class="text-3xl font-semibold mb-6">My Activity</h2>

            <?php
            // Cek apakah ada laporan untuk ditampilkan
            if ($result->num_rows > 0) {
                // Loop untuk menampilkan data dari tabel items
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='flex items-start justify-between bg-blue-50 p-4 rounded-md mb-4'>";
                    echo "<div class='flex items-center space-x-4'>";
                    echo "<img src='" . $row['photo_path'] . "' alt='" . $row['item_name'] . "' class='w-24 h-24 object-cover rounded' />";
                    echo "<div>";
                    echo "<h3 class='text-xl font-medium'>" . htmlspecialchars($row['item_name']) . "</h3>";
                    echo "<button class='mt-2 mr-4 bg-[#124076] text-white py-1 px-4 rounded hover:bg-[#336fb3]'>Detail Laporan</button>";
                    echo "<button class='mt-2 mr-4 bg-[#124076] text-white py-1 px-4 rounded hover:bg-[#336fb3]'>Ubah Status</button>";
                    // Tombol Hapus
                    echo "<a href='javascript:void(0);' class='mt-2 bg-red-100 text-red-700 py-2 px-4 rounded hover:bg-red-200' onclick='confirmDelete(" . $row['id'] . ")'>Hapus Laporan</a>";
                    echo "</div>";
                    echo "</div>";

                    // Cek apakah kolom 'status' ada sebelum menampilkannya
                    if (isset($row['type'])) {
                        echo "<div class='flex flex-col space-y-2 ml-4'>";
                        echo "<span class='bg-green-100 text-green-700 px-3 py-1 rounded-md text-sm text-center'>" . htmlspecialchars($row['type']) . "</span>";
                        echo "</div>";
                    } else {
                        echo "<div class='flex flex-col space-y-2 ml-4'>";
                        echo "<span class='bg-gray-100 text-gray-700 px-3 py-1 rounded-md text-sm text-center'>Status Tidak Diketahui</span>";
                        echo "</div>";
                    }

                    echo "</div>";
                }
            } else {
                echo "<p>Tidak ada item yang ditemukan.</p>";
            }
            ?>
        </section>
    </div>

    <!-- Footer Section -->
    <footer class="bg-[#124076]">
        <div class="mx-auto w-full max-w-screen-xl p-4 py-6 lg:py-8">
            <div class="md:flex md:justify-between">
                <div class="mb-6 md:mb-0">
                    <a href="#" class="flex items-center">
                        <img src="Assets/img/lostnfoundlogowhite.png" class="h-28 me-3" alt="FlowBite Logo" />
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-8 sm:gap-6 sm:grid-cols-3">
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-white uppercase dark:text-white">About</h2>
                        <ul class="text-white font-medium">
                            <li class="mb-4"><a href="https://flowbite.com/" class="hover:underline">About Lost and Found Items</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Lost and Found Items</h2>
                        <ul class="text-white font-medium">
                            <li class="mb-4"><a href="#" class="hover:underline">Lost Items</a></li>
                            <li><a href="#" class="hover:underline">Found Items</a></li>
                            <li><a href="#" class="hover:underline">Information about Lost and Found Items</a></li>
                        </ul>
                    </div>
                    <div>
                        <h2 class="mb-6 text-sm font-semibold text-gray-900 uppercase dark:text-white">Legal</h2>
                        <ul class="text-white font-medium">
                            <li class="mb-4"><a href="#" class="hover:underline">Feedback</a></li>
                            <li><a href="#" class="hover:underline">Terms & Conditions Lost and Found Items</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <hr class="my-6 border-white sm:mx-auto" />
            <div class="text-center sm:flex sm:items-center sm:justify-between">
                <span class="text-sm text-white text-center">© 2024 <a href="/" class="hover:underline">Lost and Found Team</a>. All Rights Reserved.</span>
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

    <!-- Sweet Alert -->
    <script>
        function confirmDelete(itemId) {
            Swal.fire({
                title: "Ingin menghapus laporan ini?",
                text: "Anda tidak akan dapat mengembalikan ini!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#124076",
                cancelButtonColor: "#B91C1C",
                cancelButtonText: "Batalkan",
                confirmButtonText: "Ya, Hapus!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Redirect ke halaman delete_item.php dengan parameter id
                    window.location.href = "delete_item.php?id=" + itemId;
                }
            });
        }
    </script>

    <script>
        document.getElementById("notification-icon").addEventListener("click", function() {
            const dropdown = document.getElementById("notification-dropdown");
            dropdown.classList.toggle("hidden");
        });

        // Menutup dropdown ketika klik di luar area
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
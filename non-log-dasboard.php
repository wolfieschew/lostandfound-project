<?php
// Koneksi ke database
$host = 'localhost';
$db = 'lost_and_found_items';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Inisialisasi variabel pencarian dan kategori
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Query dengan filter pencarian dan kategori
$sql = "SELECT id, item_name, category, type, description, date_of_event, photo_path FROM items WHERE 1=1";
if (!empty($search)) {
  $sql .= " AND (item_name LIKE '%" . $conn->real_escape_string($search) . "%' OR description LIKE '%" . $conn->real_escape_string($search) . "%')";
}
if (!empty($category)) {
  $sql .= " AND category = '" . $conn->real_escape_string($category) . "'";
}
$sql .= " ORDER BY date_of_event DESC";

// Eksekusi query
$result = $conn->query($sql);

// Cek jika query menghasilkan hasil
if ($result === false) {
  die("Query error: " . $conn->error);
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Search Page | Lost and Found Items</title>
  <!-- CSS -->
  <link rel="stylesheet" href="style/index2.css" />
  <!-- Boxicons CSS -->
  <link
    href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
    rel="stylesheet" />
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Playpen+Sans:wght@300;500&display=swap"
    rel="stylesheet" />
  <!-- Flaticon -->
  <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-solid-straight/css/uicons-solid-straight.css'>
</head>

<body>
  <!-- Header Section -->
  <header>
    <nav>
      <div class="logo">
        <img src="Assets/img/lostnfoundlogo.png" height="70px" />
      </div>
      <ul id="menuList">
        <li><a class="login-btn" href="log_in.php">Log in</a></li>
        <li><a class="sign-up btn" href="sign-up.html">Sign Up</a></li>
      </ul>
      <div class="menu-icon">
        <i class="bx bx-menu" onclick="toggleMenu()"></i>
      </div>
    </nav>
  </header>
  <!-- Header Section -->

  <!-- Search Section -->
  <section>
    <div class="search-content">
      <form method="GET" action="">
        <div class="search-bar">
          <!-- Dropdown untuk kategori -->
          <select id="category" name="category" class="btn-list">
            <option value="">Kategori</option>
            <option value="Elektronik" <?= $category == 'Elektronik' ? 'selected' : '' ?>>Elektronik</option>
            <option value="Buku & Elektronik" <?= $category == 'Buku & Elektronik' ? 'selected' : '' ?>>Buku & Elektronik</option>
            <option value="Aksesori Pribadi" <?= $category == 'Aksesori Pribadi' ? 'selected' : '' ?>>Aksesori Pribadi</option>
            <option value="Peralatan Khusus" <?= $category == 'Peralatan Khusus' ? 'selected' : '' ?>>Perhiasan Khusus</option>
          </select>

          <!-- Input untuk pencarian -->
          <input
            type="text"
            id="search"
            name="search"
            placeholder="Search"
            value="<?= htmlspecialchars($search) ?>" />

          <!-- Tombol Submit -->
          <button type="submit" class="btn btn-search"><i style="font-size: 1.1rem" class='bx bx-search'></i></button>
        </div>
        <p style="text-align: center;">Example: <span style="color: red;">KTM, Dompet, Kunci Motor, Jam Tangan</span></p>
      </form>
    </div>
  </section>

  <!-- Report List Section -->
  <section>
    <div class="test">
      <h2>Newest <span style="color: #124076;">Report</span> Items</h2>
      <div class="card-container" id="cardContainer">
        <?php if ($result->num_rows > 0): ?>
          <div class="card-content">
            <?php while ($row = $result->fetch_assoc()): ?>
              <div class="card">
                <?php
                $type = strtolower(trim($row['type']));
                $statusText = ($type == 'hilang') ? 'Lost' : 'Found';
                $statusClass = ($type == 'hilang') ? 'lost' : 'found';
                ?>
                <span class="status <?= $statusClass ?>">
                  <?= $statusText ?>
                </span>
                <div class="card-image">
                  <?php
                  $imagePath = !empty($row['photo_path']) ? htmlspecialchars($row['photo_path']) : 'images/default-image.jpg';
                  ?>
                  <img src="<?= $imagePath ?>" alt="Report Image" />
                </div>
                <div class="card-details">
                  <h3><?= htmlspecialchars($row['item_name']) ?></h3>
                  <p>
                    <i class="bx bx-calendar-alt calendar-icon"></i>
                    <?= htmlspecialchars($row['date_of_event']) ?>
                  </p>
                  <button class="btn btn-details" onclick="showLoginModal()">Details</button>
                </div>
              </div>
            <?php endwhile; ?>
          </div>
        <?php else: ?>
          <p>No reports found. Try different filters or keywords.</p>
        <?php endif; ?>
      </div>
      <!-- Tombol Show More dan Collapse -->
      <div style="text-align: center; margin-top: 20px;">
        <!-- Tombol Show More -->
        <button id="showMoreBtn" class="btn btn-show-more" onclick="showMore()">
          <i class='bx bx-chevrons-down'></i> Tampilkan Lainnya
        </button>

        <!-- Tombol Collapse -->
        <button id="collapseBtn" class="btn btn-collapse" onclick="collapseCards()" style="display: none;">
          <i class='bx bx-chevrons-up'></i> Tutup
        </button>
      </div>
    </div>
  </section>



  <!-- Modal -->
  <div id="loginModal" class="modal" style="display: none;">
    <div class="modal-content">
      <span class="close-btn" onclick="closeModal()">&times;</span>
      <h3 style="margin-bottom: 1rem ;">Silahkan Login untuk Mengakses Details</h3>
      <p style="margin-bottom: 1rem;">Anda harus login untuk melihat detail laporan ini. Silakan masuk atau daftar untuk melanjutkan.</p>
      <i style="font-size: 100px; color: #124076;" class="fi fi-ss-exclamation"></i>
      <div class="modal-buttons">
        <a href="log_in.php" class="btn btn-login">Login</a>
        <button class="btn btn-cancel" onclick="closeModal()">Cancel</button>
      </div>
    </div>
  </div>



  <!-- Report Section -->
  <section>
    <div class="report-main">
      <div class="report">
        <div class="report-content">
          <h2>Laporkan Barang Anda!</h2>
        </div>
        <div class="report-section">
          <div class="report-btn">
            <a href="log_in.html"><button type="submit">Saya Telah Kehilangan</button></a>
          </div>
          <div class="report-btn">
            <a href="log_in.html"><button type="submit">Saya Telah Menemukan</button></a>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- Report Section -->

  <!-- Footer Section -->
  <section>
    <div class="footer">
      <div class="footer-main">
        <div class="fotter-img">
          <img src="Assets/img/lostnfoundlogowhite.png" height="85px" />
        </div>
      </div>
      <div class="footer-section">
        <div class="footer-content">
          <h3>About</h3>
          <ul>
            <li>About Lost and Found Items</li>
            <li>Feedback</li>
            <li>Terms and Condition Lost And Found Items</li>
          </ul>
        </div>
        <div class="footer-content">
          <h3>Lost and Found Items</h3>
          <ul>
            <li>Lost Items</li>
            <li>Found Items</li>
            <li>Information About Lost and Found Items</li>
          </ul>
        </div>
      </div>
    </div>
  </section>
  <!-- Footer Section -->

  <script>
    let menuList = document.getElementById("menuList");
    menuList.style.maxHeight = "0px";

    function toggleMenu() {
      if (menuList.style.maxHeight == "0px") {
        menuList.style.maxHeight = "300px";
      } else {
        menuList.style.maxHeight = "0px";
      }
    }
  </script>

  <script>
    document.addEventListener("DOMContentLoaded", () => {
      const cards = document.querySelectorAll(".card");
      const cardContainer = document.getElementById("cardContainer");
      const showMoreBtn = document.getElementById("showMoreBtn");
      const collapseBtn = document.getElementById("collapseBtn");

      function updateVisibleCards() {
        const isResponsive = window.innerWidth <= 768;
        const maxVisible = isResponsive ? 2 : 8; // Maksimal card: 2 untuk responsif, 8 untuk desktop

        // Sembunyikan card yang melebihi batas
        cards.forEach((card, index) => {
          card.style.display = index < maxVisible ? "block" : "none";
        });

        // Atur tinggi container hanya pada mode responsif
        if (isResponsive) {
          const cardHeight = 220; // Tinggi rata-rata card pada responsif
          const padding = 20; // Tambahkan ruang ekstra untuk memastikan tidak ada yang terpotong
          const visibleCards = Math.min(cards.length, maxVisible);
          cardContainer.style.height = `${visibleCards * cardHeight + padding}px`; // Atur tinggi container dengan padding tambahan
        } else {
          cardContainer.style.height = "auto"; // Kembali ke auto di desktop
        }

        // Reset tombol
        collapseBtn.style.display = "none"; // Sembunyikan tombol "Collapse"
        showMoreBtn.style.display = cards.length > maxVisible ? "inline-block" : "none"; // Tampilkan tombol "Show More" hanya jika ada lebih banyak card
      }

      // Fungsi untuk menampilkan semua card
      window.showMore = function() {
        cards.forEach((card) => {
          card.style.display = "block"; // Tampilkan semua card
        });

        // Tambahkan tinggi container hanya di mode responsif
        if (window.innerWidth <= 768) {
          const cardHeight = 220; // Tinggi rata-rata card pada responsif
          const padding = 20; // Tambahkan padding ekstra
          cardContainer.style.height = `${cards.length * cardHeight + padding}px`;
        }

        showMoreBtn.style.display = "none"; // Sembunyikan tombol "Show More"
        collapseBtn.style.display = "inline-block"; // Tampilkan tombol "Collapse"
      };

      // Fungsi untuk menyembunyikan kembali card yang melebihi batas
      window.collapseCards = function() {
        updateVisibleCards(); // Reset ke jumlah card sesuai ukuran layar
      };

      // Perbarui card yang terlihat saat halaman dimuat
      updateVisibleCards();

      // Tambahkan event listener untuk resize
      window.addEventListener("resize", updateVisibleCards);
    });
  </script>

  <script>
    function showLoginModal() {
      // Display the modal
      document.getElementById('loginModal').style.display = 'flex';
    }

    function closeModal() {
      // Hide the modal
      document.getElementById('loginModal').style.display = 'none';
    }
  </script>


  <script
    src="https://kit.fontawesome.com/f8e1a90484.js"
    crossorigin="anonymous"></script>
</body>

</html>
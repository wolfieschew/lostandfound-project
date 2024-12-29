<?php
session_start();
$error = isset($_SESSION['error']) ? $_SESSION['error'] : '';
unset($_SESSION['error']); // Hapus pesan setelah ditampilkan
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Log-in</title>
  <link rel="stylesheet" href="style/login.css" />
  <!-- Google Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Caveat:wght@700&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Playpen+Sans:wght@300;500&display=swap"
    rel="stylesheet" />
  <!-- BX BX ICONS -->
  <link
    href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css"
    rel="stylesheet" />
</head>

<body>
  <section>
    <div class="sign-in section">
      <div class="logo-img">
        <img
          src="Assets/img/lostnnfoundtransprnt.png"
          height="80px"
          alt="Logo" />
      </div>
      <!-- Bagian kanan -->
      <div class="login-content">
        <div class="login-container">
          <div class="login-welcome">
            <h1>Welcome</h1>
            <p>Welcome to Lost and Found Items. Find your lost items here.</p>
          </div>
          <form action="process_login.php" method="post">
            <input
              class="input-field"
              name="email"
              id="email"
              type="text"
              placeholder="Enter your Email"
              required />
            <br />
            <div class="pwd-btn">
              <input
                class="input-field"
                name="password"
                id="password"
                type="password"
                placeholder="Enter your Password"
                required />
              <!-- <i class="bx bx-low-vision"></i> -->
            </div>
            <div class="options">
              <label><input type="checkbox" name="remember_me" style="cursor: pointer" />remember
                me</label>
              <a href="forgot_password.html">Forgot password</a>
            </div>
            <button class="btn-submit" type="submit" style="cursor: pointer">
              Sign in
            </button>
          </form>
          <div class="sign-up btn">
            <p>
              Don't have an account?
              <a href="sign-up.html" style="cursor: pointer">Sign up here</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <?php if (!empty($error)): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      Swal.fire({
        title: 'Error!',
        text: "<?= htmlspecialchars($error); ?>",
        icon: 'error',
        confirmButtonText: 'OK',
        customClass: {
          confirmButton: 'confirm-button'
        }
      });
    </script>

  <?php endif; ?>

</body>

</html>
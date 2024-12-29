<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Reset-Password</title>
  <link rel="stylesheet" href="style/reset_password.css" />
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
            <h1>Create a new Password</h1>
            <p>Use Unique and Easy to Remember Password</p>
          </div>
          <form id="resetPasswordForm" onsubmit="resetPassword(event)">
            <input
              class="input-field"
              type="password"
              id="new_password"
              placeholder="Enter your new Password"
              required />
            <input
              class="input-field"
              type="password"
              id="confirm_password"
              placeholder="Confirm your new Password"
              required />
            <br />
            <button class="btn-submit" type="submit">Update Password</button>
          </form>

          <div class="sign-up btn">
            <p>
              Don't have an account? <a href="sign-up.html">Sign up here</a>
            </p>
          </div>
        </div>
      </div>
    </div>
  </section>
  <!-- SweetAlert -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    function resetPassword(event) {
      event.preventDefault(); // Mencegah pengiriman formulir secara default

      const urlParams = new URLSearchParams(window.location.search);
      const email = urlParams.get("email"); // Ambil email dari parameter URL

      const newPassword = document.getElementById("new_password").value;
      const confirmPassword = document.getElementById("confirm_password").value;

      // Validasi kesamaan password
      if (newPassword !== confirmPassword) {
        Swal.fire({
          icon: "error",
          title: "Passwords Do Not Match",
          text: "Please ensure both passwords are the same.",
          confirmButtonText: "OK",
          customClass: {
            confirmButton: "custom-gray",
          },
        });
        return;
      }

      // Kirim permintaan ke server untuk memperbarui password
      fetch("update_password.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            email: email,
            password: newPassword,
          }),
        })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            Swal.fire({
              icon: "success",
              title: "Password Updated",
              text: "Your password has been successfully updated. You can now log in.",
              confirmButtonText: "OK",
              customClass: {
                confirmButton: "custom-gray",
              },
            }).then(() => {
              window.location.href = "log_in.php"; // Redirect ke halaman login
            });
          } else {
            Swal.fire({
              icon: "error",
              title: "Update Failed",
              text: "Failed to update password. Please try again.",
              confirmButtonText: "OK",
              customClass: {
                confirmButton: "custom-gray",
              },
            });
          }
        })
        .catch(() => {
          Swal.fire({
            icon: "error",
            title: "Error",
            text: "An error occurred. Please try again later.",
            confirmButtonText: "OK",
            customClass: {
              confirmButton: "custom-gray-button",
            },
          });
        });
    }
  </script>
</body>

</html>
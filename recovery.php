<?php
include 'components/connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? '';

// Function to validate the password
function validatePassword($password) {
    return (
        strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[^A-Za-z0-9]/', $password)
    );
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message[] = 'Invalid email format';
    } else {
        if ($newPassword !== $confirmPassword) {
            $message[] = 'Confirm password does not match.';
        } elseif (!validatePassword($newPassword)) {
            $message[] = 'Password must be at least 8 characters and include uppercase, lowercase, number, and symbol.';
        } else {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

            $update_password = $conn->prepare("UPDATE [users] SET [password] = ? WHERE [email] = ?");
            $update_password->execute([$hashedPassword, $email]);

            if ($update_password->rowCount() > 0) {
                $successMessage = 'Password updated successfully. You can now log in.';
            } else {
                $message[] = 'Email not found or no changes made.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Password Recovery</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="icon" href="images/favicon.ico">
   <link rel="stylesheet" href="css/style.css">

   <style>
      .password-container {
         position: relative;
      }
      .eye-icon {
         position: absolute;
         top: 50%;
         right: 10px;
         transform: translateY(-50%);
         cursor: pointer;
         font-size: 20px;
         color: black;
      }
      #scrollToTopBtn {
         position: fixed;
         bottom: 20px;
         right: 20px;
         background: #222;
         color: white;
         padding: 0.5rem 1rem;
         font-size: 1.5rem;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         display: none;
         z-index: 1000;
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<br><br><br><br><br><br><br>

<section class="form-container">
   <form action="" method="post">
      <h3>Password Recovery</h3>
      <input type="email" name="email" value="<?= isset($email) ? htmlspecialchars($email) : ''; ?>" required placeholder="Email Address" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      
      <div class="password-container">
         <input type="password" name="new_password" required placeholder="New Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <span class="eye-icon" onclick="togglePassword('new_password')"><i class="fas fa-eye" id="eye-icon-new_password"></i></span>
      </div>
      
      <div class="password-container">
         <input type="password" name="confirm_password" required placeholder="Confirm Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <span class="eye-icon" onclick="togglePassword('confirm_password')"><i class="fas fa-eye" id="eye-icon-confirm_password"></i></span>
      </div>

      <input type="submit" value="Submit" name="submit" class="btn">
      <a href="login.php" class="btn back-btn">Back</a>
   </form>
</section>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo "<script>swal({ title: 'Error', text: '$msg', icon: 'warning', button: 'Close' });</script>";
   }
}

if (isset($successMessage)) {
   echo "<script>swal({ title: 'Success', text: '$successMessage', icon: 'success', button: 'Close' }).then(() => { window.location.href = 'login.php'; });</script>";
}
?>

<div class="loader">
   <img src="images/loader.gif" alt="Loading...">
</div>

<script src="js/script.js"></script>
<script>
   function togglePassword(inputName) {
      const input = document.getElementsByName(inputName)[0];
      const icon = document.getElementById('eye-icon-' + inputName);
      if (input.type === "password") {
         input.type = "text";
         icon.classList.replace('fa-eye', 'fa-eye-slash');
      } else {
         input.type = "password";
         icon.classList.replace('fa-eye-slash', 'fa-eye');
      }
   }

   const scrollBtn = document.getElementById("scrollToTopBtn");
   window.onscroll = () => {
      scrollBtn.style.display = window.scrollY > 100 ? "block" : "none";
   };
   scrollBtn.onclick = () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
   };
</script>

</body>
</html>

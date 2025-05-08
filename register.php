<?php
include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

if (isset($_SESSION['user_id'])) {
   header('Location: home.php');
   exit;
}

function validatePassword($password) {
   return (
      strlen($password) >= 8 &&
      preg_match('/[A-Z]/', $password) &&
      preg_match('/[a-z]/', $password) &&
      preg_match('/[0-9]/', $password) &&
      preg_match('/[^A-Za-z0-9]/', $password)
   );
}

function validatePhoneNumber($phoneNumber) {
   $numericPhoneNumber = preg_replace('/\D/', '', $phoneNumber);
   return strlen($numericPhoneNumber) === 11;
}

if (isset($_POST['submit'])) {
   $fname = $_POST['fname'];
   $mname = $_POST['mname'];
   $lname = $_POST['lname'];
   $email = $_POST['email'];
   $number = $_POST['number'];
   $password = $_POST['pass'];
   $confirmPassword = $_POST['cpass'];
   $address = 'To be updated';

   if ($password !== $confirmPassword) {
      $message[] = 'Password is not matched.';
   } elseif (!validatePassword($password)) {
      $message[] = 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.';
   } elseif (!validatePhoneNumber($number)) {
      $message[] = 'Phone number must have exactly 11 digits.';
   } else {
      $check_email = $conn->prepare("SELECT * FROM [users] WHERE email = ?");
      $check_email->execute([$email]);

      $check_number = $conn->prepare("SELECT * FROM [users] WHERE number = ?");
      $check_number->execute([$number]);

      if ($check_email->fetch(PDO::FETCH_ASSOC)) {
         $message[] = 'Email is already registered.';
      } elseif ($check_number->fetch(PDO::FETCH_ASSOC)) {
         $message[] = 'Phone number is already used.';
      } else {
         $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

         $insert_user = $conn->prepare("INSERT INTO [users] (fname, mname, lname, email, number, password, address) VALUES (?, ?, ?, ?, ?, ?, ?)");
         $insert_user->execute([$fname, $mname, $lname, $email, $number, $hashedPassword, $address]);

         $select_user = $conn->prepare("SELECT * FROM [users] WHERE email = ?");
         $select_user->execute([$email]);
         $row = $select_user->fetch(PDO::FETCH_ASSOC);

         if ($row) {
            $_SESSION['user_id'] = $row['id'];
            header('Location: home.php');
            exit;
         } else {
            $message[] = 'User creation failed. Please try again.';
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Sign up</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="icon" href="images/favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="css/style.css">
   <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>
   <style>
      .password-container { position: relative; }
      .eye-icon {
         position: absolute;
         top: 50%;
         right: 10px;
         transform: translateY(-50%);
         cursor: pointer;
         font-size: 20px;
         color: black;
      }
      .highlight { border: 2px solid red; }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container">
   <form action="" method="post" id="myForm">
      <br>
      <h3>Sign up</h3>
      <input type="text" name="fname" value="<?= htmlspecialchars($fname ?? '') ?>" required placeholder="First Name" class="box" maxlength="50">
      <input type="text" name="mname" value="<?= htmlspecialchars($mname ?? '') ?>" required placeholder="Middle Name" class="box" maxlength="50">
      <input type="text" name="lname" value="<?= htmlspecialchars($lname ?? '') ?>" required placeholder="Last Name" class="box" maxlength="50">
      <input type="email" name="email" value="<?= htmlspecialchars($email ?? '') ?>" required placeholder="Email Address" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <input type="number" name="number" value="<?= htmlspecialchars($number ?? '') ?>" required placeholder="Phone Number" class="box" oninput="if(this.value.length > 11) this.value = this.value.slice(0, 11);">

      <div class="password-container">
         <input type="password" name="pass" required placeholder="Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <span class="eye-icon" onclick="togglePassword('pass')">
            <i class="fas fa-eye" id="eye-icon-pass"></i>
         </span>
      </div>
      <div class="password-container">
         <input type="password" name="cpass" required placeholder="Confirm Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <span class="eye-icon" onclick="togglePassword('cpass')">
            <i class="fas fa-eye" id="eye-icon-cpass"></i>
         </span>
      </div>
      <input type="submit" value="Register" name="submit" class="btn">
      <p>Already have an account? <a href="login.php" style="text-decoration: underline;">Log in</a></p>
   </form>
</section>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo '
         <script>
            swal({
               title: "Error",
               text: "'.htmlspecialchars($msg).'",
               icon: "warning",
               button: "Close",
            });
         </script>
      ';
   }
}
?>

<div class="loader">
   <img src="images/loader.gif" alt="">
</div>

<script src="js/script.js"></script>

<script>
function togglePassword(inputId) {
   var passwordInput = document.getElementsByName(inputId)[0];
   var eyeIcon = document.getElementById('eye-icon-' + inputId);
   var form = document.getElementById('myForm');

   if (passwordInput.type === "password") {
      passwordInput.type = "text";
      eyeIcon.classList.remove('fa-eye');
      eyeIcon.classList.add('fa-eye-slash');
      form.classList.add('highlight');
   } else {
      passwordInput.type = "password";
      eyeIcon.classList.remove('fa-eye-slash');
      eyeIcon.classList.add('fa-eye');
      form.classList.remove('highlight');
   }
}
</script>

</body>
</html>

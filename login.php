<?php
include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

if (isset($_SESSION['user_id'])) {
   header('Location: home.php');
   exit;
}

if (isset($_POST['submit'])) {
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $password = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);

   $select_user = $conn->prepare("SELECT * FROM [users] WHERE email = ?");
   $select_user->execute([$email]);
   $row = $select_user->fetch(PDO::FETCH_ASSOC);

   if ($row && password_verify($password, $row['password'])) {
      $_SESSION['user_id'] = $row['id'];
      header('Location: home.php');
      exit;
   } else {
      $message[] = $row ? 'You have entered the wrong password.' : 'You are not yet registered. Please sign up.';
   }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Log in</title>
   <link rel="icon" href="images/favicon.ico" type="image/x-icon">


   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>



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
         font-size: 20px; /* Adjust the size as needed */
         color: black; /* Adjust the color as needed */
      }
   </style>
</head>
<body>
   
<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<section class="form-container">
<br><br><br><br><br>
   <form action="" method="post">
      <h3>Log in</h3>
      <input type="email" name="email" value="<?php echo isset($email) ? $email : ''; ?>" required placeholder="Email Address" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
      <div class="password-container">
         <input type="password" name="pass" required placeholder="Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
         <span class="eye-icon" onclick="togglePassword('pass')">
            <i class="fas fa-eye" id="eye-icon-pass"></i>
         </span>
      </div>
      <input type="submit" value="Log in" name="submit" class="btn">
      <p>Don't have an account? <a href="register.php" style="text-decoration: underline;">Sign up</a></p>
     
      <p>Forgot your password? <a href="recovery.php" style="text-decoration: underline;">Recover</a></p>
   </form>
</section>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
         <script>
            swal({
               title: "Please try again",
               text: "'.$message.'",
               icon: "error",
               button: "Okay",
            });
         </script>
      ';
   }
}
?>

<div class="loader">
   <img src="images/loader.gif" alt="">
</div>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>
   function togglePassword(inputId) {
      var passwordInput = document.getElementsByName(inputId)[0];
      var eyeIcon = document.getElementById('eye-icon-' + inputId);

      if (passwordInput.type === "password") {
         passwordInput.type = "text";
         eyeIcon.classList.remove('fa-eye');
         eyeIcon.classList.add('fa-eye-slash');
      } else {
         passwordInput.type = "password";
         eyeIcon.classList.remove('fa-eye-slash');
         eyeIcon.classList.add('fa-eye');
      }
   }
</script>

<style>
   /* Base styles for all screen sizes */
body {
  font-family: 'Arial', sans-serif;
  margin: 0;
  padding: 0;
}

.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

/* Responsive styles using media queries */

/* For screens smaller than 600px */
@media only screen and (max-width: 600px) {
  body {
    font-size: 14px;
  }

  .container {
    padding: 10px;
  }
}

/* For screens between 600px and 900px */
@media only screen and (min-width: 601px) and (max-width: 900px) {
  body {
    font-size: 16px;
  }

  .container {
    padding: 15px;
  }
}

/* For screens larger than 900px */
@media only screen and (min-width: 901px) {
  body {
    font-size: 18px;
  }

  .container {
    padding: 20px;
  }
}
</style>

</body>
</html>

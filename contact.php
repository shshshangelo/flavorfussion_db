<?php
include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];

   $select_user = $conn->prepare("SELECT fname, mname, lname, email, number FROM [users] WHERE id = ?");
   $select_user->execute([$user_id]);
   $user_info = $select_user->fetch(PDO::FETCH_ASSOC);

   $fname = $user_info['fname'];
   $mname = $user_info['mname'];
   $lname = $user_info['lname'];
   $email = $user_info['email'];
   $number = $user_info['number'];
} else {
   $user_id = '';
   $fname = $mname = $lname = $email = $number = '';
}

$messageSent = false;

if (isset($_POST['send'])) {
   $fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
   $mname = filter_var($_POST['mname'], FILTER_SANITIZE_STRING);
   $lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $msg = filter_var($_POST['msg'], FILTER_SANITIZE_STRING);
   $rating = filter_var($_POST['rating'], FILTER_SANITIZE_STRING);

   $select_message = $conn->prepare("SELECT * FROM [messages] WHERE fname = ? AND mname = ? AND lname = ? AND email = ? AND number = ? AND message = ? AND rating = ?");
   $select_message->execute([$fname, $mname, $lname, $email, $number, $msg, $rating]);

   if ($select_message->fetch(PDO::FETCH_ASSOC)) {
      $message[] = 'Already sent message.';
   } else {
      $insert_message = $conn->prepare("INSERT INTO [messages] (user_id, fname, mname, lname, email, number, message, rating) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
      $insert_message->execute([$user_id, $fname, $mname, $lname, $email, $number, $msg, $rating]);
      $messageSent = true;
   }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Feedbacks</title>

   <!-- SweetAlert CDN link -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

   <link rel="icon" href="images/favicon.ico" type="image/x-icon">


   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>

</head>
<body>

<!-- header section starts  -->
<?php include 'components/user_header.php'; ?>
<!-- header section ends -->

<div class="heading">
   <h3>Feedbacks</h3>
   <p><a href="home.php">Home</a> <span> / Feedbacks</span></p>
</div>

<style>
.my-custom-sweetalert {
   font-size: 16px; /* Adjust the font size as needed */
}

.experience-label {
   margin-top: 10px;
   font-size: 30px;
   color: #fff;
}

#rating {
   padding: 8px;
   font-size: 20px;
   border: 1px solid #ccc;
   border-radius: 4px;
   margin-bottom: 15px;
}

</style>

<!-- contact section starts  -->
<section class="contact">
   <div class="row">
      <div class="image">
         <img src="images/contact-img.svg" alt="">
      </div>
      <form action="" method="post">
         <h3>"We hope you're enjoying your meal! How's our food so far?"</h3>
         <!-- Pre-fill the form fields with user information -->
         <input type="text" name="fname" maxlength="50" class="box" placeholder="First Name" required value="<?php echo $fname; ?>" readonly>
         <input type="text" name="mname" maxlength="50" class="box" placeholder="Middle Name" required value="<?php echo $mname; ?>" readonly>
         <input type="text" name="lname" maxlength="50" class="box" placeholder="Last Name" required value="<?php echo $lname; ?>" readonly>
         <input type="number" name="number" min="0" max="9999999999" class="box" placeholder="Phone Number" required maxlength="11" value="<?php echo $number; ?>" readonly>
         <input type="email" name="email" maxlength="50" class="box" placeholder="Email Address" required value="<?php echo $email; ?>" readonly>
         <textarea name="msg" class="box" required placeholder="Enter your message" maxlength="500" cols="30" rows="10"></textarea>

         <!-- Add a styled "Rate your experience" label and select dropdown for the rating -->
         <label for="rating" class="experience-label">Rate your experience: </label>
         <select id="rating" name="rating" required>
            <option value="5">5 stars</option>
            <option value="4">4 stars</option>
            <option value="3">3 stars</option>
            <option value="2">2 stars</option>
            <option value="1">1 star</option>
         </select>
         <br>
         <br>
         <input type="submit" value="Submit" name="send" class="btn">
      </form>
   </div>
</section>
<!-- contact section ends -->

<!-- SweetAlert CDN link -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

<script>
   // Display SweetAlert if the message was sent successfully
   document.addEventListener('DOMContentLoaded', function () {
      <?php
      if ($messageSent) {
         echo "Swal.fire({
            title: 'Thank you!',
            text: 'Your message has been sent successfully. We appreciate your message.',
            icon: 'success',
            confirmButtonText: 'OK',
            customClass: {
               popup: 'my-custom-sweetalert'
            }
         });";
      }
      ?>
   });
</script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

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

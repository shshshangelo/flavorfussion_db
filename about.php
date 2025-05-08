<?php
include 'components/connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>About Us</title>

   <!-- External CSS -->
   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />
   <link rel="icon" href="images/favicon.ico" type="image/x-icon" />
   <link rel="stylesheet" href="css/style.css" />

   <!-- Scroll to top -->
   <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>About Us</h3>
   <p><a href="home.php">Home</a> <span> / About Us</span></p>
</div>

<section class="about">
   <div class="row">
      <div class="image">
         <img src="images/about-img.svg" alt="About Image" />
      </div>
      <div class="content">
         <h3>Why FlavorFussion Eats?</h3>
         <p>Our online food ordering system seamlessly connects you to a world of flavors. Savor the convenience, relish the choices, and let your cravings be the guide. Welcome to a feast of simplicity – where every click brings you closer to a delightful dining experience. Order, eat, repeat!</p>
         <a href="menu.php" class="btn">Our Menu</a>
      </div>
   </div>
</section>

<h1 class="title">About Us</h1>

<?php include 'components/footer.php'; ?>

<section class="steps">
   <h1 class="title">Simple Steps to Order</h1>
   <div class="box-container">
      <div class="box">
         <img src="images/step-1.png" alt="Step 1" />
         <h3>Choose Menu</h3>
         <p>"Explore a culinary journey where every dish tells a story, and every bite is a moment to savor."</p>
      </div>
      <div class="box">
         <img src="images/step-2.png" alt="Step 2" />
         <h3>Fast Delivery</h3>
         <p>"From our kitchen to your doorstep in a heartbeat. Swift, reliable, and deliciously quick."</p>
      </div>
      <div class="box">
         <img src="images/step-3.png" alt="Step 3" />
         <h3>Enjoy</h3>
         <p>"Sit back, relax, and savor the moment. Your favorite flavors delivered, just as you imagined. Bon appétit!"</p>
      </div>
   </div>
</section>

<style>
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

@media only screen and (max-width: 600px) {
  body { font-size: 14px; }
  .container { padding: 10px; }
}

@media only screen and (min-width: 601px) and (max-width: 900px) {
  body { font-size: 16px; }
  .container { padding: 15px; }
}

@media only screen and (min-width: 901px) {
  body { font-size: 18px; }
  .container { padding: 20px; }
}
</style>

<script src="js/script.js"></script>
</body>
</html>

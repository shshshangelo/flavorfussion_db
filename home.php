<?php
include 'components/connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? '';

include 'components/add_cart.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>
   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="icon" href="images/favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <style>
      .swal-wide {
         width: 600px !important;
         padding: 40px 30px !important;
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<button id="scrollToTopBtn" aria-label="Scroll to Top">▲</button>

<section class="hero">
   <div class="swiper hero-slider">
      <div class="swiper-wrapper">
         <?php
         $slides = [
            ["title" => "Parisenne Sky", "desc" => "Melted perfection on a crispy crust.", "img" => "home-img-1.png"],
            ["title" => "Cluck Deluxe", "desc" => "Bite into bliss. Stacked with flavor.", "img" => "home-img-2.png"],
            ["title" => "Berry Delight", "desc" => "Sip, Smile, Repeat.", "img" => "home-img-3.png"],
            ["title" => "Pizza Reine", "desc" => "Baked to perfection.", "img" => "home-img-4.png"],
            ["title" => "Cocoa Muffin", "desc" => "Cupcakes are delightful treats.", "img" => "home-img-5.png"],
            ["title" => "Orange Juice", "desc" => "Bursting with natural sweetness.", "img" => "home-img-6.png"]
         ];
         foreach ($slides as $slide) {
         ?>
         <div class="swiper-slide slide">
            <div class="content">
               <span><?= $slide['desc']; ?></span>
               <h3><?= $slide['title']; ?></h3>
               <a href="menu.php" class="btn">Order Now</a>
            </div>
            <div class="image">
               <img src="images/<?= $slide['img']; ?>" alt="">
            </div>
         </div>
         <?php } ?>
      </div>
      <div class="swiper-pagination"></div>
   </div>
</section>

<section class="category">
   <h1 class="title">Food Categories</h1>
   <div class="box-container">
      <a href="category.php?category=Starter Packs" class="box">
         <img src="images/cat-1.png" alt="">
         <h3>Starter Packs</h3>
      </a>
      <a href="category.php?category=Main Dishes" class="box">
         <img src="images/cat-2.png" alt="">
         <h3>Main Dishes</h3>
      </a>
      <a href="category.php?category=Desserts" class="box">
         <img src="images/cat-4.png" alt="">
         <h3>Desserts</h3>
      </a>
      <a href="category.php?category=Drinks" class="box">
         <img src="images/cat-3.png" alt="">
         <h3>Drinks</h3>
      </a>
   </div>
</section>

<section class="products">
   <h1 class="title">Latest Menu</h1>
   <div class="box-container">
      <?php
      $select_products = $conn->prepare("SELECT TOP 12 * FROM [products] ORDER BY [id] DESC");
      $select_products->execute();
      while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
      ?>
         <form action="" method="post" class="box">
            <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
            <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
            <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
            <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
            <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
            <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
            <a href="category.php?category=<?= urlencode($fetch_products['category']); ?>" class="cat"><?= $fetch_products['category']; ?></a>
            <div class="name"><?= $fetch_products['name']; ?></div>
            <div class="flex">
               <div class="price"><span>₱</span><?= number_format($fetch_products['price'], 2); ?></div>
               <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
            </div>
         </form>
      <?php } ?>
   </div>
   <div class="more-btn">
      <a href="menu.php" class="btn">View All Menu</a>
   </div>
</section>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="js/script.js"></script>

<script>
   new Swiper(".hero-slider", {
      loop: true,
      grabCursor: true,
      effect: "flip",
      pagination: { el: ".swiper-pagination", clickable: true },
      autoplay: { delay: 4500, disableOnInteraction: false }
   });

   document.addEventListener('DOMContentLoaded', function () {
      document.querySelectorAll('.fa-shopping-cart').forEach(button => {
         button.addEventListener('click', event => {
            if ('<?= $user_id ?>' === '') {
               event.preventDefault();
               Swal.fire({
                  icon: 'warning',
                  title: '<span style="font-size: 28px;">Login Required</span>',
                  html: '<p style="font-size: 20px;">Please login to add items to your cart.</p>',
                  confirmButtonText: '<span style="font-size: 18px; padding: 6px 16px;">OK</span>',
                  customClass: { popup: 'swal-wide' }
               }).then(() => {
                  window.location.href = 'login.php';
               });
            }
         });
      });
   });
</script>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo "<script>
         Swal.fire({
            icon: 'success',
            title: '<span style=\"font-size: 32px;\">Success</span>',
            html: '<p style=\"font-size: 22px;\">$msg</p>',
            confirmButtonText: '<span style=\"font-size: 20px; padding: 10px 30px;\">OK</span>',
            customClass: { popup: 'swal-wide' }
         });
      </script>";
   }
}
if (isset($message1)) {
   foreach ($message1 as $msg) {
      echo "<script>
         Swal.fire({
            icon: 'warning',
            title: '<span style=\"font-size: 28px;\">Notice</span>',
            html: '<p style=\"font-size: 20px;\">$msg</p>',
            confirmButtonText: '<span style=\"font-size: 18px; padding: 8px 20px;\">OK</span>',
            customClass: { popup: 'swal-wide' }
         });
      </script>";
   }
}
?>

<div class="loader">
   <img src="images/loader.gif" alt="Loading...">
</div>

</body>
</html>

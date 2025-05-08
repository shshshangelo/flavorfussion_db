<?php
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
};

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

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <link rel="icon" href="images/favicon.ico" type="image/x-icon">


   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php include 'components/user_header.php'; ?>



<section class="hero">

   <div class="swiper hero-slider">

      <div class="swiper-wrapper">

         <div class="swiper-slide slide">
            <div class="content">
            <span>"Melted perfection on a crispy crust. Your pizza, your way, every slice tells a story."</span>
               <h3>Cheesy Onion Pizza</h3>
               <a href="menu.php" class="btn">Order Now</a>
            </div>
            <div class="image">
               <img src="images/home-img-1.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>"Bite into bliss. Our burgers are crafted with love, stacked with flavor, and served just the way you like."</span>
               <h3>Cluck Deluxe</h3>
               <a href="menu.php" class="btn">Order Now</a>
            </div>
            <div class="image">
               <img src="images/home-img-2.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>"Sip, Smile, Repeat. Dive into a world of refreshing goodness with our vibrant and delicious smoothies."</span>
               <h3>Berry Delight</h3>
               <a href="menu.php" class="btn">Order Now</a>
            </div>
            <div class="image">
               <img src="images/home-img-3.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>"Baked to perfection, it transforms into a slice of comfort that satisfies cravings and fuels gatherings."</span>
               <h3>Mushroom Pizza</h3>
               <a href="menu.php" class="btn">Order Now</a>
            </div>
            <div class="image">
               <img src="images/home-img-4.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>"Cupcakes are delightful, miniature treats that bring joy with every bite."</span>
               <h3>Cocoa Muffin</h3>
               <a href="menu.php" class="btn">Order Now</a>
            </div>
            <div class="image">
               <img src="images/home-img-5.png" alt="">
            </div>
         </div>

         <div class="swiper-slide slide">
            <div class="content">
               <span>"Bursting with natural sweetness and a hint of tanginess, it's a popular choice for a revitalizing start to the day or a refreshing pick-me-up. "</span>
               <h3>Orange Juice</h3>
               <a href="menu.php" class="btn">Order Now</a>
            </div>
            <div class="image">
               <img src="images/home-img-6.png" alt="">
            </div>
         </div>

      </div>

      <div class="swiper-pagination"></div>

   </div>

</section>


<section class="category">

   <h1 class="title">Food Categories</h1>

   <div class="box-container">

      <a href="category.php?category=starter packs" class="box">
         <img src="images/cat-1.png" alt="">
         <h3>Starter Packs</h3>
      </a>

      <a href="category.php?category=main dishes" class="box">
         <img src="images/cat-2.png" alt="">
         <h3>Main Dishes</h3>
      </a>

      <a href="category.php?category=desserts" class="box">
         <img src="images/cat-4.png" alt="">
         <h3>Desserts</h3>
      </a>
	  
      <a href="category.php?category=drinks" class="box">
         <img src="images/cat-3.png" alt="">
         <h3>Drinks</h3>
      </a>

   </div>
</section>

<section class="products">
    <h1 class="title">Latest Menu</h1>
    <div class="box-container">
        <?php
        $select_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC LIMIT 12");
        $select_products->execute();

        if ($select_products->rowCount() > 0) {
            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
        ?>
                <form action="" method="post" class="box">
                    <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
                    <input type="hidden" name="name" value="<?= $fetch_products['name']; ?>">
                    <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
                    <input type="hidden" name="image" value="<?= $fetch_products['image']; ?>">
                    <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
                    <img src="uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                    <a href="category.php?category=<?= $fetch_products['category']; ?>" class="cat"><?= $fetch_products['category']; ?></a>
                    <div class="name"><?= $fetch_products['name']; ?></div>
                    <div class="flex">
                        <div class="price" id="price<?= $fetch_products['id']; ?>"><span>₱</span><?= $fetch_products['price']; ?></div>
                        <input type="number" name="qty" class="qty" min="1" max="99" value="1" maxlength="2">
                    </div>
                </form>
        <?php
            }
        } else {
            echo '<p class="empty">No new dishes added yet.</p>';
        }
        ?>
    </div>
</section>


<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>

<!-- custom js file link  -->
<script src="js/script.js"></script>

<script>
var swiper = new Swiper(".hero-slider", {
   loop:true,
   grabCursor: true,
   effect: "flip",
   pagination: {
      el: ".swiper-pagination",
      clickable:true,
   },
});

</script>

<!-- Add this JavaScript at the end of your HTML body or in a separate script file -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    var quantityInputs = document.querySelectorAll('.qty');

    quantityInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            var quantity = this.value;
            var unitPrice = parseFloat(this.closest('.box').querySelector('[name="price"]').value);
            var totalPrice = quantity * unitPrice;
            var priceElement = this.closest('.box').querySelector('.price');
            priceElement.innerHTML = '<span>₱</span>' + totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    });
});

</script>

<div class="loader">
   <img src="images/loader.gif" alt="">
</div>

</body>
</html>
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
   <title>Category</title>
   <link rel="icon" href="images/favicon.ico" type="image/x-icon">

   <!-- Bootstrap & Dependencies -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>

   <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <!-- Font Awesome -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- Custom CSS -->
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

<section class="category">
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
   <div class="box-container">
      <?php
      if (isset($_GET['category'])) {
         $category = $_GET['category'];
         $select_products = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY id DESC");
         $select_products->execute([$category]);
         $products = $select_products->fetchAll(PDO::FETCH_ASSOC);

         if (!empty($products)) {
            foreach ($products as $fetch_products) {
      ?>
      <form action="" method="post" class="box">
         <input type="hidden" name="pid" value="<?= $fetch_products['id']; ?>">
         <input type="hidden" name="name" value="<?= htmlspecialchars($fetch_products['name']); ?>">
         <input type="hidden" name="price" value="<?= $fetch_products['price']; ?>">
         <input type="hidden" name="image" value="<?= htmlspecialchars($fetch_products['image']); ?>">
         <button type="submit" class="fas fa-shopping-cart" name="add_to_cart"></button>
         <img src="uploaded_img/<?= htmlspecialchars($fetch_products['image']); ?>" alt="">
         <a href="category.php?category=<?= urlencode($fetch_products['category']); ?>" class="cat"><?= htmlspecialchars($fetch_products['category']); ?></a>
         <div class="name"><?= htmlspecialchars($fetch_products['name']); ?></div>
         <div class="flex">
            <div class="price"><span>₱</span><?= number_format($fetch_products['price'], 2); ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="1">
         </div>
      </form>
      <?php
            }
         } else {
            echo '<p class="empty">No products found in this category.</p>';
         }
      } else {
         echo '<p class="empty">No category selected.</p>';
      }
      ?>
   </div>
</section>

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
   <img src="images/loader.gif" alt="">
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
   document.querySelectorAll('.products .box').forEach(box => {
      const qtyInput = box.querySelector('.qty');
      const priceVal = parseFloat(box.querySelector('input[name="price"]').value);
      const priceEl  = box.querySelector('.price');

      function updatePrice() {
         const quantity = parseInt(qtyInput.value) || 1;
         const total = priceVal * quantity;
         priceEl.innerHTML = '<span>₱</span>' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
      }

      qtyInput.addEventListener('input', updatePrice);
   });
});
</script>

<script src="js/script.js"></script>
</body>
</html>

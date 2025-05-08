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
   <title>Menu Lists</title>

   <!-- SweetAlert -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <link rel="icon" href="images/favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">

   <style>
      .swal-wide-lg {
         width: 650px !important;
         padding: 50px 40px !important;
      }

      .swal2-title-lg {
         font-size: 32px !important;
         font-weight: bold;
      }

      .swal2-confirm-lg {
         font-size: 20px !important;
         padding: 12px 30px !important;
      }

      .products .box {
         box-shadow: 0 0 10px rgba(0,0,0,0.1);
         transition: transform 0.2s ease-in-out;
      }

      .products .box:hover {
         transform: scale(1.05);
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>Our Menu</h3>
   <p><a href="home.php">Home</a> <span> / Menu Lists</span></p>
</div>

<button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>

<section class="products">
   <div class="box-container">

      <?php
      $select_products = $conn->prepare("SELECT * FROM [products] ORDER BY [id] DESC");
      $select_products->execute();
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

<script>
document.addEventListener('DOMContentLoaded', function () {
   document.querySelectorAll('.products .box').forEach(box => {
      const qtyInput = box.querySelector('.qty');
      const priceVal = parseFloat(box.querySelector('[name="price"]').value);
      const priceEl  = box.querySelector('.price');
      qtyInput.addEventListener('input', () => {
         const total = (qtyInput.value || 1) * priceVal;
         priceEl.innerHTML = '<span>₱</span>' + total.toLocaleString('en-US',{minimumFractionDigits:2,maximumFractionDigits:2});
      });
   });

   document.querySelectorAll('.fa-shopping-cart').forEach(button => {
      button.addEventListener('click', event => {
         if ('<?= $user_id ?>' === '') {
            event.preventDefault();
            Swal.fire({
               icon: 'warning',
               title: 'You need to log in!',
               text: 'Please log in or create an account to add items to your cart.',
               confirmButtonColor: '#3085d6',
               confirmButtonText: 'OK'
            }).then(() => location.href = 'register.php');
         }
      });
   });
});
</script>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo "
      <script>
         Swal.fire({
            icon: 'success',
            title: '✔ Success',
            html: '<p style=\"font-size: 24px; font-weight: 500;\">$msg</p>',
            iconColor: '#28a745',
            customClass: {
               title: 'swal2-title-lg',
               popup: 'swal-wide-lg',
               confirmButton: 'swal2-confirm-lg'
            },
            confirmButtonText: 'OK'
         });
      </script>";
   }
}

if (isset($message1)) {
   foreach ($message1 as $msg) {
      echo "
      <script>
         Swal.fire({
            icon: 'warning',
            title: 'Notice',
            html: '<p style=\"font-size: 22px;\">$msg</p>',
            iconColor: '#f39c12',
            customClass: {
               title: 'swal2-title-lg',
               popup: 'swal-wide-lg',
               confirmButton: 'swal2-confirm-lg'
            },
            confirmButtonText: 'OK'
         });
      </script>";
   }
}
?>

<script src="js/script.js"></script>

<div class="loader">
   <img src="images/loader.gif" alt="Loading...">
</div>

</body>
</html>

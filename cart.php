<?php

include 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
   $user_id = $_SESSION['user_id'];
} else {
   $user_id = '';
   header('location:home.php');
   exit();
}

if (isset($_POST['delete'])) {
   $cart_id = $_POST['cart_id'];
   $delete_cart_item = $conn->prepare("DELETE FROM [cart] WHERE [id] = ?");
   $delete_cart_item->execute([$cart_id]);
   $message[] = 'Your order was deleted from cart';
}

if (isset($_POST['delete_all'])) {
   $delete_cart_item = $conn->prepare("DELETE FROM [cart] WHERE [user_id] = ?");
   $delete_cart_item->execute([$user_id]);
   $message[] = 'All of your orders were deleted from the cart.';
}

if (isset($_POST['update_qty'])) {
   $cart_id = $_POST['cart_id'];
   $qty = filter_var($_POST['qty'], FILTER_SANITIZE_STRING);
   $update_qty = $conn->prepare("UPDATE [cart] SET [quantity] = ? WHERE [id] = ?");
   $update_qty->execute([$qty, $cart_id]);
   $message[] = 'Your menu order quantity was successfully updated.';
}

$grand_total = 0;

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>

   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

   <link rel="icon" href="images/favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include 'components/user_header.php'; ?>

<button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>

<section class="products">
   <h1 class="title">Total Orders</h1>
   <div class="box-container">
      <?php
         $grand_total = 0;
         $select_cart = $conn->prepare("SELECT * FROM [cart] WHERE [user_id] = ?");
         $select_cart->execute([$user_id]);
         $cart_items = $select_cart->fetchAll(PDO::FETCH_ASSOC);

         if ($cart_items && count($cart_items) > 0) {
            foreach ($cart_items as $fetch_cart) {
      ?>
      <form action="" method="post" class="box" oninput="updateTotal(this)">
         <input type="hidden" name="cart_id" value="<?= $fetch_cart['id']; ?>">
         <button type="button" class="fas fa-times" data-toggle="modal" data-target="#modal<?= $fetch_cart['id']; ?>"></button>
         <img src="uploaded_img/<?= $fetch_cart['image']; ?>" alt="">
         <div class="name"><?= $fetch_cart['name']; ?></div>
         <div class="flex">
            <div class="price"><span>₱</span><?= $fetch_cart['price']; ?></div>
            <input type="number" name="qty" class="qty" min="1" max="99" value="<?= $fetch_cart['quantity']; ?>" maxlength="2">
            <button type="submit" class="fas fa-edit" name="update_qty"></button>
         </div>
         <?php
            $sub_total = $fetch_cart['price'] * $fetch_cart['quantity'];
         ?>
         <div class="sub-total">Total Price: <span>₱<?= number_format($sub_total, 2); ?></span></div>

         <!-- Modal -->
         <div class="modal fade" id="modal<?= $fetch_cart['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h1 class="modal-title">Warning!</h1>
                     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                     </button>
                  </div>
                  <div class="modal-body">
                     <p style="font-size: 20px;">Are you sure you want to remove this order?</p>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                     <button type="submit" name="delete" class="btn btn-primary">Confirm</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
      <?php
         $grand_total += $sub_total;
            }
         } else {
            echo '<p class="empty">Hungry? Order now.</p>';
         }
      ?>
   </div>

   <div class="cart-total">
      <p>Total Due: <span id="grand-total">₱<?= number_format($grand_total, 2); ?></span></p>
      <a href="checkout.php" class="btn <?= ($grand_total > 1)?'':'disabled'; ?>">Proceed To Checkout</a>
   </div>

   <div class="more-btn">
      <form action="" method="post">
         <button type="button" class="delete-btn <?= ($grand_total > 1)?'':'disabled'; ?>" data-toggle="modal" data-target="#deleteAllModal">Delete all</button>

         <div class="modal fade" id="deleteAllModal" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-dialog-centered" role="document">
               <div class="modal-content">
                  <div class="modal-header">
                     <h1 class="modal-title">Warning!</h1>
                     <button type="button" class="close" data-dismiss="modal"><span>&times;</span></button>
                  </div>
                  <div class="modal-body">
                     <p style="font-size: 20px;">Are you sure you want to remove all of your orders?</p>
                  </div>
                  <div class="modal-footer">
                     <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                     <button type="submit" name="delete_all" class="btn btn-primary">Confirm</button>
                  </div>
               </div>
            </div>
         </div>
      </form>
      <a href="menu.php" class="btn">Order More</a>
   </div>
</section>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      echo "
         <script>
            swal({
               title: 'Success',
               text: '$msg',
               icon: 'success',
               button: 'Close',
            });
         </script>
      ";
   }
}
?>

<script src="js/script.js"></script>

<div class="loader">
   <img src="images/loader.gif" alt="">
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var quantityInputs = document.querySelectorAll('.qty');

    quantityInputs.forEach(function (input) {
        input.addEventListener('input', function () {
            var quantity = this.value;
            var unitPrice = parseFloat(this.closest('.box').querySelector('.price').innerText.replace('₱', ''));
            var totalPrice = quantity * unitPrice;
            var priceElement = this.closest('.box').querySelector('.sub-total span');
            priceElement.innerHTML = '₱' + totalPrice.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        });
    });
});
</script>

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

</body>
</html>

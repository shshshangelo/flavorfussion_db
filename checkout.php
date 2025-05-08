<?php
include 'components/connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
   header('location:login.php');
   exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user profile
$select_profile = $conn->prepare("SELECT * FROM [users] WHERE [id] = ?");
$select_profile->execute([$user_id]);
$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

// Handle form submit
if (isset($_POST['submit'])) {
   $fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
   $mname = filter_var($_POST['mname'], FILTER_SANITIZE_STRING);
   $lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
   $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
   $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
   $address = trim(filter_var($_POST['address'], FILTER_SANITIZE_STRING));
   $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
   $total_products = $_POST['total_products'];
   $total_price = $_POST['total_price'];

   $check_cart = $conn->prepare("SELECT * FROM [cart] WHERE [user_id] = ?");
   $check_cart->execute([$user_id]);

   $invalid_addresses = ['to be updated', 'n/a', 'na', 'enter your full address', ''];
   $address_is_invalid = in_array(strtolower($address), $invalid_addresses) || strlen($address) < 10;

   if ($check_cart->rowCount() == 0) {
      $message[] = 'Your cart is empty.';
   } elseif (empty($email) || $address_is_invalid) {
      $message[] = 'Please update your address before placing an order.';
   } elseif (empty($method)) {
      $message[] = 'Please select a payment method.';
   } else {
      $insert_order = $conn->prepare("INSERT INTO [orders] ([user_id], [fname], [mname], [lname], [number], [email], [address], [method], [total_products], [total_price]) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
      $insert_order->execute([$user_id, $fname, $mname, $lname, $number, $email, $address, $method, $total_products, $total_price]);

      $delete_cart = $conn->prepare("DELETE FROM [cart] WHERE [user_id] = ?");
      $delete_cart->execute([$user_id]);

      $message[] = 'Your order was successfully placed!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Checkout</title>
   <script src="https://cdn.jsdelivr.net/npm/sweetalert/dist/sweetalert.min.js"></script>
   <link rel="stylesheet" href="css/style.css">
   <link rel="icon" href="images/favicon.ico">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <style>
      .user-info p,
      .cart-items p {
         font-size: 1.6rem;
         color: white;
         margin-bottom: 1rem;
         display: flex;
         align-items: center;
         gap: 10px;
      }

      .cart-items .name {
         font-weight: bold;
      }

      .cart-items .price {
         margin-left: auto;
      }

      .grand-total {
         font-size: 1.8rem;
         font-weight: bold;
         margin-top: 20px;
         color: darkgreen;
      }

      .empty {
         color: red;
         font-weight: bold;
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
   <h3>Checkout</h3>
   <p><a href="home.php">Home</a> <span> / Checkout</span></p>
</div>

<section class="checkout">
<form action="" method="post">
   <div class="cart-items">
      <h3>My Orders</h3>
      <?php
      $grand_total = 0;
      $cart_items = [];

      $select_cart = $conn->prepare("SELECT * FROM [cart] WHERE [user_id] = ?");
      $select_cart->execute([$user_id]);

      $cart_rows = $select_cart->fetchAll(PDO::FETCH_ASSOC);
      if ($cart_rows && count($cart_rows) > 0) {
         foreach ($cart_rows as $fetch_cart) {
            $cart_items[] = $fetch_cart['name'] . ' (' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . ')';
            $grand_total += $fetch_cart['price'] * $fetch_cart['quantity'];

            echo '<p><span class="name">' . $fetch_cart['name'] . '</span> <span class="price">₱' . $fetch_cart['price'] . ' x ' . $fetch_cart['quantity'] . '</span></p>';
         }
      } else {
         echo '<p class="empty">Looks like your cart is empty. Please, order now.</p>';
      }

      $total_products = implode(' - ', $cart_items);
      ?>
      <p class="grand-total"><span>Total Due:</span> ₱<?= number_format($grand_total, 2); ?></p>
      <a href="cart.php" class="btn">View My Orders</a>
   </div>

   <!-- Hidden values -->
   <input type="hidden" name="total_products" value="<?= $total_products; ?>">
   <input type="hidden" name="total_price" value="<?= $grand_total; ?>">
   <input type="hidden" name="fname" value="<?= $fetch_profile['fname']; ?>">
   <input type="hidden" name="mname" value="<?= $fetch_profile['mname']; ?>">
   <input type="hidden" name="lname" value="<?= $fetch_profile['lname']; ?>">
   <input type="hidden" name="number" value="<?= $fetch_profile['number']; ?>">
   <input type="hidden" name="email" value="<?= $fetch_profile['email']; ?>">
   <input type="hidden" name="address" value="<?= $fetch_profile['address']; ?>">

   <div class="user-info">
      <h3>Your Info</h3>
      <p><i class="fas fa-user"></i><?= $fetch_profile['fname'] . ' ' . $fetch_profile['mname'] . ' ' . $fetch_profile['lname'] ?></p>
      <p><i class="fas fa-phone"></i><?= $fetch_profile['number'] ?></p>
      <p><i class="fas fa-envelope"></i><?= $fetch_profile['email'] ?></p>
      <a href="update_profile.php" class="btn">Update Info</a>

      <h3>Delivery Address</h3>
      <p><i class="fas fa-map-marker-alt"></i><?= $fetch_profile['address'] ?: 'Enter your Full Address'; ?></p>
      <a href="update_address.php" class="btn">Update Address</a>

      <select name="method" id="paymentMethod" class="box" required>
         <option value="" disabled selected>--Select Payment Method--</option>
         <option value="Cash on Delivery">Cash on Delivery</option>
         <option value="Card">Card</option>
         <option value="Gcash">Gcash</option>
         <option value="Maya">Maya</option>
      </select>

      <input type="submit" value="Place Order" name="submit" class="btn" style="width:100%; background:var(--red); color:white;">
   </div>
</form>
</section>

<?php
if (isset($message)) {
   foreach ($message as $msg) {
      $is_success = strpos(strtolower($msg), 'successfully') !== false;
      $title = $is_success ? 'Success!' : 'Error';
      $icon = $is_success ? 'success' : 'error';

      echo "
      <script>
      swal({
         title: '$title',
         text: '$msg',
         icon: '$icon',
         button: 'OK'
      }).then(() => {
         if ($is_success) {
            window.location.href = 'orders.php';
         }
      });
      </script>";
   }
}
?>

<div class="loader">
   <img src="images/loader.gif" alt="Loading...">
</div>

<script src="js/script.js"></script>
</body>
</html>

<?php
if (!isset($conn)) {
   include 'components/connect.php';
}

if (session_status() === PHP_SESSION_NONE) {
   session_start();
}

$user_id = $_SESSION['user_id'] ?? '';
?>

<header class="header">
   <section class="flex">
      <a href="home.php" class="logo">FlavorFussion Eats</a>

      <nav class="navbar">
         <a href="about.php">About Us</a>
         <a href="messages.php">Feedbacks</a>
         <a href="menu.php">Menu Lists</a>
         <?php if (!empty($user_id)) : ?>
            <a href="orders.php">Orders</a>
            <a href="cancelled_orders.php">Cancelled</a>
            <a href="history.php">History</a>
            <a href="contact.php">To Rate</a>
         <?php endif; ?>
      </nav>

      <div class="icons">
         <?php
         $total_cart_items = 0;
         if (!empty($user_id)) {
            $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM cart WHERE user_id = ?");
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $total_cart_items = $row['total'] ?? 0;
         }
         ?>
         <a href="search.php"><i class="fas fa-search"></i></a>
         <a href="cart.php"><i class="fas fa-shopping-cart"></i><span>(<?= $total_cart_items ?>)</span></a>
         <div id="user-btn" class="fas fa-user"></div>
         <div id="menu-btn" class="fas fa-bars"></div>
      </div>

      <div class="profile">
         <?php
         if (!empty($user_id)) {
            $stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
         ?>
               <p class="name"><?= htmlspecialchars($user['fname'] . ' ' . $user['mname'] . ' ' . $user['lname']) ?></p>
               <div class="flex">
                  <a href="profile.php" class="btn">My Profile</a>
                  <a href="components/user_logout.php" class="delete-btn">Sign out</a>
               </div>
         <?php
            }
         } else {
         ?>
            <p class="name">Get started.</p>
            <a href="register.php" class="btn">Sign up</a>
            <a href="login.php" class="btn">Log in</a>
         <?php } ?>
      </div>
   </section>
</header>

<style>
.header .navbar {
   display: flex;
   gap: 15px;
}

.header .navbar a[href="search.php"] {
   order: 2;
}
</style>

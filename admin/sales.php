<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    header('location:admin_login.php');
    exit();
}

// Fetch admin profile
$fetch_profile = [];
$get_admin = $conn->prepare("SELECT * FROM admin WHERE id = ?");
$get_admin->execute([$admin_id]);
if ($get_admin->rowCount() > 0) {
    $fetch_profile = $get_admin->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Management Dashboard</title>

   <!-- Bootstrap & FontAwesome -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="icon" href="favicon.ico" type="image/x-icon" />
   <link rel="stylesheet" href="../css/sales_style.css">
</head>

<body>

<?php include '../components/sales_header.php'; ?>

<section class="dashboard">
   <h1 class="heading" style="color: #ffffff; cursor: default;">Management Dashboard</h1>

   <div class="box-container">

      <div class="box">
         <br>
         <h3>Hi!</h3>
         <p><?= htmlspecialchars($fetch_profile['name'] ?? ''); ?></p>
      </div>

      <div class="box">
         <?php
         $total_pendings = 0;
         $select_pendings = $conn->prepare("SELECT * FROM orders WHERE order_status = ?");
         $select_pendings->execute(['Preparing your Food']);
         while ($fetch = $select_pendings->fetch(PDO::FETCH_ASSOC)) {
            $total_pendings += $fetch['total_price'];
         }
         ?>
         <h3>₱<?= $total_pendings; ?></h3>
         <p>Total Pending Payment Amount of Customer</p>
         <a href="pending.php" class="btn">Pending Payment Amount</a>
      </div>

      <div class="box">
         <?php
         $total_completes_amount = 0;
         $select = $conn->prepare("SELECT total_price FROM completed_orders WHERE order_status = ?");
         $select->execute(['Completed']);
         while ($fetch = $select->fetch(PDO::FETCH_ASSOC)) {
            $total_completes_amount += $fetch['total_price'];
         }
         ?>
         <h3>₱<?= $total_completes_amount; ?></h3>
         <p>Total Completed Payment Amount of Customer</p>
         <a href="complete.php" class="btn">Completed Payment Amount</a>
      </div>

      <div class="box">
         <?php
         $select = $conn->prepare("SELECT COUNT(*) FROM completed_orders WHERE order_status = ?");
         $select->execute(['Completed']);
         $completed_order_count = $select->fetchColumn();
         ?>
         <h3><?= $completed_order_count; ?></h3>
         <p>Total Completed Orders of Customer</p>
         <a href="complete.php" class="btn">Total Completed Orders</a>
      </div>

      <div class="box">
         <?php
         $select = $conn->prepare("SELECT COUNT(*) FROM cancelled_orders");
         $select->execute();
         $cancelled_count = $select->fetchColumn();
         ?>
         <h3><?= $cancelled_count; ?></h3>
         <p>Total Cancelled Orders of Customer</p>
         <a href="cancel.php" class="btn">Total Cancelled Orders</a>
      </div>

      <div class="box">
         <?php
         $select = $conn->prepare("SELECT COUNT(*) FROM messages");
         $select->execute();
         $message_count = $select->fetchColumn();
         ?>
         <h3><?= $message_count; ?></h3>
         <p>New Reviews of Customers</p>
         <a href="messages.php" class="btn">All Feedbacks</a>
      </div>

      <div class="box">
         <?php
         $select = $conn->prepare("SELECT COUNT(*) FROM users");
         $select->execute();
         $user_count = $select->fetchColumn();
         ?>
         <h3><?= $user_count; ?></h3>
         <p>Customer's Account</p>
         <a href="users_accounts.php" class="btn">Customers Account</a>
      </div>

   </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>

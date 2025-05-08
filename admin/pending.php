<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:admin_login.php');
    exit();
}

if (isset($_POST['update_payment'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $timestamp = ($order_status === 'Completed') ? date('Y-m-d H:i:s') : null;

    $update_status = $conn->prepare("UPDATE orders SET order_status = ?, completed_timestamp = ? WHERE id = ?");
    $update_status->execute([$order_status, $timestamp, $order_id]);

    if ($order_status === 'Completed') {
        $conn->prepare("INSERT INTO completed_orders (user_id, placed_on, fname, mname, lname, address, total_products, total_price, method, order_status, completed_timestamp)
                        SELECT user_id, placed_on, fname, mname, lname, address, total_products, total_price, method, order_status, completed_timestamp FROM orders WHERE id = ?")
             ->execute([$order_id]);

        $conn->prepare("INSERT INTO order_history (user_id, placed_on, fname, mname, lname, address, total_products, total_price, method, order_status)
                        SELECT user_id, placed_on, fname, mname, lname, address, total_products, total_price, method, order_status FROM orders WHERE id = ?")
             ->execute([$order_id]);

        $conn->prepare("DELETE FROM orders WHERE id = ?")->execute([$order_id]);

        header('location:placed_orders.php');
        exit();
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $conn->prepare("DELETE FROM orders WHERE id = ?")->execute([$delete_id]);
    header('location:placed_orders.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Pending Customer Orders</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <link rel="icon" href="favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="../css/admin_style.css">
   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

   <style>
      .box-container {
         display: flex;
         flex-wrap: wrap;
         justify-content: center;
      }
      .box {
         border: 1px solid #ccc;
         border-radius: 8px;
         padding: 20px;
         margin: 10px;
         max-width: 400px;
         background-color: #f9f9f9;
         box-shadow: 0 2px 8px rgba(0,0,0,0.1);
      }
      .box:hover {
         transform: scale(1.02);
      }
      .box p {
         font-size: 16px;
         margin-bottom: 8px;
      }
      .box span {
         font-weight: bold;
      }
      .btn-group {
         margin-top: 10px;
         display: flex;
         justify-content: space-between;
      }
      .btn {
         font-size: 14px;
         padding: 5px 10px;
      }
      .heading {
         text-align: center;
         margin-top: 20px;
         font-size: 24px;
      }
   </style>
</head>

<body>

<?php include '../components/sales_header.php'; ?>

<section class="placed-orders">
   <h1 class="heading">Pending Customer Orders</h1>
   <div class="box-container">
   <?php
      $select_orders = $conn->prepare("SELECT * FROM orders WHERE order_status = 'Preparing your Food'");
      $select_orders->execute();
      $orders = $select_orders->fetchAll(PDO::FETCH_ASSOC);

      if (count($orders) > 0) {
         foreach ($orders as $order) {
            $estimatedTime = date('Y-m-d H:i:s', strtotime($order['placed_on']) + 300);
   ?>
      <div class="box">
         <p>Date/Time Placed: <span><?= $order['placed_on']; ?></span></p>
         <p>Customer: <span><?= $order['fname'] . ' ' . $order['mname'] . ' ' . $order['lname']; ?></span></p>
         <p>Total Menu: <span><?= $order['total_products']; ?></span></p>
         <p>Total Due: <span>₱<?= $order['total_price']; ?></span></p>
         <p>Payment Method: <span><?= $order['method']; ?></span></p>
         <p>Delivery Time: <span><?= $estimatedTime; ?></span></p>
         <p>Order Status: <span style="color:red;"><?= $order['order_status']; ?></span></p>

         <form action="" method="POST">
            <input type="hidden" name="order_id" value="<?= $order['id']; ?>">
            <input type="hidden" name="order_status" value="Completed">
            <div class="btn-group">
               <button type="submit" name="update_payment" class="btn btn-success">Mark as Completed</button>
               <a href="?delete=<?= $order['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this order?')">Delete</a>
            </div>
         </form>
      </div>
   <?php
         }
      } else {
         echo '<p class="empty">No pending orders found.</p>';
      }
   ?>
   </div>
</section>

</body>
</html>

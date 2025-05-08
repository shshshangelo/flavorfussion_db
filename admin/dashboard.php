<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    header('location:admin_login.php');
    exit;
}

// Fetch HeadChef's name
$admin_name = 'HeadChef';
try {
    $stmt = $conn->prepare("SELECT name FROM admin WHERE id = ?");
    $stmt->execute([$admin_id]);
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $admin_name = $row['name'];
    }
} catch (PDOException $e) {
    echo "Error fetching admin data: " . $e->getMessage();
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8" />
   <meta http-equiv="X-UA-Compatible" content="IE=edge" />
   <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   <title>HeadChef Dashboard</title>
   <link rel="icon" href="favicon.ico" type="image/x-icon" />

   <!-- Bootstrap + FontAwesome -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" />
   <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" />

   <link rel="stylesheet" href="../css/admin_style.css" />
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="dashboard">
   <h1 class="heading">HeadChef Dashboard</h1>

   <div class="box-container">

      <div class="box">
         <h3>Hello!</h3>
         <p><?= htmlspecialchars($admin_name) ?></p>
      </div>

      <div class="box">
         <?php
         try {
             $stmt = $conn->query("SELECT COUNT(*) AS total FROM products");
             $count = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
         } catch (PDOException $e) {
             echo "Error fetching product count: " . $e->getMessage();
             $count = 0;
         }
         ?>
         <h3><?= $count ?></h3>
         <p>Total Menu</p>
         <a href="products.php" class="btn">Menu Lists</a>
      </div>

      <div class="box">
         <?php
         try {
             $stmt = $conn->prepare("SELECT COUNT(*) AS total FROM orders WHERE order_status = ?");
             $stmt->execute(['Preparing your Food']);
             $pending = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
         } catch (PDOException $e) {
             echo "Error fetching pending orders count: " . $e->getMessage();
             $pending = 0;
         }
         ?>
         <h3><?= $pending ?></h3>
         <p>Customer Orders</p>
         <a href="placed_orders.php" class="btn">Orders</a>
      </div>

      <div class="box">
         <?php
         try {
             $stmt = $conn->query("SELECT COUNT(*) AS total FROM cancelled_orders");
             $cancelled = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
         } catch (PDOException $e) {
             echo "Error fetching cancelled orders count: " . $e->getMessage();
             $cancelled = 0;
         }
         ?>
         <h3><?= $cancelled ?></h3>
         <p>Cancelled Orders</p>
         <a href="cancelled_orders.php" class="btn">Cancelled Orders</a>
      </div>

      <div class="box">
         <?php
         try {
             $stmt = $conn->query("SELECT COUNT(*) AS total FROM completed_orders");
             $completed = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
         } catch (PDOException $e) {
             echo "Error fetching completed orders count: " . $e->getMessage();
             $completed = 0;
         }
         ?>
         <h3><?= $completed ?></h3>
         <p>All Orders</p>
         <a href="completed_orders.php" class="btn">Total Orders</a>
      </div>

   </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>

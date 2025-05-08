<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:admin_login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Completed Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/admin_style.css">

    <style>
        .box-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .box {
            background-color: #66806A;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 15px;
            margin: 10px;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            color: #fff;
        }

        .box p {
            margin-bottom: 6px;
            font-size: 17px;
        }

        .box span {
            font-weight: bold;
        }

        .Empty {
            text-align: center;
            font-size: 20px;
            margin-top: 20px;
            color: red;
        }

        .heading {
            text-align: center;
            font-size: 28px;
            margin: 30px 0 15px;
        }

        @media (max-width: 768px) {
            .box {
                width: calc(50% - 20px);
            }
        }

        @media (max-width: 480px) {
            .box {
                width: calc(100% - 20px);
            }
        }
    </style>
</head>
<body>

<?php include '../components/sales_header.php'; ?>

<section class="completed-orders">
    <h1 class="heading">Completed Orders</h1>
    <div class="box-container">

        <?php
        $select = $conn->prepare("SELECT * FROM completed_orders ORDER BY placed_on DESC");
        $select->execute();
        $completed_orders = $select->fetchAll(PDO::FETCH_ASSOC);

        if (count($completed_orders) > 0) {
            foreach ($completed_orders as $order) {
                $placedTime = strtotime($order['placed_on']);
                $estimatedDeliveryTime = date('Y-m-d H:i:s', $placedTime + 300);
        ?>

        <div class="box">
            <p>Date/Time Placed: <span><?= $order['placed_on']; ?></span></p>
            <p>Customer: <span><?= $order['fname'] . ' ' . $order['mname'] . ' ' . $order['lname']; ?></span></p>
            <p>Total Menu: <span><?= $order['total_products']; ?></span></p>
            <p>Total Due: <span>₱<?= $order['total_price']; ?></span></p>
            <p>Payment Method: <span><?= $order['method']; ?></span></p>
            <p>Delivery Time: <span><?= $estimatedDeliveryTime; ?></span></p>
            <p>Order Status: <span style="color: white; font-weight: bold;">Completed</span></p>
        </div>

        <?php
            }
        } else {
            echo '<p class="Empty">No completed orders yet!</p>';
        }
        ?>

    </div>
</section>

<script src="../js/sales_script.js"></script>
</body>
</html>

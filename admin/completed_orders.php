<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Total Orders of Customers</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <style>
        .box-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .box {
            background-color: green;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            margin: 15px;
            max-width: 400px;
            color: white;
            transition: transform 0.2s ease-in-out;
        }

        .box:hover {
            transform: scale(1.02);
        }

        .box p {
            margin-bottom: 5px;
            font-size: 18px;
        }

        .Empty {
            padding: 1.5rem;
            text-align: center;
            width: 100%;
            font-size: 2rem;
            text-transform: capitalize;
            color: red;
        }

        @media screen and (max-width: 768px) {
            .box {
                width: calc(50% - 20px);
            }
        }

        @media screen and (max-width: 480px) {
            .box {
                width: calc(100% - 20px);
            }
        }

        .box label {
            text-transform: uppercase;
            font-weight: bold;
            font-size: 16px;
        }
    </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="completed-orders">
    <h1 class="heading">Total Orders</h1>

    <div class="box-container">
        <?php
        $select_completed_orders = $conn->prepare("SELECT * FROM completed_orders ORDER BY placed_on DESC");
        $select_completed_orders->execute();
        $completed_orders = $select_completed_orders->fetchAll(PDO::FETCH_ASSOC);

        if (count($completed_orders) > 0) {
            foreach ($completed_orders as $fetch) {
                $placedTime = strtotime($fetch['placed_on']);
                $estimatedDeliveryTime = date('Y-m-d H:i:s', $placedTime + (15 * 60));
        ?>
        <div class="box">
            <p>Date/Time Placed: <span><?= $fetch['placed_on']; ?></span></p>
            <p>Customer Name: <span><?= $fetch['fname'] . ' ' . $fetch['mname'] . ' ' . $fetch['lname']; ?></span></p>
            <p>Total Menu: <span><?= $fetch['total_products']; ?></span></p>
            <p>Total Due: <span>₱<?= $fetch['total_price']; ?></span></p>
            <p>Payment Method: <span><?= $fetch['method']; ?></span></p>
            <p>Delivered Date/Time: <span><?= $estimatedDeliveryTime; ?></span></p>
            <p>Order Status:
                <label style="color: white;">
                    Completed
                    <input type="checkbox" checked disabled>
                </label>
            </p>
        </div>
        <?php
            }
        } else {
            echo '<p class="Empty">No completed orders yet.</p>';
        }
        ?>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
<script src="../js/admin_script.js"></script>
</body>
</html>

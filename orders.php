<?php
include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>

    <style>
        .box-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }
        .box {
            background-color: #66806A;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s ease-in-out;
            flex-basis: calc(33.33% - 20px);
            box-sizing: border-box;
            max-width: 400px;
            margin: 10px;
        }
        .box:hover {
            transform: scale(1.02);
        }
        .box p {
            margin-bottom: 5px;
            font-size: 18px;
            color: black;
        }
        .empty {
            padding: 1.5rem;
            text-align: center;
            width: 100%;
            font-size: 2rem;
            text-transform: capitalized;
            color: red;
        }
        .cancel-btn {
            background-color: #dc3545;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .cancel-btn:hover {
            background-color: #c82333;
        }
        .rate-btn {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        .rate-btn:hover {
            background-color: #45a049;
        }
        .larger-sweetalert-container, 
        .larger-sweetalert-popup,
        .larger-sweetalert-title,
        .larger-sweetalert-content,
        .larger-sweetalert-confirm-button,
        .larger-sweetalert-cancel-button {
            font-size: 2rem;
        }
    </style>
</head>

<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
    <h3>Orders</h3>
    <p><a href="home.php">Home</a> <span> / Total Orders</span></p>
</div>

<section class="orders">
    <div class="box-container">
        <?php
        $select_orders = $conn->prepare("SELECT * FROM [orders] WHERE user_id = ? ORDER BY placed_on DESC");
        $select_orders->execute([$user_id]);
        
        $orders = $select_orders->fetchAll(PDO::FETCH_ASSOC);
        if (count($orders) > 0) {
            foreach ($orders as $fetch_orders) {
                $placedTime = strtotime($fetch_orders['placed_on']);
                $estimatedDeliveryTime = date('Y-m-d H:i:s', $placedTime + (15 * 60));
        ?>
                <div class="box">
                    <p>Date/Time Placed On: <span><?= $fetch_orders['placed_on']; ?></span></p>
                    <p>First Name: <span><?= $fetch_orders['fname']; ?></span></p>
                    <p>Middle Name: <span><?= $fetch_orders['mname']; ?></span></p>
                    <p>Last Name: <span><?= $fetch_orders['lname']; ?></span></p>
                    <p>Full Address: <span><?= $fetch_orders['address']; ?></span></p>
                    <p>Your Orders: <span><?= $fetch_orders['total_products']; ?></span></p>
                    <p>Total Due: <span>₱<?= $fetch_orders['total_price']; ?></span></p>
                    <p>Payment Method: <span><?= $fetch_orders['method']; ?></span></p>
                    <p>Order Status: 
                        <span style="color:<?php
                            if ($fetch_orders['order_status'] == 'Preparing your Food') {
                                echo 'red';
                            } elseif ($fetch_orders['order_status'] == 'Cancelled') {
                                echo 'grey';
                            } else {
                                echo 'green';
                            }
                        ?>"><?= $fetch_orders['order_status']; ?></span>
                    </p>
        
                    <?php if ($fetch_orders['order_status'] == 'Completed') : ?>
                        <p>Delivered Date/Time: <span><?= $estimatedDeliveryTime; ?></span></p>
                        <button class="rate-btn" onclick="redirectToMessages()">Rate Us</button>
                        <script>
                            function redirectToMessages() {
                                window.location.href = "contact.php";
                            }
                        </script>
                    <?php endif; ?>
        
                    <?php if ($fetch_orders['order_status'] != 'Completed') : ?>
                        <form id="cancelOrderForm<?= $fetch_orders['id']; ?>" method="post" action="cancel_order.php">
                            <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                            <button type="button" class="cancel-btn" onclick="confirmCancel('<?= $fetch_orders['id']; ?>')">Cancel My Order</button>
                        </form>
                    <?php endif; ?>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty">No orders placed yet!</p>';
        }
        
        ?>
    </div>
</section>

<script>
function confirmCancel(orderId) {
    Swal.fire({
        title: "Are you sure you want to cancel the order?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
        customClass: {
            container: 'larger-sweetalert-container',
            popup: 'larger-sweetalert-popup',
            title: 'larger-sweetalert-title',
            content: 'larger-sweetalert-content',
            confirmButton: 'larger-sweetalert-confirm-button',
            cancelButton: 'larger-sweetalert-cancel-button'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById("cancelOrderForm" + orderId).submit();
        }
    });
}
</script>

<div class="loader">
    <img src="images/loader.gif" alt="">
</div>

<script src="js/script.js"></script>

</body>
</html>

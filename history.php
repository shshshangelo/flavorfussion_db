<?php
include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>

    <style>
        .order-history { margin-top: 5px; }
        .box-container { display: flex; flex-wrap: wrap; justify-content: center; }
        .box {
            background-color: #66806A;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 20px;
            margin: 10px;
            max-width: 400px;
            color: black;
            flex-basis: calc(33.33% - 20px);
            box-sizing: border-box;
        }
        .box:hover { transform: scale(1.02); }
        .box p { font-size: 18px; }
        .empty { text-align: center; font-size: 2rem; color: red; width: 100%; }
        .order-again-btn, .delete-btn {
            padding: 9px 10px;
            border-radius: 5px;
            font-size: 19px;
            color: #fff;
            border: none;
            cursor: pointer;
        }
        .order-again-btn { background-color: #007bff; }
        .order-again-btn:hover { background-color: #0056b3; }
        .delete-btn { background-color: red; }
        .delete-btn:hover { background-color: #c82333; }
    </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
    <h3>Order History</h3>
    <p><a href="home.php">Home</a> <span> / Order History</span></p>
</div>

<section class="order-history">
    <div class="box-container">
        <?php
        try {
            $select_history = $conn->prepare("SELECT * FROM [order_history] WHERE user_id = ? ORDER BY placed_on DESC");
            $select_history->execute([$user_id]);
            $orders = $select_history->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo '<p class="empty">Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
            die();
        }

        if (count($orders) > 0) {
            foreach ($orders as $fetch) {
                if ($fetch['order_status'] === 'Completed') {
                    $placedTime = strtotime($fetch['placed_on']);
                    $estimatedDeliveryTime = date('Y-m-d H:i:s', $placedTime + (15 * 60));
                }
                ?>
                <div class="box">
                    <p>Date/Time Placed On: <span><?= $fetch['placed_on']; ?></span></p>
                    <p>First Name: <span><?= $fetch['fname']; ?></span></p>
                    <p>Middle Name: <span><?= $fetch['mname']; ?></span></p>
                    <p>Last Name: <span><?= $fetch['lname']; ?></span></p>
                    <p>Full Address: <span><?= $fetch['address']; ?></span></p>
                    <p>Your Orders: <span><?= $fetch['total_products']; ?></span></p>
                    <p>Total Due: <span>₱<?= $fetch['total_price']; ?></span></p>
                    <p>Payment Method: <span><?= $fetch['method']; ?></span></p>
                    <p>Order Status: <span><?= $fetch['order_status']; ?></span></p>
                    <?php if ($fetch['order_status'] === 'Completed'): ?>
                        <p>Delivered Date/Time: <span><?= $estimatedDeliveryTime; ?></span></p>
                    <?php endif; ?>
                    <button class="order-again-btn" onclick="orderAgain(<?= $fetch['id']; ?>)">Order Again</button>
                    <button class="delete-btn" onclick="deleteOrder(<?= $fetch['id']; ?>)">Delete</button>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">No order history available!</p>';
        }
        ?>
    </div>
</section>

<style>
    .swal-wide {
    width: 550px !important;
    font-size: 1.4rem;
}

.swal-title-lg {
    font-size: 1.8rem !important;
}

.swal-content-lg {
    font-size: 1.4rem !important;
}

.swal-btn-lg {
    font-size: 1.2rem !important;
    padding: 10px 25px !important;
}
</style>

<script>
function orderAgain(orderId) {
    Swal.fire({
        title: 'Order again?',
        text: 'Do you want to reorder this?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'Yes, order again!',
        customClass: {
            popup: 'swal-wide',
            title: 'swal-title-lg',
            content: 'swal-content-lg',
            confirmButton: 'swal-btn-lg',
            cancelButton: 'swal-btn-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Ordered again!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                customClass: { popup: 'swal-wide' }
            });
            setTimeout(() => {
                window.location.href = "order_again.php?id=" + orderId;
            }, 2000);
        }
    });
}

function deleteOrder(orderId) {
    Swal.fire({
        title: 'Delete this order?',
        text: 'This will remove it from your history.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it!',
        customClass: {
            popup: 'swal-wide',
            title: 'swal-title-lg',
            content: 'swal-content-lg',
            confirmButton: 'swal-btn-lg',
            cancelButton: 'swal-btn-lg'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'Deleted!',
                icon: 'success',
                timer: 1500,
                showConfirmButton: false,
                customClass: { popup: 'swal-wide' }
            });
            setTimeout(() => {
                window.location.href = "delete_order.php?id=" + orderId;
            }, 2000);
        }
    });
}
</script>


<div class="loader">
    <img src="images/loader.gif" alt="Loading...">
</div>

<script src="js/script.js"></script>

</body>
</html>

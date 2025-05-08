<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

$message = [];

if (isset($_POST['update_payment'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];
    $timestamp = ($order_status === 'Completed') ? date('Y-m-d H:i:s') : null;

    $update_status = $conn->prepare("UPDATE orders SET order_status = ?, completed_timestamp = ? WHERE id = ?");
    $update_status->execute([$order_status, $timestamp, $order_id]);

    if ($order_status === 'Completed') {
        $move_to_completed = $conn->prepare("INSERT INTO completed_orders (user_id, placed_on, fname, mname, lname, address, total_products, total_price, method, order_status, completed_timestamp)
            SELECT user_id, placed_on, fname, mname, lname, address, total_products, total_price, method, order_status, completed_timestamp FROM orders WHERE id = ?");
        $move_to_completed->execute([$order_id]);

        $move_to_history = $conn->prepare("INSERT INTO order_history (user_id, placed_on, fname, mname, lname, address, total_products, total_price, method, order_status)
            SELECT user_id, placed_on, fname, mname, lname, address, total_products, total_price, method, order_status FROM orders WHERE id = ?");
        $move_to_history->execute([$order_id]);

        $message[] = 'Your order is on its way';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_order = $conn->prepare("DELETE FROM orders WHERE id = ?");
    $delete_order->execute([$delete_id]);
    header('location:placed_orders.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Order Lists</title>
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .flex-btn .btn {
            font-size: 1.4rem;
            padding: 12px 24px;
            min-width: 120px;
            margin: 5px;
        }
    </style>
</head>

<body>
<?php include '../components/admin_header.php'; ?>

<section class="placed-orders">
    <h1 class="heading">Customer Orders</h1>
    <div class="box-container">
        <?php
        $select_orders = $conn->prepare("SELECT * FROM orders ORDER BY placed_on DESC");
        $select_orders->execute();
        $orders = $select_orders->fetchAll(PDO::FETCH_ASSOC);

        if (count($orders) > 0) {
            foreach ($orders as $fetch_orders) {
                $placedTime = strtotime($fetch_orders['placed_on']);
                $estimatedDeliveryTime = date('Y-m-d H:i:s', $placedTime + (15 * 60));
        ?>
        <div class="box">
            <p>Date/Time: <span><?= $fetch_orders['placed_on']; ?></span></p>
            <p>Name: <span><?= $fetch_orders['fname'] . ' ' . $fetch_orders['mname'] . ' ' . $fetch_orders['lname']; ?></span></p>
            <p>Address: <span><?= $fetch_orders['address']; ?></span></p>
            <p>Total Menu: <span><?= $fetch_orders['total_products']; ?></span></p>
            <p>Total Due: <span>₱<?= $fetch_orders['total_price']; ?></span></p>
            <p>Payment: <span><?= $fetch_orders['method']; ?></span></p>
            <p>ETA: <span><?= $estimatedDeliveryTime; ?></span></p>

            <form method="POST">
                <input type="hidden" name="order_id" value="<?= $fetch_orders['id']; ?>">
                <p>Order Status:</p>
                <select name="order_status" class="drop-down">
                    <option value="" selected disabled><?= $fetch_orders['order_status']; ?></option>
                    <option value="Preparing your Food">Preparing your Food</option>
                    <option value="Completed">Completed</option>
                </select>

                <div class="flex-btn">
                    <input type="submit" name="update_payment" class="btn btn-success" value="Update" <?= $fetch_orders['order_status'] === 'Completed' ? 'disabled' : '' ?>>
                    <button type="button" class="btn btn-danger" onclick="showDeleteConfirmation(<?= $fetch_orders['id']; ?>)">Delete</button>
                </div>
            </form>
        </div>
        <?php
            }
        } else {
            echo '<p class="empty">No orders yet</p>';
        }
        ?>
    </div>
</section>

<?php if (!empty($message)) : ?>
<script>
    Swal.fire({
        title: 'Out for delivery!',
        text: 'Your order is on the way.',
        icon: 'success',
        confirmButtonText: 'OK',
        customClass: {
            popup: 'my-custom-sweetalert'
        }
    });
</script>
<?php endif; ?>

<script>
function showDeleteConfirmation(orderId) {
    Swal.fire({
        title: 'Warning!',
        text: 'Are you sure you want to remove this receipt?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Confirm',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "placed_orders.php?delete=" + orderId;
        }
    });
}
</script>

<script src="../js/admin_script.js"></script>
</body>
</html>

<?php
include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id']) && isset($_POST['order_id'])) {
    $user_id = $_SESSION['user_id'];
    $order_id = $_POST['order_id'];

    // Fetch the order details
    $select_order = $conn->prepare("SELECT * FROM [orders] WHERE id = ? AND user_id = ?");
    $select_order->execute([$order_id, $user_id]);

    if ($order_details = $select_order->fetch(PDO::FETCH_ASSOC)) {
        // Insert into cancelled_orders table
        $insert_cancelled_order = $conn->prepare("INSERT INTO [cancelled_orders] (user_id, order_id, cancelled_on) VALUES (?, ?, GETDATE())");
        $insert_cancelled_order->execute([$user_id, $order_id]);

        // Delete from original orders table
        $delete_order = $conn->prepare("DELETE FROM [orders] WHERE id = ? AND user_id = ?");
        $delete_order->execute([$order_id, $user_id]);

        header('Location: cancelled_orders.php');
        exit();
    }
}

// Redirect to home if something is invalid
header('Location: home.php');
exit();
?>

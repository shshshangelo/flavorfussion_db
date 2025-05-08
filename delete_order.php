<?php

include 'components/connect.php';

if (isset($_GET['id'])) {
    $orderId = $_GET['id'];

    // Perform the deletion from the order_history table
    $deleteOrder = $conn->prepare("DELETE FROM `order_history` WHERE id = ?");
    $deleteOrder->execute([$orderId]);

    // Redirect back to the order history page
    header('Location: history.php');
} else {
    // Handle the case where the order ID is not provided
    echo 'Invalid request';
}

?>

<?php
include 'components/connect.php';
session_start();

if (!isset($_GET['id']) || empty($_GET['id'])) {
    exit('Invalid request');
}

$orderId = $_GET['id'];

// Join to get both number and email
$selectOrder = $conn->prepare("
    SELECT oh.*, u.number, u.email 
    FROM [order_history] oh
    JOIN [users] u ON oh.user_id = u.id
    WHERE oh.id = ?
");
$selectOrder->execute([$orderId]);
$orders = $selectOrder->fetchAll(PDO::FETCH_ASSOC);

if (count($orders) > 0) {
    $order = $orders[0];

    $insertOrder = $conn->prepare("
        INSERT INTO [orders] (
            [user_id], [fname], [mname], [lname], [number], [email], [address],
            [total_products], [total_price], [method], [order_status], [placed_on]
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $insertOrder->execute([
        $order['user_id'],
        $order['fname'],
        $order['mname'],
        $order['lname'],
        $order['number'],
        $order['email'],
        $order['address'],
        $order['total_products'],
        $order['total_price'],
        $order['method'],
        'Preparing your Food',
        date('Y-m-d H:i:s')
    ]);

    header('Location: orders.php');
    exit();
    
} else {
    exit('Invalid order ID');
}

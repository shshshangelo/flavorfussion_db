<?php
if (isset($_POST['add_to_cart'])) {

    if ($user_id == '') {
        header('Location: register.php');
        exit;
    } else {
        $pid   = filter_var($_POST['pid'], FILTER_SANITIZE_STRING);
        $name  = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
        $image = filter_var($_POST['image'], FILTER_SANITIZE_STRING); // expecting filename only
        $qty   = filter_var($_POST['qty'], FILTER_SANITIZE_STRING);

        // Check if item already exists in cart
        $check_stmt = $conn->prepare("SELECT * FROM [cart] WHERE [name] = ? AND [user_id] = ?");
        $check_stmt->execute([$name, $user_id]);

        if ($check_stmt->fetch()) {
            $message1[] = 'The selected menu is already in your cart.';
        } else {
            $insert_stmt = $conn->prepare("
                INSERT INTO [cart] ([user_id], [pid], [name], [price], [quantity], [image])
                VALUES (?, ?, ?, ?, ?, ?)
            ");

            $success = $insert_stmt->execute([$user_id, $pid, $name, $price, $qty, $image]);

            if ($success) {
                $message[] = 'Your order is successfully placed to cart.';
            } else {
                $message1[] = 'Failed to add to cart.';
            }
        }
    }
}

// Show success messages
if (!empty($message)) {
    foreach ($message as $msg) {
        echo "
        <script>
        swal({
            title: 'Successfully Ordered',
            text: '$msg',
            icon: 'success',
            button: 'Close'
        });
        </script>
        ";
    }
}

// Show warning messages
if (!empty($message1)) {
    foreach ($message1 as $msg) {
        echo "
        <script>
        swal({
            text: '$msg',
            icon: 'warning',
            button: 'Close'
        });
        </script>
        ";
    }
}
?>

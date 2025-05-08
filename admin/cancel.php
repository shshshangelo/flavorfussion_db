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
    <title>Cancelled Orders</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & FontAwesome -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../css/sales_style.css">

    <style>
        .box-container-cancelled {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .box-cancelled {
            background-color: #6c757d;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin: 15px;
            max-width: 400px;
            color: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            transition: transform 0.2s ease-in-out;
        }

        .box-cancelled:hover {
            transform: scale(1.02);
        }

        .box-cancelled p {
            margin-bottom: 8px;
            font-size: 16px;
        }

        .Empty-cancelled {
            padding: 2rem;
            text-align: center;
            font-size: 20px;
            color: red;
            width: 100%;
        }

        @media screen and (max-width: 768px) {
            .box-cancelled {
                width: calc(50% - 30px);
            }
        }

        @media screen and (max-width: 480px) {
            .box-cancelled {
                width: calc(100% - 30px);
            }
        }
    </style>
</head>

<body>

<?php include '../components/sales_header.php'; ?>

<section class="cancelled-orders">
    <h1 class="heading">Cancelled Orders</h1>

    <div class="box-container-cancelled">
        <?php
        $query = "
            SELECT co.*, u.fname, u.mname, u.lname, u.email, u.number, u.address
            FROM cancelled_orders co
            JOIN users u ON co.user_id = u.id
            ORDER BY co.cancelled_on DESC
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $cancelled_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($cancelled_orders) > 0) {
            foreach ($cancelled_orders as $order) {
        ?>
            <div class="box-cancelled">
                <p>Cancelled On: <span><?= $order['cancelled_on']; ?></span></p>
                <p>First Name: <span><?= $order['fname']; ?></span></p>
                <p>Middle Name: <span><?= $order['mname']; ?></span></p>
                <p>Last Name: <span><?= $order['lname']; ?></span></p>
                <p>Email: <span><?= $order['email']; ?></span></p>
                <p>Number: <span><?= $order['number']; ?></span></p>
                <p>Address: <span><?= $order['address']; ?></span></p>
                <p style="color: #ffc107;">Order Status: <strong>Cancelled</strong></p>
            </div>
        <?php
            }
        } else {
            echo '<p class="Empty-cancelled">No cancelled orders yet!</p>';
        }
        ?>
    </div>
</section>

<script src="../js/sales_script.js"></script>
</body>
</html>

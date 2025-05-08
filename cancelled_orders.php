<?php
include 'components/connect.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cancelled Orders</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>
</head>

<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
    <h3>Cancelled Orders</h3>
    <p><a href="home.php">Home</a> <span> / Cancelled Orders</span></p>
</div>

<section class="cancelled-orders">
    <div class="box-container">
        <?php
        $stmt = $conn->prepare("
            SELECT co.*, u.fname, u.mname, u.lname, u.email, u.number, u.address
            FROM [cancelled_orders] co
            JOIN [users] u ON co.user_id = u.id
            WHERE co.user_id = ?
            ORDER BY co.cancelled_on DESC
        ");
        $stmt->execute([$user_id]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($orders) > 0) {
            foreach ($orders as $order) {
                echo '<div class="box">';
                echo '<p>Cancelled On: <span>' . htmlspecialchars($order['cancelled_on']) . '</span></p>';
                echo '<p>First Name: <span>' . htmlspecialchars($order['fname']) . '</span></p>';
                echo '<p>Middle Name: <span>' . htmlspecialchars($order['mname']) . '</span></p>';
                echo '<p>Last Name: <span>' . htmlspecialchars($order['lname']) . '</span></p>';
                echo '<p>Email: <span>' . htmlspecialchars($order['email']) . '</span></p>';
                echo '<p>Number: <span>' . htmlspecialchars($order['number']) . '</span></p>';
                echo '<p>Address: <span>' . htmlspecialchars($order['address']) . '</span></p>';
                echo '<p style="color: red;">Order Status: <strong>Cancelled</strong></p>';
                echo '</div>';
            }
        } else {
            echo '<p class="empty">No cancelled orders yet!</p>';
        }
        ?>
    </div>
</section>

<div class="loader">
    <img src="images/loader.gif" alt="">
</div>

<script src="js/script.js"></script>

<style>
    .box-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
    }

    .box {
        background-color: green;
        border: 1px solid #ccc;
        border-radius: 10px;
        padding: 20px;
        margin: 10px;
        max-width: 400px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s ease-in-out;
        color: white;
    }

    .box:hover {
        transform: scale(1.02);
    }

    .box p {
        margin-bottom: 5px;
        font-size: 18px;
        color: black;
    }

    span {
        font-weight: bold;
    }

    .empty {
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

    body {
        font-family: 'Arial', sans-serif;
        margin: 0;
        padding: 0;
    }

    .container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }

    @media only screen and (max-width: 600px) {
        body { font-size: 14px; }
        .container { padding: 10px; }
    }

    @media only screen and (min-width: 601px) and (max-width: 900px) {
        body { font-size: 16px; }
        .container { padding: 15px; }
    }

    @media only screen and (min-width: 901px) {
        body { font-size: 18px; }
        .container { padding: 20px; }
    }
</style>

</body>
</html>

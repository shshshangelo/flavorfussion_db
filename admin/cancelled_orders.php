<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    header('location:admin_login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Cancelled Orders</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <style>
        .box-container-cancelled {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
        }

        .box-cancelled {
            background-color: green;
            border-radius: 10px;
            padding: 20px;
            margin: 15px;
            color: white;
            max-width: 400px;
            width: 100%;
            box-shadow: 0 0 8px rgba(0,0,0,0.15);
        }

        .box-cancelled:hover {
            transform: scale(1.01);
        }

        .box-cancelled p {
            font-size: 17px;
            margin-bottom: 8px;
        }

        @media screen and (min-width: 768px) {
            .box-cancelled {
                width: calc(33.33% - 30px);
            }
        }

        .empty-cancelled {
            text-align: center;
            font-size: 20px;
            padding: 2rem;
            color: red;
        }
    </style>
</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="cancelled-orders">
    <h1 class="heading">Cancelled Orders</h1>
    <div class="box-container-cancelled">

        <?php
        $query = "
            SELECT co.cancelled_on, u.fname, u.mname, u.lname, u.email, u.number, u.address
            FROM cancelled_orders co
            JOIN users u ON co.user_id = u.id
            ORDER BY co.cancelled_on DESC
        ";

        $stmt = $conn->prepare($query);
        $stmt->execute();
        $cancelled_orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($cancelled_orders) > 0) {
            foreach ($cancelled_orders as $row) {
        ?>
                <div class="box-cancelled">
                    <p><strong>Cancelled On:</strong> <?= $row['cancelled_on']; ?></p>
                    <p><strong>Customer:</strong> <?= $row['fname'] . ' ' . $row['mname'] . ' ' . $row['lname']; ?></p>
                    <p><strong>Email:</strong> <?= $row['email']; ?></p>
                    <p><strong>Phone:</strong> <?= $row['number']; ?></p>
                    <p><strong>Address:</strong> <?= $row['address']; ?></p>
                    <p style="font-weight: bold; color: red;">Status: Cancelled</p>
                </div>
        <?php
            }
        } else {
            echo '<p class="empty-cancelled">No cancelled orders yet.</p>';
        }
        ?>

    </div>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>

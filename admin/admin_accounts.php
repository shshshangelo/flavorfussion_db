<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;

if (!$admin_id) {
    header('location:admin_login.php');
    exit();
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    
    // Prevent self-deletion
    if ($delete_id != $admin_id) {
        $delete_admin = $conn->prepare("DELETE FROM admin WHERE id = ?");
        $delete_admin->execute([$delete_id]);
    }
    header('location:admin_accounts.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Workers Accounts</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon">

    <!-- Bootstrap, FontAwesome, and SweetAlert -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>

<body>

<?php include '../components/admin_header.php'; ?>

<section class="accounts">
    <h1 class="heading">Workers Accounts</h1>
    <div class="box-container">
        <?php
        $select_account = $conn->prepare("SELECT * FROM admin");
        $select_account->execute();

        if ($select_account->rowCount() > 0) {
            while ($fetch = $select_account->fetch(PDO::FETCH_ASSOC)) {
                $is_self = ($fetch['id'] == $admin_id);
        ?>
            <div class="box">
                <p>Name: <span><?= $fetch['name']; ?></span></p>
                <div class="flex-btn">
                    <?php if (!$is_self): ?>
                        <a href="#" class="delete-btn" onclick="confirmDelete(<?= $fetch['id']; ?>, '<?= $fetch['name']; ?>')">delete</a>
                    <?php endif; ?>

                    <?php if ($is_self): ?>
                        <a href="update_profile.php" class="option-btn">update</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php
            }
        } else {
            echo '<p class="empty">no accounts available</p>';
        }
        ?>
    </div>
</section>

<script>
    function confirmDelete(id, name) {
        Swal.fire({
            title: 'Remove',
            text: `Are you sure you want to remove the account of ${name}?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!',
            customClass: {
                popup: 'larger-sweetalert'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'admin_accounts.php?delete=' + id;
            }
        });
    }
</script>

<style>
    .larger-sweetalert {
        font-size: 18px;
    }
</style>

<script src="../js/admin_script.js"></script>

</body>
</html>

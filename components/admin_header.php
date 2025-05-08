<?php
// No need to call session_start() here if it's already started in the parent page

include '../components/connect.php';

$admin_id = $_SESSION['admin_id'] ?? null;

$fetch_profile = ['name' => 'HeadChef'];
if ($admin_id && $conn instanceof PDO) {
    $select_profile = $conn->prepare("SELECT * FROM admin WHERE id = ?");
    $select_profile->execute([$admin_id]);
    if ($select_profile->rowCount() > 0) {
        $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
    }
}
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="icon" href="favicon.ico" type="image/x-icon">

<header class="header">
    <section class="flex">
        <a href="dashboard.php" class="logo">HeadChef | <span>Dashboard</span></a>

        <nav class="navbar">
            <a href="products.php">Add A New Menu</a>
            <a href="placed_orders.php">Customer Orders</a>
            <a href="completed_orders.php">Total Orders</a>
            <a href="cancelled_orders.php">Cancel Orders</a>
            <a href="admin_login.php" target="_blank">Management</a>
        </nav>

        <div class="icons">
            <div id="menu-btn" class="fas fa-bars"></div>
            <div id="user-btn" class="fas fa-user"></div>
        </div>

        <div class="profile">
            <p><?= htmlspecialchars($fetch_profile['name']) ?></p>
            <a href="#" class="delete-btn" onclick="confirmLogout()">logout</a>
        </div>
    </section>
</header>

<style>
.swal2-popup {
    font-size: 1.6rem;
}
</style>

<script>
function confirmLogout() {
    Swal.fire({
        title: "Logout",
        text: "Are you sure you want to logout from this website?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, logout!",
        customClass: {
            popup: 'custom-swal-popup',
        },
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "../components/admin_logout.php";
        }
    });
}
</script>

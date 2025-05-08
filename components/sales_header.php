<?php
// Assuming $conn and $admin_id are already available from the parent page
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<header class="header">

   <section class="flex">

      <a href="sales.php" class="logo">Management | <span>Dashboard</span></a>

      <nav class="navbar">
         <a href="pending.php">Pending Orders</a>
         <a href="complete.php">Completed Orders</a>
         <a href="cancel.php">Cancelled Orders</a>
         <a href="messages.php">Feedbacks</a>
         <a href="users_accounts.php">Users</a>
      </nav>

      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>

      <div class="profile">
         <?php
            $select_profile = $conn->prepare("SELECT * FROM admin WHERE id = ?");
            $select_profile->execute([$admin_id]);
            $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
         ?>
         <p><?= htmlspecialchars($fetch_profile['name']); ?></p>
         <div class="flex-btn">
            <a href="#" class="delete-btn" onclick="confirmLogout()">Logout</a>
         </div>
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
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = "../components/admin_logout.php";
        }
    });
}
</script>

<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
   header('location:admin_login.php');
   exit();
}

// Delete user and related data
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   $conn->prepare("DELETE FROM [cart] WHERE user_id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM [orders] WHERE user_id = ?")->execute([$delete_id]);
   $conn->prepare("DELETE FROM [users] WHERE id = ?")->execute([$delete_id]);

   header('location:users_accounts.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Users Accounts</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <!-- Bootstrap + jQuery -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js"></script>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

   <!-- SweetAlert2 -->
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>

   <link rel="icon" href="favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="../css/admin_style.css">

   <style>
      .box-container {
         display: flex;
         flex-wrap: wrap;
         justify-content: center;
      }

      .box {
         border: 1px solid #ccc;
         border-radius: 10px;
         padding: 15px;
         margin: 10px;
         max-width: 400px;
         width: 100%;
         background-color: #f9f9f9;
         box-shadow: 0 0 8px rgba(0,0,0,0.1);
      }

      .box p {
         font-size: 15px;
         margin: 5px 0;
      }

      .box span {
         font-weight: bold;
      }

      .delete-btn {
         display: inline-block;
         background: #dc3545;
         color: white;
         padding: 8px 12px;
         margin-top: 10px;
         text-decoration: none;
         border-radius: 4px;
         font-size: 14px;
      }

      .delete-btn:hover {
         background: #bd2130;
      }

      .heading {
         text-align: center;
         font-size: 28px;
         margin: 30px 0 20px;
      }

      .empty {
         text-align: center;
         font-size: 20px;
         color: red;
         margin-top: 20px;
      }

      .larger-sweetalert {
         font-size: 18px;
      }
   </style>
</head>

<body>

<?php include '../components/sales_header.php'; ?>

<section class="accounts">
   <h1 class="heading">Users Account</h1>

   <div class="box-container">
      <?php
      try {
         $select_account = $conn->prepare("SELECT * FROM [users]");
         $select_account->execute();
         $users = $select_account->fetchAll(PDO::FETCH_ASSOC);

         if (count($users) > 0) {
            foreach ($users as $user) {
      ?>
         <div class="box">
            <p>First Name: <span><?= htmlspecialchars($user['fname']); ?></span></p>
            <p>Middle Name: <span><?= htmlspecialchars($user['mname']); ?></span></p>
            <p>Last Name: <span><?= htmlspecialchars($user['lname']); ?></span></p>
            <p>Email: <span><?= htmlspecialchars($user['email']); ?></span></p>
            <p>Number: <span><?= htmlspecialchars($user['number']); ?></span></p>
            <p>Address: <span><?= htmlspecialchars($user['address']); ?></span></p>
            <a href="#" class="delete-btn" onclick="confirmDelete(<?= $user['id']; ?>)">Delete</a>
         </div>
      <?php
            }
         } else {
            echo '<p class="empty">No accounts available</p>';
         }
      } catch (PDOException $e) {
         echo '<p class="empty">Query Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
      }
      ?>
   </div>
</section>

<script>
   function confirmDelete(userId) {
      Swal.fire({
         title: 'Remove',
         text: 'You want to remove this account?',
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
            window.location.href = 'users_accounts.php?delete=' + userId;
         }
      });
   }
</script>

<script src="../js/admin_script.js"></script>

</body>
</html>

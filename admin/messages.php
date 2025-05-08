<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'] ?? null;
if (!$admin_id) {
    header('location:admin_login.php');
    exit();
}

// DELETE message logic (for AJAX call)
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_message = $conn->prepare("DELETE FROM [messages] WHERE id = ?");
    $delete_message->execute([$delete_id]);
    exit(); // return nothing for AJAX
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <title>Messages</title>
   <meta name="viewport" content="width=device-width, initial-scale=1.0">

   <!-- Bootstrap & FontAwesome -->
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
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
   </style>
</head>
<body>

<?php include '../components/sales_header.php'; ?>

<section class="messages">
   <h1 class="heading">Feedbacks</h1>

   <div class="box-container">
   <?php
   try {
      $select_messages = $conn->prepare("SELECT * FROM [messages] ORDER BY id DESC");
      $select_messages->execute();
      $messages = $select_messages->fetchAll(PDO::FETCH_ASSOC);

      if (count($messages) > 0) {
         foreach ($messages as $msg) {
   ?>
      <div class="box">
         <p>First Name: <span><?= htmlspecialchars($msg['fname']); ?></span></p>
         <p>Middle Name: <span><?= htmlspecialchars($msg['mname']); ?></span></p>
         <p>Last Name: <span><?= htmlspecialchars($msg['lname']); ?></span></p>
         <p>Mobile Number: <span><?= htmlspecialchars($msg['number']); ?></span></p>
         <p>Email: <span><?= htmlspecialchars($msg['email']); ?></span></p>
         <p>Message: <span><?= nl2br(htmlspecialchars($msg['message'])); ?></span></p>
         <p>Rating: <span><?= htmlspecialchars($msg['rating']); ?></span></p>
         <button class="btn btn-danger btn-sm mt-2" onclick="showConfirmAlert(<?= $msg['id']; ?>)">
            <i class="fas fa-trash-alt"></i> Delete
         </button>
      </div>
   <?php
         }
      } else {
         echo '<p class="empty">You have no messages</p>';
      }
   } catch (PDOException $e) {
      echo '<p class="empty">Query Error: ' . htmlspecialchars($e->getMessage()) . '</p>';
   }
   ?>
   </div>
</section>

<script>
   function showConfirmAlert(deleteId) {
      Swal.fire({
         title: 'Are you sure?',
         text: 'You won\'t be able to recover this message!',
         icon: 'warning',
         showCancelButton: true,
         confirmButtonColor: '#d33',
         cancelButtonColor: '#3085d6',
         confirmButtonText: 'Yes, delete it!'
      }).then((result) => {
         if (result.isConfirmed) {
            $.ajax({
               type: 'GET',
               url: 'messages.php?delete=' + deleteId,
               success: function () {
                  Swal.fire(
                     'Deleted!',
                     'The message has been deleted.',
                     'success'
                  ).then(() => {
                     location.reload();
                  });
               }
            });
         }
      });
   }
</script>

<script src="../js/admin_script.js"></script>
</body>
</html>

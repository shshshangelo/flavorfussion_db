<?php
include 'components/connect.php';
session_start();

$user_id = $_SESSION['user_id'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Customer Feedbacks</title>

   <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css" />
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <link rel="stylesheet" href="css/style.css">
   <link rel="icon" href="images/favicon.ico" type="image/x-icon">

   <style>
      .heading {
         text-align: center;
         margin-bottom: 2rem;
         text-transform: capitalize;
         color: var(--white);
         font-size: 5rem;
      }

      .messages .box-container {
         display: grid;
         grid-template-columns: repeat(auto-fit, minmax(330px, 1fr));
         gap: 1.5rem;
         padding: 2rem;
      }

      .messages .box-container .box {
         background-color: brown;
         border-radius: .5rem;
         box-shadow: var(--box-shadow);
         border: var(--border);
         padding: 2rem;
         color: #fff;
      }

      .messages .box-container .box p {
         font-size: 1.8rem;
         font-weight: bold;
         margin-bottom: 0.5rem;
      }

      .messages .box-container .box p span {
         color: black;
         font-weight: normal;
      }

      h3 {
         font-size: 3.5rem;
         color: white;
         text-align: center;
         margin-bottom: 15px;
      }

      #scrollToTopBtn {
         position: fixed;
         bottom: 20px;
         right: 20px;
         background: #222;
         color: white;
         padding: 0.5rem 1rem;
         font-size: 1.5rem;
         border: none;
         border-radius: 5px;
         cursor: pointer;
         display: none;
         z-index: 1000;
      }

      .empty {
         text-align: center;
         font-size: 2rem;
         color: red;
         margin: 3rem auto;
      }

      @media (max-width: 600px) {
         h3 {
            font-size: 2.5rem;
         }
      }
   </style>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<!-- Scroll to Top Button -->
<button id="scrollToTopBtn" title="Go to top">&#9650;</button>

<section class="messages">
   <h3>Customer's Feedbacks</h3>
   <div class="box-container">
      <?php
         $select_messages = $conn->prepare("SELECT * FROM messages ORDER BY id DESC");
         $select_messages->execute();
         $messages = $select_messages->fetchAll(PDO::FETCH_ASSOC);

         if (count($messages) > 0) {
            foreach ($messages as $fetch) {
               ?>
               <div class="box">
                  <p>First Name: <span><?= htmlspecialchars($fetch['fname']) ?></span></p>
                  <p>Middle Name: <span><?= htmlspecialchars($fetch['mname']) ?></span></p>
                  <p>Last Name: <span><?= htmlspecialchars($fetch['lname']) ?></span></p>
                  <p>Message: <span><?= nl2br(htmlspecialchars($fetch['message'])) ?></span></p>
                  <p>Rating: <span><?= htmlspecialchars($fetch['rating']) ?></span></p>
               </div>
               <?php
            }
         } else {
            echo '<p class="empty">No feedbacks yet</p>';
         }
      ?>
   </div>
</section>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script src="js/script.js"></script>
<script>
   // Scroll to top button visibility
   const scrollBtn = document.getElementById("scrollToTopBtn");
   window.onscroll = () => {
      scrollBtn.style.display = window.scrollY > 100 ? "block" : "none";
   };
   scrollBtn.onclick = () => {
      window.scrollTo({ top: 0, behavior: 'smooth' });
   };
</script>

<div class="loader">
   <img src="images/loader.gif" alt="Loading...">
</div>

</body>
</html>

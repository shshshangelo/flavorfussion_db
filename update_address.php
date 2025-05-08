<?php
include 'components/connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
   header('location:home.php');
   exit;
}

$user_id = $_SESSION['user_id'];
$message = [];

if (isset($_POST['submit'])) {
   $city = htmlspecialchars(trim($_POST['city']));
   $barangay = htmlspecialchars(trim($_POST['barangay']));
   $postal_code = htmlspecialchars(trim($_POST['postal_code']));
   $area = htmlspecialchars(trim($_POST['area']));

   $address = "$city, $barangay, $postal_code, $area";

   $update_address = $conn->prepare("UPDATE [users] SET [address] = ? WHERE [id] = ?");
   $update_address->execute([$address, $user_id]);

   $message[] = 'Your address was successfully changed.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Update Address</title>
   <link rel="stylesheet" href="css/style.css">
   <link rel="icon" href="images/favicon.ico" type="image/x-icon">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
   <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container" style="margin-top: 100px;">
   <form action="" method="post">
      <h3>Enter your Full Address</h3>
      <input type="text" class="box" placeholder="Enter City" required maxlength="50" name="city">
      <input type="text" class="box" placeholder="Enter Barangay" required maxlength="50" name="barangay">
      <input type="text" class="box" placeholder="Postal Code" required inputmode="numeric" pattern="[0-9]{4,6}" name="postal_code">
      <input type="text" class="box" placeholder="Street Name, Building, House No." required maxlength="100" name="area">
      <input type="submit" value="Save Address" name="submit" class="btn">
      <a href="checkout.php" class="btn">Back</a>
   </form>
</section>

<?php if (!empty($message)) : ?>
<script>
   swal({
      title: "Success!",
      text: "Your profile address was successfully updated.",
      icon: "success",
      button: "OK",
   }).then(function () {
      window.location.href = "checkout.php";
   });
</script>
<?php endif; ?>

<script src="js/script.js"></script>

<div class="loader">
   <img src="images/loader.gif" alt="Loading...">
</div>

<style>
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

<?php

include 'components/connect.php';
session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    header('location:home.php');
    exit();
}

try {
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $conn->exec("SET NOCOUNT ON");

    $select_profile = $conn->prepare("SELECT * FROM [users] WHERE [id] = ?");
    $select_profile->execute([$user_id]);

    $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

    if (!$fetch_profile) {
        echo "<h2 style='color:red'>❌ No user found in [users] table with ID = $user_id</h2>";

        // Debug info
        $stmt = $conn->query("SELECT COUNT(*) as total FROM [users]");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p>Total rows in [users] table: " . $count['total'] . "</p>";

        $test = $conn->query("SELECT [id], [fname] FROM [users]");
        while ($row = $test->fetch(PDO::FETCH_ASSOC)) {
            echo "User ID: {$row['id']} - Name: {$row['fname']}<br>";
        }

        exit();
    }

} catch (PDOException $e) {
    echo "<h3>Query Error: " . $e->getMessage() . "</h3>";
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<div class="heading">
    <h3>Profile</h3>
    <p><a href="home.php">Home</a> <span> / My Profile Information</span></p>
</div>

<section class="user-details">
    <div class="box-container">
        <div class="box">
            <img src="images/customer.png" alt="Customer Avatar" style="max-width: 120px; border-radius: 50%; margin-bottom: 1rem;">
            <p>First Name: <span><?= htmlspecialchars($fetch_profile['fname'] ?? '') ?></span></p>
            <p>Middle Name: <span><?= htmlspecialchars($fetch_profile['mname'] ?? '') ?></span></p>
            <p>Last Name: <span><?= htmlspecialchars($fetch_profile['lname'] ?? '') ?></span></p>
            <p>Phone Number: <span><?= htmlspecialchars($fetch_profile['number'] ?? 'N/A') ?></span></p>
            <p>Email: <span><?= htmlspecialchars($fetch_profile['email'] ?? 'N/A') ?></span></p>
            <p>Address: 
                <span><?= empty($fetch_profile['address']) ? 'Enter your Full Address' : htmlspecialchars($fetch_profile['address']) ?></span>
            </p>
            <div style="margin-top: 1rem;">
                <a href="update_profile.php" class="btn">Update Profile</a>
                <a href="update_address.php" class="btn">Update Address</a>
            </div>
        </div>
    </div>
</section>

<div class="loader">
    <img src="images/loader.gif" alt="">
</div>

<script src="js/script.js"></script>

<style>
body {
  font-family: 'Arial', sans-serif;
  margin: 0;
  padding: 0;
}

.box-container {
  display: flex;
  justify-content: center;
  padding: 1rem;
}

.box {
  background: #f9f9f9;
  padding: 2rem;
  border-radius: 10px;
  box-shadow: 0 0 10px rgba(0,0,0,0.1);
  max-width: 500px;
  width: 100%;
  text-align: center;
}

.box p {
  font-size: 16px;
  margin: 10px 0;
}

.box p span {
  font-weight: bold;
  color: #333;
}

.btn {
  display: inline-block;
  margin: 0.3rem;
}

@media only screen and (max-width: 600px) {
  body { font-size: 14px; }
  .box { padding: 1rem; }
}
@media only screen and (min-width: 601px) and (max-width: 900px) {
  body { font-size: 16px; }
}
@media only screen and (min-width: 901px) {
  body { font-size: 18px; }
}
</style>

</body>
</html>

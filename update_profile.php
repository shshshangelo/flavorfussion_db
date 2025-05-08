<?php
include 'components/connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$message = array();

if (isset($_POST['submit'])) {
    $fname = filter_var($_POST['fname'], FILTER_SANITIZE_STRING);
    $mname = filter_var($_POST['mname'], FILTER_SANITIZE_STRING);
    $lname = filter_var($_POST['lname'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
    $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);

    if (!empty($fname)) {
        $conn->prepare("UPDATE [users] SET fname = ? WHERE id = ?")->execute([$fname, $user_id]);
        $message[] = 'First name updated successfully!';
    }

    if (!empty($mname)) {
        $conn->prepare("UPDATE [users] SET mname = ? WHERE id = ?")->execute([$mname, $user_id]);
        $message[] = 'Middle name updated successfully!';
    }

    if (!empty($lname)) {
        $conn->prepare("UPDATE [users] SET lname = ? WHERE id = ?")->execute([$lname, $user_id]);
        $message[] = 'Last name updated successfully!';
    }

    if (!empty($email)) {
        $check_email = $conn->prepare("SELECT * FROM [users] WHERE email = ? AND id != ?");
        $check_email->execute([$email, $user_id]);
        if ($check_email->rowCount() > 0) {
            $message[] = 'Email already taken!';
        } else {
            $conn->prepare("UPDATE [users] SET email = ? WHERE id = ?")->execute([$email, $user_id]);
            $message[] = 'Email updated successfully!';
        }
    }

    if (!empty($number)) {
        $check_number = $conn->prepare("SELECT * FROM [users] WHERE number = ? AND id != ?");
        $check_number->execute([$number, $user_id]);
        if ($check_number->rowCount() > 0) {
            $message[] = 'Phone number already taken!';
        } else {
            $conn->prepare("UPDATE [users] SET number = ? WHERE id = ?")->execute([$number, $user_id]);
            $message[] = 'Phone number updated successfully!';
        }
    }
}

// Fetch user profile
$select_profile = $conn->prepare("SELECT * FROM [users] WHERE id = ?");
$select_profile->execute([$user_id]);

$fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);

if (!$fetch_profile) {
    header('location:home.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>
</head>
<body>

<?php include 'components/user_header.php'; ?>

<section class="form-container update-form">
    <form action="" method="post">
        <h3>Update Profile</h3>
        <input type="text" name="fname" placeholder="First Name" class="box" maxlength="50" value="<?= htmlspecialchars($fetch_profile['fname']); ?>">
        <input type="text" name="mname" placeholder="Middle Name" class="box" maxlength="50" value="<?= htmlspecialchars($fetch_profile['mname']); ?>">
        <input type="text" name="lname" placeholder="Last Name" class="box" maxlength="50" value="<?= htmlspecialchars($fetch_profile['lname']); ?>">
        <input type="email" name="email" placeholder="Email" class="box" maxlength="50" value="<?= htmlspecialchars($fetch_profile['email']); ?>">
        <input type="number" name="number" placeholder="Phone Number" class="box" maxlength="11" value="<?= htmlspecialchars($fetch_profile['number']); ?>">
        <input type="submit" value="Update Now" name="submit" class="btn">
        <a href="update_password.php" class="btn">Change Password</a>
        <a href="profile.php" class="btn back-btn">Back</a>
    </form>
</section>

<?php
if (!empty($message)) {
    foreach ($message as $msg) {
        echo "
            <script>
            swal({
                title: 'Update Info',
                text: '" . htmlspecialchars($msg) . "',
                icon: 'success',
                button: 'Okay',
            });
            </script>
        ";
    }
}
?>

<div class="loader">
    <img src="images/loader.gif" alt="">
</div>

<script src="js/script.js"></script>

<style>
body { font-family: 'Arial', sans-serif; margin: 0; padding: 0; }
.container { max-width: 1200px; margin: 0 auto; padding: 20px; }
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

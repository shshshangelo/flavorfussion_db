<?php
include 'components/connect.php';
session_start();

if (empty($_SESSION['user_id'])) {
    header('location:home.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Function to validate the password
function validatePassword($password) {
    return (
        strlen($password) >= 8 &&
        preg_match('/[A-Z]/', $password) &&
        preg_match('/[a-z]/', $password) &&
        preg_match('/[0-9]/', $password) &&
        preg_match('/[^A-Za-z0-9]/', $password)
    );
}

if (isset($_POST['submit'])) {
    $old_pass = filter_var($_POST['old_pass'], FILTER_SANITIZE_STRING);
    $new_pass = filter_var($_POST['new_pass'], FILTER_SANITIZE_STRING);
    $confirm_pass = filter_var($_POST['confirm_pass'], FILTER_SANITIZE_STRING);

    $select_prev_pass = $conn->prepare("SELECT [password] FROM [users] WHERE [id] = ?");
    $select_prev_pass->execute([$user_id]);
    $fetch_prev_pass = $select_prev_pass->fetch(PDO::FETCH_ASSOC);

    if ($fetch_prev_pass) {
        $prev_pass = $fetch_prev_pass['password'];

        if (!password_verify($old_pass, $prev_pass)) {
            $message[] = 'Old password not matched!';
        } elseif ($new_pass != $confirm_pass) {
            $message[] = 'Confirm password not matched!';
        } elseif (!validatePassword($new_pass)) {
            $message[] = 'Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.';
        } else {
            $hashedPassword = password_hash($new_pass, PASSWORD_DEFAULT);

            $update_pass = $conn->prepare("UPDATE [users] SET [password] = ? WHERE [id] = ?");
            $update_pass->execute([$hashedPassword, $user_id]);

            $successMessage = 'Password updated successfully!';
        }
    } else {
        $message[] = 'User not found or session expired.';
    }
}

// Fetch profile for access control
$select_profile = $conn->prepare("SELECT * FROM [users] WHERE [id] = ?");
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
    <title>Update Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="css/style.css">
    <button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>
</head>
<body>

<style>
.password-container {
    position: relative;
}
.eye-icon {
    position: absolute;
    top: 50%;
    right: 10px;
    transform: translateY(-50%);
    cursor: pointer;
    font-size: 20px;
    color: black;
}
</style>

<?php include 'components/user_header.php'; ?>

<section class="form-container update-form">
    <br><br><br><br><br><br><br>
    <form action="update_password.php" method="post">
        <h3>Update Password</h3>

        <div class="password-container">
            <input type="password" name="old_pass" required placeholder="Enter your Old Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <span class="eye-icon" onclick="togglePassword('old_pass')">
                <i class="fas fa-eye" id="eye-icon-old_pass"></i>
            </span>
        </div>

        <div class="password-container">
            <input type="password" name="new_pass" required placeholder="Enter your New Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <span class="eye-icon" onclick="togglePassword('new_pass')">
                <i class="fas fa-eye" id="eye-icon-new_pass"></i>
            </span>
        </div>

        <div class="password-container">
            <input type="password" name="confirm_pass" required placeholder="Confirm your New Password" class="box" maxlength="50" oninput="this.value = this.value.replace(/\s/g, '')">
            <span class="eye-icon" onclick="togglePassword('confirm_pass')">
                <i class="fas fa-eye" id="eye-icon-confirm_pass"></i>
            </span>
        </div>

        <input type="submit" value="Update Now" name="submit" class="btn">
        <a href="update_profile.php" class="btn back-btn">Back</a>
    </form>
</section>

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <script>
            swal({
                title: "Error",
                text: "' . $msg . '",
                icon: "warning",
                button: "Close",
            });
        </script>';
    }
}

if (isset($successMessage)) {
    echo '
    <script>
        swal({
            title: "Success",
            text: "' . $successMessage . '",
            icon: "success",
            button: "Close",
        });
    </script>';
}
?>

<div class="loader">
    <img src="images/loader.gif" alt="">
</div>

<script src="js/script.js"></script>
<script>
function togglePassword(inputId) {
    var passwordInput = document.getElementsByName(inputId)[0];
    var eyeIcon = document.getElementById('eye-icon-' + inputId);

    if (passwordInput.type === "password") {
        passwordInput.type = "text";
        eyeIcon.classList.remove('fa-eye');
        eyeIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = "password";
        eyeIcon.classList.remove('fa-eye-slash');
        eyeIcon.classList.add('fa-eye');
    }
}
</script>

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
}
@media only screen and (min-width: 901px) {
  body { font-size: 18px; }
}
</style>

</body>
</html>

<?php
include '../components/connect.php';
session_start();

if (isset($_POST['submit'])) {
    // Sanitize input
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $pass = filter_var($_POST['pass'], FILTER_SANITIZE_STRING);

    // Query with LOWER to avoid case sensitivity issues
    $query = "SELECT * FROM admin WHERE LOWER(name) = LOWER(?) AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$name, $pass]);

    $fetch_admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($fetch_admin) {
        $_SESSION['admin_id'] = $fetch_admin['id'];

        // Check if Management
        if (stripos($fetch_admin['name'], 'Management') === 0) {
            header('location:sales.php');
            exit();
        } else {
            header('location:dashboard.php');
            exit();
        }
    } else {
        $message[] = 'Incorrect username or password!';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log in</title>
    <link rel="icon" href="favicon.ico" type="image/x-icon" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
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

<?php
if (isset($message)) {
    foreach ($message as $msg) {
        echo '
        <script>
            swal({
                title: "Please try again",
                text: "' . $msg . '",
                icon: "warning",
                button: "Close",
            });
        </script>';
    }
}
?>

<section class="form-container">
    <form action="" method="POST">
        <h3>Sign in</h3>
        <div class="input-container">
            <input type="text" name="name" maxlength="20" required placeholder="Username" class="box" oninput="this.value=this.value.replace(/\s/g,'')">
        </div>
        <div class="input-container password-container">
            <input type="password" name="pass" maxlength="20" required placeholder="Password" class="box" oninput="this.value=this.value.replace(/\s/g,'')">
            <span class="eye-icon" onclick="togglePassword()">
                <i class="fas fa-eye" id="eye-icon-pass"></i>
            </span>
        </div>
        <input type="submit" value="Sign in" name="submit" class="btn">
    </form>
</section>

<script>
function togglePassword() {
    const input = document.getElementsByName('pass')[0];
    const icon = document.getElementById('eye-icon-pass');

    if (input.type === "password") {
        input.type = "text";
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = "password";
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}
</script>

</body>
</html>

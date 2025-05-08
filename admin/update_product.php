<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_GET['update'])) {
    $update_id = $_GET['update'];
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$update_id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        header('location:products.php');
        exit;
    }
}

if (isset($_POST['update_product'])) {
    $id = $_POST['id'];
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $old_image = $_POST['old_image'];
    $image = $_FILES['image']['name'];
    $image_tmp = $_FILES['image']['tmp_name'];
    $image_path = __DIR__ . '/../uploaded_img/' . $image;

    if (!empty($image)) {
        move_uploaded_file($image_tmp, $image_path);
        if (file_exists(__DIR__ . '/../uploaded_img/' . $old_image)) {
            unlink(__DIR__ . '/../uploaded_img/' . $old_image);
        }
        $update_query = $conn->prepare("UPDATE products SET name = ?, price = ?, category = ?, image = ? WHERE id = ?");
        $update_query->execute([$name, $price, $category, $image, $id]);
    } else {
        $update_query = $conn->prepare("UPDATE products SET name = ?, price = ?, category = ? WHERE id = ?");
        $update_query->execute([$name, $price, $category, $id]);
    }

    echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({ title: "Success!", text: "Menu updated successfully.", icon: "success" })
            .then(() => { window.location.href = "products.php"; });
        });
    </script>';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Menu</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
    <link rel="icon" href="favicon.ico" type="image/x-icon">

</head>
<body>

<?php include '../components/admin_header.php'; ?>

<section class="add-products">
    <form method="POST" enctype="multipart/form-data">
        <h3>Update Menu</h3>
        <input type="hidden" name="id" value="<?= $product['id']; ?>">
        <input type="hidden" name="old_image" value="<?= $product['image']; ?>">

        <input type="text" name="name" class="box" required maxlength="100" value="<?= htmlspecialchars($product['name']); ?>">
        <input type="number" name="price" class="box" required min="0" max="9999999999" value="<?= $product['price']; ?>">

        <select name="category" class="box" required>
            <option value="" disabled>--Select Category--</option>
            <option value="Starter Packs" <?= $product['category'] == 'Starter Packs' ? 'selected' : ''; ?>>Starter Packs</option>
            <option value="Main Dishes" <?= $product['category'] == 'Main Dishes' ? 'selected' : ''; ?>>Main Dishes</option>
            <option value="Desserts" <?= $product['category'] == 'Desserts' ? 'selected' : ''; ?>>Desserts</option>
            <option value="Drinks" <?= $product['category'] == 'Drinks' ? 'selected' : ''; ?>>Drinks</option>
        </select>

        <img src="../uploaded_img/<?= htmlspecialchars($product['image']); ?>" style="max-height:150px;">
        <input type="file" name="image" class="box" accept="image/*">

        <input type="submit" name="update_product" value="Update Menu" class="btn">
        <a href="products.php" class="btn btn-secondary">Cancel</a>
    </form>
</section>

<script src="../js/admin_script.js"></script>
</body>
</html>

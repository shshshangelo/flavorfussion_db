<?php
include '../components/connect.php';
session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

if (isset($_POST['add_product'])) {
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);

    $image = filter_var($_FILES['image']['name'], FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = __DIR__ . '/../uploaded_img/' . $image;

    $select_products = $conn->prepare("SELECT * FROM products WHERE name = ?");
    $select_products->execute([$name]);

    if ($select_products->fetch()) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({ title: "Error!", text: "Menu name already exists!", icon: "error" });
            });
        </script>';
    } elseif ($image_size > 2000000) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({ title: "Error!", text: "Image size is too large! (max 2MB)", icon: "error" });
            });
        </script>';
    } elseif (!in_array(pathinfo($image, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif', 'webp'])) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({ title: "Error!", text: "Invalid image format! Use JPG, PNG, or GIF.", icon: "error" });
            });
        </script>';
    } else {
        if (move_uploaded_file($image_tmp_name, $image_folder)) {
            $insert_product = $conn->prepare("INSERT INTO products (name, category, price, image) VALUES (?, ?, ?, ?)");
            $insert_product->execute([$name, $category, $price, $image]);

            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({ title: "Success!", text: "New menu successfully added.", icon: "success" })
                    .then(() => { window.location.href = "products.php"; });
                });
            </script>';
        } else {
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({ title: "Error!", text: "Failed to upload image.", icon: "error" });
                });
            </script>';
        }
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $select = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $select->execute([$delete_id]);
    $fetch = $select->fetch(PDO::FETCH_ASSOC);

    if ($fetch) {
        $image_path = __DIR__ . '/../uploaded_img/' . $fetch['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }

        $conn->prepare("DELETE FROM products WHERE id = ?")->execute([$delete_id]);
        $conn->prepare("DELETE FROM cart WHERE pid = ?")->execute([$delete_id]);

        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({ title: "Deleted!", text: "Menu removed successfully.", icon: "success" })
                .then(() => { window.location.href = "products.php"; });
            });
        </script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add A New Menu</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
    <link rel="icon" href="favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="../css/admin_style.css">
</head>
<body>
<?php include '../components/admin_header.php'; ?>

<section class="add-products">
    <form method="POST" enctype="multipart/form-data">
        <h3>Add a New Menu</h3>
        <input type="text" name="name" placeholder="Menu Name" maxlength="100" required class="box">
        <input type="number" name="price" placeholder="Menu Price" min="0" max="9999999999" required class="box">
        <select name="category" class="box" required>
            <option value="" disabled selected>--Select Category--</option>
            <option value="Starter Packs">Starter Packs</option>
            <option value="Main Dishes">Main Dishes</option>
            <option value="Desserts">Desserts</option>
            <option value="Drinks">Drinks</option>
        </select>
        <input type="file" name="image" accept="image/*" required class="box">
        <input type="submit" name="add_product" value="Add The Menu" class="btn">
    </form>
</section>

<section class="show-products">
    <div class="box-container">
        <?php
        $products = $conn->prepare("SELECT * FROM products ORDER BY id DESC");
        $products->execute();
        $all_products = $products->fetchAll(PDO::FETCH_ASSOC);

        if (count($all_products) > 0) {
            foreach ($all_products as $product) {
                ?>
                <div class="box">
                    <img src="../uploaded_img/<?= htmlspecialchars($product['image']) ?>" alt="" style="max-width: 100%; height: auto;">
                    <div class="flex">
                    <div class="price">₱<?= number_format($product['price'], 2); ?></div>
                    <div class="category"><?= $product['category']; ?></div>
                    </div>
                    <div class="name"><?= $product['name']; ?></div>
                    <div class="flex-btn">
                        <a href="update_product.php?update=<?= $product['id']; ?>" class="option-btn">update</a>
                        <a href="#" class="delete-btn" onclick="confirmDelete(<?= $product['id']; ?>)">delete</a>
                    </div>
                </div>
                <?php
            }
        } else {
            echo '<p class="empty">No new menu added yet.</p>';
        }
        ?>
    </div>
</section>

<script>
function confirmDelete(id) {
   Swal.fire({
      title: 'Are you sure?',
      text: "This will permanently delete the menu item.",
      icon: 'warning',
      showCancelButton: true,
      confirmButtonColor: '#d33',
      cancelButtonColor: '#3085d6',
      confirmButtonText: 'Yes, delete it.',
      cancelButtonText: 'Cancel'
   }).then((result) => {
      if (result.isConfirmed) {
         window.location.href = `products.php?delete=${id}`;
      }
   });
}
</script>

<script src="../js/admin_script.js"></script>
</body>
</html>

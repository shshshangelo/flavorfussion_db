<?php
include '../components/connect.php';
session_start();
$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
    header('location:admin_login.php');
}

if (isset($_POST['add_product'])) {
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);
    $category = $_POST['category'];
    $category = filter_var($category, FILTER_SANITIZE_STRING);

    $image = $_FILES['image']['name'];
    $image = filter_var($image, FILTER_SANITIZE_STRING);
    $image_size = $_FILES['image']['size'];
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_folder = '../uploaded_img/' . $image;

    $select_products = $conn->prepare("SELECT * FROM `products` WHERE name = ?");
    $select_products->execute([$name]);

    if ($select_products->rowCount() > 0) {
        echo '<script>
            document.addEventListener("DOMContentLoaded", function() {
                Swal.fire({
                    title: "Error!",
                    text: "Menu name already exists!",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            });
        </script>';
    } else {
        if ($image_size > 2000000) {
            $message[] = 'image size is too large';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);

            $insert_product = $conn->prepare("INSERT INTO `products`(name, category, price, image) VALUES(?,?,?,?)");
            $insert_product->execute([$name, $category, $price, $image]);

             // Display success message using SweetAlert2 and redirect
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: "Success!",
                text: "New menu successfully added.",
                icon: "success",
                confirmButtonText: "OK"
            }).then(() => {
                // Redirect to another page if needed
                // window.location.href = "your_redirect_page.php";
            });
        });
    </script>';
}
}
}



if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $delete_product_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $delete_product_image->execute([$delete_id]);
    $fetch_delete_image = $delete_product_image->fetch(PDO::FETCH_ASSOC);

    // Delete the product and its image
    unlink('../uploaded_img/' . $fetch_delete_image['image']);
    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $delete_product->execute([$delete_id]);

    // Also delete the product from the cart
    $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE pid = ?");
    $delete_cart->execute([$delete_id]);

    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        Swal.fire({
            title: "Success!",
            text: "Successfully removed menu.",
            icon: "success",
            confirmButtonText: "OK"
        }).then(() => {
            // Redirect to another page if needed
            window.location.href = "products.php";
        });
    });
</script>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Lists</title>

    <!-- Add SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <!-- Add Bootstrap CSS and JS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>

<link rel="icon" href="images/favicon.ico" type="image/x-icon">


    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- custom css file link  -->
    <link rel="stylesheet" href="../css/admin_style.css">
</head>


<button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>


<body>

    <?php include '../components/admin_header.php' ?>

    <!-- add products section starts  -->

    <!-- add products section ends -->

    <style>
        /* Add custom CSS to make SweetAlert2 message larger */
        .swal2-popup {
            font-size: 1.5em; /* Adjust the font size as needed */
        }
    </style>

    <!-- show products section starts  -->

    <section class="show-products" style="padding-top: 0;">

        <div class="box-container">

            <?php
$show_products = $conn->prepare("SELECT * FROM `products` ORDER BY id DESC");
$show_products->execute();
            if ($show_products->rowCount() > 0) {
                while ($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)) {
            ?>
            <div class="box">
                <img src="../uploaded_img/<?= $fetch_products['image']; ?>" alt="">
                <div class="flex">
                    <div class="price"><span>₱</span><?= $fetch_products['price']; ?></div>
                    <div class="category"><?= $fetch_products['category']; ?></div>
                </div>
                <div class="name"><?= $fetch_products['name']; ?></div>
<div class="flex-btn">
    <!-- Modal -->
<div class="modal fade" id="deleteModal<?= $fetch_products['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title" id="deleteModalLabel">Remove Menu</h1>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
            <p style="font-size: 20px;">Are you sure you want to remove this menu?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <a href="products.php?delete=<?= $fetch_products['id']; ?>" class="btn btn-danger">Confirm</a>
            </div>
        </div>
    </div>
</div>

</div>

            </div>
            <?php
                }
            } else {
                echo '<p class="empty">no new menu, added yet!</p>';
            }
            ?>

        </div>

    </section>

    <!-- show products section ends -->

    <!-- custom js file link  -->
    <script src="../js/admin_script.js"></script>



</body>

</html>

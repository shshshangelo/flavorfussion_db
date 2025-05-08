<?php
include 'components/connect.php';

session_start();

if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
} else {
    $user_id = '';
    header('location:home.php');
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $card_holder_name = $_POST['card_holder_name'];
    $cardNumber = $_POST['card_number'];
    $expiryDate = $_POST['expiry_date'];
    $cvv = $_POST['cvv'];

    $insertCard = $conn->prepare("INSERT INTO `cards` (user_id, card_holder_name, card_number, expiry_date, cvv) VALUES (?, ?, ?, ?, ?)");
    $insertCard->execute([$user_id, $card_holder_name, $cardNumber, $expiryDate, $cvv]);

    $_SESSION['selected_payment_method'] = 'Card';

    header("Location: checkout.php?paymentSuccess=true&paymentMethod=Card");
    exit();

    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Card Payment</title>
    
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

<!-- font awesome cdn link  -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

<!-- custom css file link  -->
<link rel="stylesheet" href="css/style.css">

<button id="scrollToTopBtn" aria-label="Scroll to Top">&#9650;</button>


</head>

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f8f8;
        margin: 0;
        padding: 0;
    }

    h1 {
        text-align: center;
        margin-top: 30px;
        color: #fff;
        font-size: 20px;
    }

    form {
        max-width: 500px;
        margin: 5px auto;
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }

    label {
        display: block;
        margin-bottom: 12px;
        font-size: 16px;
    }

    input {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        box-sizing: border-box;
    }

    button {
        background-color: #4caf50;
        color: #fff;
        padding: 15px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-size: 18px;
        width: 100%;
    }

    button:hover {
        background-color: #45a049;
    }
</style>

<body>

    <h1>Card Payment</h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

        <label for="card_holder_name">Full Name</label>
        <input type="text" id="card_holder_name" name="card_holder_name" placeholder="Full Name" required>

        <label for="card_number">Card Number:</label>
        <input type="text" id="card_number" name="card_number" placeholder="1234-1234-1234-1234" required
               oninput="formatCardNumber(this); limitCardNumber(this, 19);">

        <label for="expiry_date">Expiry Date (MM/YYYY):</label>
        <input type="text" id="expiry_date" name="expiry_date" placeholder="MM/YYYY" required
               oninput="formatExpiryDate(this); limitExpiryDate(this, 6);">

        <label for="cvv">CVV:</label>
        <input type="text" id="cvv" name="cvv" placeholder="CVV" required maxlength="3">

        <button type="button" onclick="showConfirmation()">Submit Payment</button>
    </form>

    <!-- custom js file link  -->
    <script src="js/script.js"></script>

    <script>
        function formatCardNumber(input) {
            let cardNumber = input.value.replace(/\D/g, '');
            cardNumber = cardNumber.replace(/(\d{4})(\d{0,4})(\d{0,4})(\d{0,4})/, function (_, p1, p2, p3, p4) {
                let parts = [p1, p2, p3, p4].filter(Boolean);
                return parts.join('-');
            });
            input.value = cardNumber;
        }

        function limitCardNumber(input, maxLength) {
            if (input.value.length > maxLength) {
                input.value = input.value.slice(0, maxLength);
            }
        }

        function limitExpiryDate(input, maxLength) {
            let expiryDate = input.value.replace(/\D/g, '');
            if (expiryDate.length > maxLength) {
                expiryDate = expiryDate.slice(0, maxLength);
            }
            expiryDate = expiryDate.replace(/(\d{2})(\d{0,4})/, function (_, p1, p2) {
                return p1 + (p2 ? '/' + p2 : '');
            });
            input.value = expiryDate;
        }

        function formatExpiryDate(input) {
            let expiryDate = input.value.replace(/\D/g, '');
            expiryDate = expiryDate.replace(/(\d{2})(\d{0,4})/, function (_, p1, p2) {
                return p1 + (p2 ? '/' + p2 : '');
            });
            input.value = expiryDate;
        }

        // Function to show SweetAlert confirmation
    function showConfirmation() {
        swal({
            title: "Submit Payment",
            text: "Are you sure you want to submit the payment?",
            icon: "warning",
            buttons: true,
            dangerMode: true,
        })
        .then((willSubmit) => {
            if (willSubmit) {
                // If user clicks "OK" in the confirmation, submit the form
                document.querySelector("form").submit();
            }
        });
    }
    </script>

<style>
   /* Base styles for all screen sizes */
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

/* Responsive styles using media queries */

/* For screens smaller than 600px */
@media only screen and (max-width: 600px) {
  body {
    font-size: 14px;
  }

  .container {
    padding: 10px;
  }
}

/* For screens between 600px and 900px */
@media only screen and (min-width: 601px) and (max-width: 900px) {
  body {
    font-size: 16px;
  }

  .container {
    padding: 15px;
  }
}

/* For screens larger than 900px */
@media only screen and (min-width: 901px) {
  body {
    font-size: 18px;
  }

  .container {
    padding: 20px;
  }
}
</style>

</body>
</html>

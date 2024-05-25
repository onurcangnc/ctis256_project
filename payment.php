<?php
session_start();

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_SESSION['cart_details']) || empty($_SESSION['cart_details'])) {
    echo "<script>alert('No products in cart. Redirecting to product page.'); window.location.href = 'product.php';</script>";
    exit();
}

$totalAmount = 0;
foreach ($_SESSION['cart_details'] as $details) {
    $totalAmount += $details['price'] * $details['quantity'];
}

if (isset($_POST['iban_pay'])) {
    $iban = $_POST['iban'];
    $amount = $_POST['amount'];

    if ($amount <= 0) {
        echo "<script>alert('Entered amount must be greater than zero.'); window.location.href = 'payment.php';</script>";
        exit();
    }

    $response = simulatePaymentProcessing($iban, $amount, $totalAmount);

    if ($response['success']) {
        // Payment successful, clear the cart
        $_SESSION['cart'] = [];
        $_SESSION['cart_details'] = [];
        echo "<script>alert('Payment successful!'); window.location.href = 'product.php';</script>";
        exit();
    } else {
        echo "<script>alert('Payment failed: " . $response['message'] . "'); window.location.href = 'login.php';</script>";
        exit();
    }
}

function simulatePaymentProcessing($iban, $amount, $totalCost) {
    if ($amount >= $totalCost) {
        return [
            'success' => true,
            'message' => 'Payment processed successfully'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Insufficient amount. Please check your balance.'
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            background: #ddd;
            min-height: 100vh;
            vertical-align: middle;
            display: flex;
        }

        .card {
            margin: auto;
            width: 600px;
            padding: 3rem 3.5rem;
            box-shadow: 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        .mt-50 {
            margin-top: 50px
        }

        .mb-50 {
            margin-bottom: 50px
        }

        @media(max-width:767px) {
            .card {
                width: 90%;
                padding: 1.5rem;
            }
        }

        @media(height:1366px) {
            .card {
                width: 90%;
                padding: 8vh;
            }
        }

        .card-title {
            font-weight: 700;
            font-size: 2.5em;
        }

        .nav {
            display: flex;
        }

        .nav ul {
            list-style-type: none;
            display: flex;
            padding-inline-start: unset;
            margin-bottom: 6vh;
        }

        .nav li {
            padding: 1rem;
        }

        .nav li a {
            color: black;
            text-decoration: none;
        }

        .active {
            border-bottom: 2px solid black;
            font-weight: bold;
        }

        input {
            border: none;
            outline: none;
            font-size: 1rem;
            font-weight: 600;
            color: #000;
            width: 100%;
            min-width: unset;
            background-color: transparent;
            border-color: transparent;
            margin: 0;
        }

        form a {
            color: grey;
            text-decoration: none;
            font-size: 0.87rem;
            font-weight: bold;
        }

        form a:hover {
            color: grey;
            text-decoration: none;
        }

        form .row {
            margin: 0;
            overflow: hidden;
        }

        form .row-1 {
            border: 1px solid rgba(0, 0, 0, 0.137);
            padding: 0.5rem;
            outline: none;
            width: 100%;
            min-width: unset;
            border-radius: 5px;
            background-color: rgba(221, 228, 236, 0.301);
            border-color: rgba(221, 228, 236, 0.459);
            margin: 2vh 0;
            overflow: hidden;
        }

        form .row-2 {
            border: none;
            outline: none;
            background-color: transparent;
            margin: 0;
            padding: 0 0.8rem;
        }

        form .row .row-2 {
            border: none;
            outline: none;
            background-color: transparent;
            margin: 0;
            padding: 0 0.8rem;
        }

        form .row .col-2,
        .col-7,
        .col-3 {
            display: flex;
            align-items: center;
            text-align: center;
            padding: 0 1vh;
        }

        form .row .col-2 {
            padding-right: 0;
        }

        #card-header {
            font-weight: bold;
            font-size: 0.9rem;
        }

        #card-inner {
            font-size: 0.7rem;
            color: gray;
        }

        .three .col-7 {
            padding-left: 0;
        }

        .three {
            overflow: hidden;
            justify-content: space-between;
        }

        .three .col-2 {
            border: 1px solid rgba(0, 0, 0, 0.137);
            padding: 0.5rem;
            outline: none;
            width: 100%;
            min-width: unset;
            border-radius: 5px;
            background-color: rgba(221, 228, 236, 0.301);
            border-color: rgba(221, 228, 236, 0.459);
            margin: 2vh 0;
            width: fit-content;
            overflow: hidden;
        }

        .three .col-2 input {
            font-size: 0.7rem;
            margin-left: 1vh;
        }

        .btn {
            width: 100%;
            background-color: rgb(65, 202, 127);
            border-color: rgb(65, 202, 127);
            color: white;
            justify-content: center;
            padding: 2vh 0;
            margin-top: 3vh;
        }

        .btn:focus {
            box-shadow: none;
            outline: none;
            box-shadow: none;
            color: white;
            -webkit-box-shadow: none;
            -webkit-user-select: none;
            transition: none;
        }

        .btn:hover {
            color: white;
        }

        input:focus::-webkit-input-placeholder {
            color: transparent;
        }

        input:focus:-moz-placeholder {
            color: transparent;
        }

        input:focus::-moz-placeholder {
            color: transparent;
        }

        input:focus:-ms-input-placeholder {
            color: transparent;
        }
    </style>
</head>

<body>
    <div class="card mt-50 mb-50">
        <div class="card-title mx-auto">
            Payment
        </div>
        <form action="" method="POST">
            <span id="card-header">Pay with IBAN:</span>
            <div class="form-group">
                <label for="iban">IBAN</label>
                <input type="text" class="form-control" id="iban" name="iban" placeholder="Enter IBAN" required>
            </div>
            <div class="form-group">
                <label for="amount">Amount</label>
                <input type="number" class="form-control" id="amount" name="amount" placeholder="Enter amount" required>
            </div>
            <button type="submit" class="btn btn-primary mt-3" name="iban_pay">Make IBAN Payment</button>
        </form>
    </div>
</body>

</html>

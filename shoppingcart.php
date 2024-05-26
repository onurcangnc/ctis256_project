<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

if (isset($_GET['remove'])) {
    $product_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
    header("Location: shoppingcart.php");
    exit();
}

if (isset($_POST['proceed_to_pay'])) {
    $_SESSION['cart'] = [];
    header("Location: payment.php");
    exit();
}

if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = ['quantity' => 0];
    }
    $_SESSION['cart'][$productId]['quantity']++;
}

if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $productId => $quantity) {
        if ($quantity == 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }
}

if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

$products = [];
$totalAmount = 0;
$discountedAmount = 0;
$discount = 0;
$discountCodeApplied = false;

if (!empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $idList = implode(',', $productIds);
    $sql = "SELECT * FROM product WHERE id IN ($idList) AND expire > CURDATE()";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $product) {
        $totalAmount += $product['discounted_price'] * $_SESSION['cart'][$product['id']]['quantity'];
    }

    if (isset($_POST['apply_discount']) && $_POST['discount_code'] === '123') {
        $discount = 0.20;
        $discountCodeApplied = true;
        $discountedAmount = $totalAmount * (1 - $discount);
    } else {
        $discountedAmount = $totalAmount;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        .modal .modal-dialog {
            max-width: 400px;
            margin: auto;
            top: 40%;
            transform: translateY(-40%);
        }

        .modal .modal-content {
            border-radius: 10px;
            padding: 20px;
        }

        .modal .modal-header {
            border-bottom: none;
            position: relative;
        }

        .modal .modal-header .btn-close {
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .modal .modal-body {
            text-align: center;
        }

        .modal .modal-body input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .modal .modal-footer {
            border-top: none;
            text-align: center;
        }

        .modal .modal-footer button {
            padding: 10px 20px;
            border-radius: 5px;
        }

        .btn-apply-discount {
            background-color: #28a745;
            color: white;
        }

        .btn-apply-discount:hover {
            background-color: #218838;
        }

        .original-price {
            text-decoration: line-through;
            color: #888;
        }

        .discounted-price {
            color: #e74c3c;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Grocery</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link" href="product.php">Market <span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="membership.php">Membership Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="shoppingcart.php">Shopping cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="addproductai.php">AI Assistant</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <section class="h-100">
        <div class="container h-100 py-5">
            <div class="row d-flex justify-content-center align-items-center h-100">
                <div class="col-10">

                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="fw-normal mb-0 text-black">Shopping Cart</h3>
                        <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#discountModal" <?= empty($products) ? 'disabled' : '' ?>>Apply Discount Code</button>
                    </div>

                    <?php if (empty($products)): ?>
                        <div class="alert alert-warning" role="alert">
                            Your cart is empty!
                        </div>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <div class="card rounded-3 mb-4">
                                <div class="card-body p-4">
                                    <div class="row d-flex justify-content-between align-items-center">
                                        <div class="col-md-2 col-lg-2 col-xl-2">
                                            <img src="uploads/<?= htmlspecialchars($product['img']) ?>" class="img-fluid rounded-3" alt="<?= htmlspecialchars($product['title']) ?>">
                                        </div>
                                        <div class="col-md-3 col-lg-3 col-xl-3">
                                            <p class="lead fw-normal mb-2"><?= htmlspecialchars($product['title']) ?></p>
                                            <p><?= htmlspecialchars($product['description']) ?></p>
                                        </div>
                                        <div class="col-md-3 col-lg-3 col-xl-2 d-flex">
                                            <form action="shoppingcart.php" method="post">
                                                <input type="number" name="quantities[<?= $product['id'] ?>]" value="<?= $_SESSION['cart'][$product['id']]['quantity'] ?>" class="form-control" min="1">
                                                <button class="btn btn-info btn-sm" type="submit" name="update_cart" style="margin-top: 10px;">Update</button>
                                            </form>
                                        </div>
                                        <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                            <h5 class="mb-0">
                                                <?php if ($discountCodeApplied): ?>
                                                    <span class="original-price">$<?= number_format($product['discounted_price'] * $_SESSION['cart'][$product['id']]['quantity'], 2) ?></span>
                                                    <span class="discounted-price">$<?= number_format($product['discounted_price'] * $_SESSION['cart'][$product['id']]['quantity'] * 0.8, 2) ?></span>
                                                <?php else: ?>
                                                    $<?= number_format($product['discounted_price'] * $_SESSION['cart'][$product['id']]['quantity'], 2) ?>
                                                <?php endif; ?>
                                            </h5>
                                        </div>
                                        <div class="col-md-1 col-lg-1 col-xl-1 text-end">
                                            <a href="?remove=<?= $product['id'] ?>" class="text-danger"><i class="fas fa-trash fa-lg"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <form action="shoppingcart.php" method="post">
                        <div class="card">
                            <div class="card-body d-flex justify-content-between">
                                <button href="payment.php" type="submit" name="proceed_to_pay" class="btn btn-warning btn-lg" <?= empty($products) ? 'disabled' : '' ?>>Proceed to Pay</button>
                                <h5 class="text-end">Total: $<?= number_format($discountedAmount, 2) ?></h5>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="discountModal" tabindex="-1" aria-labelledby="discountModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" >
                    <h5 class="modal-title" id="discountModalLabel" >Apply Discount Code</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="shoppingcart.php" method="post">
                        <input type="text" name="discount_code" placeholder="Enter discount code" required>
                        <button type="submit" name="apply_discount" class="btn btn-apply-discount">Apply</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>

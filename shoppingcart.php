<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['is_admin'] != 0) {
    header("Location: addproduct.php");
    exit;
}

if (isset($_GET['remove'])) {//ürün silmek için id alınır ve eğer idler eşleşiyo
    $product_id = $_GET['remove'];
    if (isset($_SESSION['cart'][$product_id])) { //eğer hrefteki product_id tanımlıysa verileri siliyor o product'ın
        unset($_SESSION['cart'][$product_id]); 
    }
    header("Location: shoppingcart.php"); 
    exit();
}

//ödeme işlemine geçince çıkmak ve cartı boşaltmak
if (isset($_POST['proceed_to_pay'])) {
    $_SESSION['cart'] = []; 
    header("Location: payment.php"); 
    exit();
}


if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id']; //formdan gelen idyi product_id'ye atamak
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = ['quantity' => 0];
    }
    $_SESSION['cart'][$productId]['quantity']++;//dynamic olarak artım için
}

// //
if (isset($_POST['update_cart'])) {
    foreach ($_POST['quantities'] as $productId => $quantity) {
        if ($quantity == 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }
}

//logout olunca sepeti temizlemek için
if (isset($_POST['clear_cart'])) {
    $_SESSION['cart'] = [];
}

$products = [];
$totalAmount = 0;
if (!empty($_SESSION['cart'])) {
    $productIds = array_keys($_SESSION['cart']);
    $idList = implode(',', $productIds);
    $sql = "SELECT * FROM product WHERE id IN ($idList) AND expire > CURDATE()";
    $stmt = $pdo->query($sql);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Toplam tutarı hesapla
    foreach ($products as $product) {
        $totalAmount += $product['price'] * $_SESSION['cart'][$product['id']]['quantity'];
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
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Grocery</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
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
                    </div>

                    <?php foreach ($products as $product): ?>
                        <div class="card rounded-3 mb-4">
                            <div class="card-body p-4">
                                <div class="row d-flex justify-content-between align-items-center">
                                    <div class="col-md-2 col-lg-2 col-xl-2">
                                        <img src="uploads/<?= htmlspecialchars($product['img']) ?>"
                                            class="img-fluid rounded-3" alt="<?= htmlspecialchars($product['title']) ?>">
                                    </div>
                                    <div class="col-md-3 col-lg-3 col-xl-3">
                                        <p class="lead fw-normal mb-2"><?= htmlspecialchars($product['title']) ?></p>
                                        <p><?= htmlspecialchars($product['description']) ?></p>
                                    </div>
                                    <div class="col-md-3 col-lg-3 col-xl-2 d-flex">
                                        <form action="shoppingcart.php" method="post">
                                            <input type="number" name="quantities[<?= $product['id'] ?>]"
                                                value="<?= $_SESSION['cart'][$product['id']]['quantity'] ?>" class="form-control"
                                                min="1">
                                            <button class="btn btn-info btn-sm" type="submit" name="update_cart"
                                                style="margin-top: 10px;">Update</button>
                                        </form>
                                    </div>
                                    <div class="col-md-3 col-lg-2 col-xl-2 offset-lg-1">
                                        <h5 class="mb-0">
                                            $<?= number_format($product['price'] * $_SESSION['cart'][$product['id']]['quantity'], 2) ?>
                                        </h5>
                                    </div>
                                    <div class="col-md-1 col-lg-1 col-xl-1 text-end">
                                        <a href="?remove=<?= $product['id'] ?>" class="text-danger"><i
                                                class="fas fa-trash fa-lg"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <form action="shoppingcart.php" method="post">
                        <div class="card">
                            <div class="card-body">
                                <button href="payment.php" type="submit" name="proceed_to_pay"
                                    class="btn btn-warning btn-block btn-lg">Proceed to Pay</button>
                                <h5 class="text-end">Total: $<?= number_format($totalAmount, 2) ?></h5>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </section>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

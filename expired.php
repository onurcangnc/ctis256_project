<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$userEmail = $_SESSION['user_email'];
$isAdmin = ($userEmail === 'admin1@gmail.com');

$query = $isAdmin ? "SELECT * FROM product WHERE expire <= CURDATE()"//admin hepsini görür
:"SELECT * FROM product WHERE market_email = ? AND expire <= CURDATE()";// Market kendi ürünlerini görür admin değilse

$stmt = $pdo->prepare($query);
if ($isAdmin) {
    $stmt->execute();
} else {
    $stmt->execute([$userEmail]);//user mail ile eşlemek ? yerine koymak
}
$expiredProducts = $stmt->fetchAll(PDO::FETCH_ASSOC);//bütün satırları almak expired olan
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Products</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="navbar.css">
    <style>
        body {
            background-color: white;
            min-height: 100vh;
        }

        .text-gray {
            color: #aaa;
        }

        .product-media {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .product-image {
            max-width: 100px;
            height: auto;
        }

        @media (max-width: 767.98px) {
            .product-media {
                flex-direction: column;
                align-items: flex-start;
            }

            .product-image {
                max-width: 80px;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="marketproduct.php">
                <?php if (isset($_SESSION['user_logo']) && !empty($_SESSION['user_logo'])): ?>
                    <img style="width: 100px;" src="<?php echo htmlspecialchars($_SESSION['user_logo']); ?>"
                        alt="Market Logo" style="height: 50px;">
                <?php else: ?>
                    Grocery
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link" href="marketproduct.php">My Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="addproduct.php">Add Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="membershipmarket.php">Membership Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Expired Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row text-center mb-5">
            <div class="col-lg-7 mx-auto">
                <h1 class="display-4">Expired Products</h1>
                <p class="lead mb-0">Here are the expired products</p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mx-auto">
                <ul class="list-group shadow">
                    <?php foreach ($expiredProducts as $product): ?>
                        <li class="list-group-item">
                            <div class="product-media p-3">
                                <div class="media-body">
                                    <h5 class="mt-0 font-weight-bold mb-2"><?= htmlspecialchars($product['title']) ?></h5>
                                    <div class="d-flex align-items-center justify-content-between mt-1">
                                        <h6 class="font-weight-bold my-2">$<?= number_format($product['price'], 2) //ondalık sayı?></h6>
                                    </div>
                                </div>
                                <img src="uploads/<?= htmlspecialchars($product['img']) ?>"
                                    alt="<?= htmlspecialchars($product['title']) ?>"
                                    class="product-image ml-lg-5 order-1 order-lg-2">
                            </div>
                        </li>
                    <?php endforeach; ?>

                </ul>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>

</html>
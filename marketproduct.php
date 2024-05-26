<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['is_admin'] != 1) {
    header("Location: product.php");
    exit;
}

$userEmail = $_SESSION['user_email'];
$isAdmin = ($userEmail === 'admin1@gmail.com');

$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 1000;
$productType = $_GET['product_type'] ?? '';
$searchKeyword = $_GET['search'] ?? '';
$marketEmail = $_GET['market_email'] ?? '';

$query = $isAdmin 
    ? "SELECT * FROM product WHERE price BETWEEN :minPrice AND :maxPrice AND type LIKE :productType AND (title LIKE :searchKeyword OR description LIKE :searchKeyword)" 
    : "SELECT * FROM product WHERE market_email = :marketEmail AND price BETWEEN :minPrice AND :maxPrice AND type LIKE :productType AND (title LIKE :searchKeyword OR description LIKE :searchKeyword)";

$stmt = $pdo->prepare($query);
$stmt->bindValue(':minPrice', $minPrice);
$stmt->bindValue(':maxPrice', $maxPrice);
$stmt->bindValue(':productType', '%' . $productType . '%');
$stmt->bindValue(':searchKeyword', '%' . $searchKeyword . '%');
if (!$isAdmin) {
    $stmt->bindValue(':marketEmail', $userEmail);
}
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product_id'])) {
    $deleteProductId = $_POST['delete_product_id'];
    $deleteStmt = $pdo->prepare("DELETE FROM product WHERE id = ?");
    $deleteStmt->execute([$deleteProductId]);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expired Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="navbar.css">
    <style>
        body {
            background-color: white;
            min-height: 100vh;
        }

        .delete-icon {
            color: red;
            cursor: pointer;
            margin-right: 10px;
        }

        .delete-icon:hover {
            color: darkred;
        }

        .edit-icon {
            color: blue;
            cursor: pointer;
            margin-left: 10px;
        }

        .edit-icon:hover {
            color: darkblue;
        }

        .img-fluid {
            max-width: 100px;
            height: auto;
        }

        .btn-link {
            padding: 0;
            border: none;
            background: none;
        }

        .filter-form {
            margin-bottom: 20px;
        }

        .table thead th {
            vertical-align: middle;
            text-align: center;
        }

        .table tbody td {
            vertical-align: middle;
            text-align: center;
        }

        #tables {
            background-color: blue;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="marketproduct.php">
                <?php if (isset($_SESSION['user_logo']) && !empty($_SESSION['user_logo'])): ?>
                    <img style="width: 100px;" src="<?php echo htmlspecialchars($_SESSION['user_logo']); ?>" alt="Market Logo">
                <?php else: ?>
                    Grocery
                <?php endif; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item active">
                        <a class="nav-link" href="marketproduct.php">All Products<span class="sr-only"></span></a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="addproduct.php">Add Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="membershipmarket.php">Membership Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="expired.php">Expired Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                </ul>
                <form class="d-flex" action="marketproduct.php" method="GET">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search Product" aria-label="Search" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row text-center mb-5">
            <div class="col-lg-7 mx-auto">
                <h1 class="display-4">Your Products</h1>
                <p class="lead mb-0">Your All products</p>
            </div>
        </div>

        <div class="filter-form">
            <form class="row g-3" method="GET" action="marketproduct.php">
                <div class="col-md-3">
                    <input type="number" class="form-control" name="min_price" placeholder="Min Price" value="<?php echo htmlspecialchars($minPrice); ?>">
                </div>
                <div class="col-md-3">
                    <input type="number" class="form-control" name="max_price" placeholder="Max Price" value="<?php echo htmlspecialchars($maxPrice); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-control" name="product_type">
                        <option value="">All Types</option>
                        <option value="Fruit" <?php echo $productType == 'Fruit' ? 'selected' : ''; ?>>Fruit</option>
                        <option value="Vegetable" <?php echo $productType == 'Vegetable' ? 'selected' : ''; ?>>Vegetable</option>
                        <option value="Nut" <?php echo $productType == 'Nut' ? 'selected' : ''; ?>>Nut</option>
                        <option value="Dairy" <?php echo $productType == 'Dairy' ? 'selected' : ''; ?>>Dairy</option>
                        <option value="Meat" <?php echo $productType == 'Meat' ? 'selected' : ''; ?>>Meat</option>
                        <option value="Grain" <?php echo $productType == 'Grain' ? 'selected' : ''; ?>>Grain</option>
                        <option value="Beverage" <?php echo $productType == 'Beverage' ? 'selected' : ''; ?>>Beverage</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                </div>
            </form>
        </div>

        <div class="row">
            <div class="col-lg-12 mx-auto">
                <table class="table table-bordered table-striped table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>Title</th>
                            <th>Expire</th>
                            <th>Image</th>
                            <th>Description</th>
                            <th>Price</th>
                            <th>Discounted Price</th>
                            <th>Type</th>
                            <th>Item Count</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?= htmlspecialchars($product['title']) ?></td>
                                <td><?= htmlspecialchars($product['expire']) ?></td>
                                <td><img src="uploads/<?= htmlspecialchars($product['img']) ?>" alt="<?= htmlspecialchars($product['title']) ?>" class="img-fluid"></td>
                                <td><?= htmlspecialchars($product['description']) ?></td>
                                <td>$<?= number_format($product['price'], 2) ?></td>
                                <td>$<?= number_format($product['discounted_price'], 2) ?></td>
                                <td><?= htmlspecialchars($product['type']) ?></td>
                                <td><?= htmlspecialchars($product['total_item']) ?></td>
                                <td>
                                    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');" style="display:inline;">
                                        <input type="hidden" name="delete_product_id" value="<?= htmlspecialchars($product['id']) ?>">
                                        <button type="submit" class="btn btn-link delete-icon"><i class="far fa-trash-alt"></i></button>
                                    </form>
                                    <a href="updateproduct.php?id=<?= htmlspecialchars($product['id']) ?>" class="btn btn-link edit-icon"><i class="far fa-edit"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
</body>

</html>

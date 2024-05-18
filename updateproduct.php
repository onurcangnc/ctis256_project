<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    // Kullanıcıyı login sayfasına yönlendir
    header("Location: login.php");
    exit;  // Sonrasında scriptin geri kalanını çalıştırmamak için exit kullan
}

$userEmail = $_SESSION['user_email'] ?? null;
$isAdmin = ($userEmail === 'admin1@gmail.com');
$selectedProductId = $_GET['product_id'] ?? null;
$productData = [];

if ($selectedProductId) {
    $productStmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
    $productStmt->execute([$selectedProductId]);
    $productData = $productStmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'] ?? '';
    $expire = $_POST['expire'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $discounted_price = $_POST['discounted_price'] ?? '';

    if (isset($_POST['update_product'])) {
        if ($isAdmin || $productData['market_email'] === $userEmail) {
            $updateStmt = $pdo->prepare("UPDATE product SET title = ?, expire = ?, description = ?, price = ?, discounted_price = ? WHERE id = ?");
            $success = $updateStmt->execute([$title, $expire, $description, $price, $discounted_price, $selectedProductId]);
            if ($success) {
                echo "<div class='alert alert-success'>Product updated successfully.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error updating product: " . implode(" ", $updateStmt->errorInfo()) . "</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>You do not have permission to update this product.</div>";
        }
    } elseif (isset($_POST['delete_product'])) {
        if ($isAdmin || $productData['market_email'] === $userEmail) {
            $deleteStmt = $pdo->prepare("DELETE FROM product WHERE id = ?");
            $deleted = $deleteStmt->execute([$selectedProductId]);
            if ($deleted) {
                echo "<div class='alert alert-success'>Product deleted successfully.</div>";
                header("Location: updateproduct.php");
                exit();
            } else {
                echo "<div class='alert alert-danger'>Error deleting product.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>You do not have permission to delete this product.</div>";
        }
    }
}

// Fetch products depending on the user type
$sql = $isAdmin ? "SELECT id, title FROM product" : "SELECT id, title FROM product WHERE market_email = ?";
$stmt = $pdo->prepare($sql);
$isAdmin ? $stmt->execute() : $stmt->execute([$userEmail]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Product</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="navbar.css">
    <link href="addproduct.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            padding-top: 20px;
        }
        html,
        body {
            overflow: hidden;
        }

        @media (max-width: 1200px) {

            html,
            body {
                overflow: auto;
            }
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="addproduct.php">
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
                        <a class="nav-link" href="addproduct.php">Add Product<span class="sr-only">(current)</span></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="membershipmarket.php">Membership Information</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="updateproduct.php">Update Product</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="expired.php">Expired Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container">
        <h2><b>Update Product</b></h2>
        <form action="updateproduct.php?product_id=<?php echo $selectedProductId; ?>" method="post"
            enctype="multipart/form-data">
            <div class="mb-3">
                <label for="product_id" class="form-label">Select Product</label>
                <select class="form-control" id="product_id" name="product_id"
                    onchange="window.location.href='updateproduct.php?product_id=' + this.value;">
                    <option>Please select</option>
                    <?php foreach ($products as $product): ?>
                        <option value="<?php echo $product['id']; ?>" <?php if ($product['id'] == $selectedProductId)
                               echo 'selected'; ?>>
                            <?php echo htmlspecialchars($product['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="mb-3">
                <label for="title" class="form-label">Product Title</label>
                <input type="text" class="form-control" id="title" name="title" required
                    value="<?php echo htmlspecialchars($productData['title'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="expire" class="form-label">Expiration Date</label>
                <input type="date" class="form-control" id="expire" name="expire" required
                    value="<?php echo htmlspecialchars($productData['expire'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description"
                    required><?php echo htmlspecialchars($productData['description'] ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type of="number" class="form-control" id="price" name="price" step="0.01" required
                    value="<?php echo htmlspecialchars($productData['price'] ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="discounted_price" class="form-label">Discounted Price</label>
                <input type="number" class="form-control" id="discounted_price" name="discounted_price" step="0.01"
                    required value="<?php echo htmlspecialchars($productData['discounted_price'] ?? ''); ?>">
            </div>
            <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
            <button style="margin:10px;" type="submit" name="delete_product" class="btn btn-danger"
                onclick="return confirm('Are you sure you want to delete this product?');">Delete Product</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

$product_id = $_GET['id'] ?? null;
if (!$product_id) {
    header("Location: marketproduct.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $discounted_price = $_POST['discounted_price'];
    $type = $_POST['type'];
    $expire = $_POST['expire'];
    $total_item = $_POST['total_item'];

    $stmt = $pdo->prepare("UPDATE product SET title = ?, description = ?, price = ?, discounted_price = ?, type = ?, expire = ?, total_item = ? WHERE id = ?");
    $stmt->execute([$title, $description, $price, $discounted_price, $type, $expire, $total_item, $product_id]);

    header("Location: marketproduct.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
$stmt->execute([$product_id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$product) {
    header("Location: marketproduct.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Product</h2>
        <form action="updateproduct.php?id=<?= $product_id ?>" method="post">
            <div class="mb-3">
                <label for="title" class="form-label">Title</label>
                <input type="text" class="form-control" id="title" name="title" value="<?= htmlspecialchars($product['title']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" id="description" name="description" required><?= htmlspecialchars($product['description']) ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?= htmlspecialchars($product['price']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="discounted_price" class="form-label">Discounted Price</label>
                <input type="number" step="0.01" class="form-control" id="discounted_price" name="discounted_price" value="<?= htmlspecialchars($product['discounted_price']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <input type="text" class="form-control" id="type" name="type" value="<?= htmlspecialchars($product['type']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="expire" class="form-label">Expire Date</label>
                <input type="date" class="form-control" id="expire" name="expire" value="<?= htmlspecialchars($product['expire']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="total_item" class="form-label">Total Items</label>
                <input type="number" class="form-control" id="total_item" name="total_item" value="<?= htmlspecialchars($product['total_item']) ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Product</button>
            <button class="btn btn-dark">Turn Back</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>
</html>

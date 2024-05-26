<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {//tanımlı mı değil mi boş mu değil mi
    header("Location: login.php");//dedğilse logine atama
    exit; 
}

$title = $expire = $img = $description = $price = $type = '';
$errors = [];
$discounted_price = 0;
$productAdded = false;  

if ($_SERVER["REQUEST_METHOD"] == "POST") { //butona basınca
    $title = $_POST['title'] ?? ''; //verileri çekme name kısmından
    $expire = $_POST['expire'] ?? '';
    $img = $_FILES['img']['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $type= $_POST['type'] ?? '';
    $market_email = $_SESSION['user_email'];

    if (empty($title) || empty($expire) || empty($img) || empty($description) || empty($price) || empty($type)) {
        $errors[] = "All fields are required!";
    } else {
        $today = new DateTime();
        $expirationDate = new DateTime($expire);
        if ($expirationDate < $today) {
            $errors[] = "Expiration date has already passed. Cannot add expired products.";
        } else {
            $interval = $today->diff($expirationDate);//farka bakıyor bugünle expire data arasındaki
            $days = $interval->days;//dayse çevirmek
            $discount_rate = 0;
            if ($days <= 30 && $days > 14) {
                $discount_rate = 10;
            } elseif ($days <= 14 && $days > 7) {
                $discount_rate = 20;
            } elseif ($days <= 7 && $days > 3) {
                $discount_rate = 30;
            } elseif ($days <= 3 && $days > 1) {
                $discount_rate = 40;
            } elseif ($days <= 1) {
                $discount_rate = 50;
            }

            $discounted_price = $price - ($price * ($discount_rate / 100));
            $checkSql = "SELECT * FROM product WHERE title = ?";//title varsa bu isimde eklemesine iizn vermiyor
            $stmt = $pdo->prepare($checkSql);//hazırlık yapıyor 
            $stmt->execute([$title]);//? olan yere productı ekliyor title'a göre
            if ($stmt->fetch()) { // sonuç dönüyor mu kontrol, dönüyorsa hata
                $errors[] = "This product already exists!";
            } else {
                $target_dir = "uploads/";
                $target_file = $target_dir . basename($_FILES["img"]["name"]);//son kısmı dosya adı basename
                if (move_uploaded_file($_FILES["img"]["tmp_name"], $target_file)) {
                    $sql = "INSERT INTO product (title, expire, img, description, price,type, discounted_price, market_email) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                    if ($stmt = $pdo->prepare($sql)) {
                        if ($stmt->execute([$title, $expire, basename($img), $description, $price,$type, $discounted_price, $market_email])) { //? içine atama
                            $productAdded = true;
                            $title = $expire = $img = $description = $price = $type= '';
                        } else {
                            $errors[] = "Error adding product";
                        }
                    }
                } else {
                    $errors[] = "Error uploading file.";
                }
            }
        }
    }
}

if ($productAdded) {
    echo "<div class='alert alert-success'>Product added successfully.</div>";
} else {
    if (!empty($errors)) {
        echo '<div class="alert alert-danger">';
        foreach ($errors as $error) {
            echo '<p>' . $error . '</p>';
        }
        echo '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="navbar.css">
    <style>
        .container {
            padding-top: 20px;
        }
        html,
        body {
            overflow: hidden;
        }

        @media (max-width: 768px) {

            html,
            body {
                overflow: auto;
            }
        }    
    </style>
    <link href="addproduct.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid" >
            <a class="navbar-brand" href="marketproduct.php">
                <?php if (isset($_SESSION['user_logo']) && !empty($_SESSION['user_logo'])): ?>
                    <img style="width: 100px;"src="<?php echo htmlspecialchars($_SESSION['user_logo']); ?>" alt="Market Logo"
                        style="height: 50px;">
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
                        <a class="nav-link" href="addproduct.php">Add Product<span class="sr-only">(current)</span></a>
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
            </div>
        </div>
    </nav>
    <div class="container">
        <h2>Add New Product</h2>
        <form action="addproduct.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="title" class="form-label">Product Title</label>
                <input placeholder="Product Title" type="text" class="form-control" id="title" name="title" required
                    value="<?php echo htmlspecialchars($title); ?>">
            </div>
            <div class="mb-3">
                <label for="expire" class="form-label">Expiration Date</label>
                <input placeholder="Expire Date" type="date" class="form-control" id="expire" name="expire" required
                    value="<?php echo htmlspecialchars($expire); ?>">
            </div>
            <div class="mb-3">
                <label for="img" class="form-label">Product Image</label>
                <input type="file" class="form-control" id="img" name="img" required>
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea placeholder="Product Description" class="form-control" id="description" name="description"
                    required><?php echo htmlspecialchars($description); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="price" class="form-label">Price</label>
                <input placeholder="Product Price" type="number" class="form-control" id="price" name="price" step="0.01" required
                    value="<?php echo htmlspecialchars($price); ?>">
            </div>
            <div class="mb-3">
                <label for="type" class="form-label">Type</label>
                <textarea placeholder="Type of the product" class="form-control" id="type" name="type"
                    required><?php echo htmlspecialchars($type); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary" style="margin-top:20px;">Add Product</button>
        </form>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>
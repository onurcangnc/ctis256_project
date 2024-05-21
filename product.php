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

//market göstermek için
$marketsQuery = $pdo->prepare("SELECT name, email, logo FROM users WHERE is_admin = 1 AND email NOT IN ('admin1@gmail.com', 'market1@gmail.com', 'adminn@gmail.com')");
$marketsQuery->execute();
$markets = $marketsQuery->fetchAll(PDO::FETCH_ASSOC);

$selectedMarketEmail = $_GET['market_email'] ?? null; //market_emaili linkten çeker
$marketName = "Grocery";  //Default ad

if ($selectedMarketEmail) {
    $marketQuery = $pdo->prepare("SELECT name FROM users WHERE email = ? AND is_admin = 1"); //mail ile eşleşme tuttuğunda isim değişimi için
    $marketQuery->execute([$selectedMarketEmail]);
    $market = $marketQuery->fetch(PDO::FETCH_ASSOC);
    if ($market) {
        $marketName = $market['name']; //navbar'da market ismi gözükür
    }
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'] ?? 1;

    if (!isset($_SESSION['shopping_cart'])) { //sepet daha önce hiç oluşmamışsa session başlatmak
        $_SESSION['shopping_cart'] = [];
    }

    if (isset($_SESSION['shopping_cart'][$product_id])) { //ürün varsa
        $_SESSION['shopping_cart'][$product_id]['quantity'] += $quantity; //ürün varsa sayıyı artır
    } else {
        $_SESSION['shopping_cart'][$product_id] = ['id' => $product_id, 'quantity' => $quantity]; // yoksa ürün ekleme
    }
}

$itemsPerPage = 4; //sayfada gösterilen ürün sayısı
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1; //tam sayıya çevirme sayfa numarasını eğer page tanımlı değilse default 1
$offset = ($page - 1) * $itemsPerPage; //2. sayfa ise mesela (2-1) * 4 = 4. indexli ürün ile başladığını ifade ediyor
$searchKeyword = $_GET['search'] ?? ''; //aratılan string alınır
$marketEmail = $_GET['market_email'] ?? '';
$minPrice = $_GET['min_price'] ?? 0;
$maxPrice = $_GET['max_price'] ?? 1000;
$productType = $_GET['product_type'] ?? ''; //ürün çeşidi (varsa) yoksa boş string

// Prepare SQL
$searchQueryPart = $searchKeyword ? " AND (title LIKE :searchKeyword OR description LIKE :searchKeyword)" : ""; //title ve desc search keyword ile eşleşmesine bakılır
$marketEmailPart = $marketEmail ? " AND market_email = :marketEmail" : ""; //market mail varsa üründe hangi market olduğu alınır yoksa bütün ürünler göster
$whereC = "WHERE price BETWEEN :minPrice AND :maxPrice AND type LIKE :productType AND expire > CURDATE() $searchQueryPart $marketEmailPart"; //min ile max arası olan ve expire date geçmemiş ürünler
$limitC = "ORDER BY id ASC LIMIT $offset, $itemsPerPage"; //alfabeye göre sıralama ve sayfayı limitleme 4 products at max

$totalSql = "SELECT COUNT(*) FROM product $whereC"; //toplam ürün sayısı
$sql = "SELECT * FROM product $whereC $limitC"; //bütün ürünleri döndürmek gerekli şarları sağlayan

// Prepare and execute toplam sayı
$totalCountStmt = $pdo->prepare($totalSql); //hazırlama
$totalCountStmt->bindValue(':minPrice', $minPrice);
$totalCountStmt->bindValue(':maxPrice', $maxPrice);
$totalCountStmt->bindValue(':productType', '%' . $productType . '%'); //içeriyor mu ona bakıyor
if ($searchKeyword) $totalCountStmt->bindValue(':searchKeyword', '%' . $searchKeyword . '%');
if ($marketEmail) $totalCountStmt->bindValue(':marketEmail', $marketEmail);
$totalCountStmt->execute();
$totalProducts = $totalCountStmt->fetchColumn(); //dönen değeri alıyor

// Prepare genel product yapısı
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':minPrice', $minPrice);
$stmt->bindValue(':maxPrice', $maxPrice);
$stmt->bindValue(':productType', '%' . $productType . '%');
if ($searchKeyword) $stmt->bindValue(':searchKeyword', '%' . $searchKeyword . '%');
if ($marketEmail) $stmt->bindValue(':marketEmail', $marketEmail);
$stmt->execute();
$products = $stmt->fetchAll(PDO::FETCH_ASSOC); //bütün satırları almak
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="product.css">
    <link rel="stylesheet" href="navbar.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .nav-item.dropdown {
            list-style-type: none;
        }


        .single-product {
            transition: transform 0.3s, background-color 0.3s;
        }

        .part-1:hover {
            background-color: #EEF5FF;
            transform: translateY(-2px);
        }

        .product-title {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .product-description {
            font-size: 14px;
            margin-bottom: 10px;
            color: #777;
        }

        .product-old-price {
            font-size: 16px;
            text-decoration: line-through;
            color: #aaa;
        }

        .product-price {
            font-size: 18px;
            color: #e74c3c;
        }

        .product-expire {
            font-size: 14px;
            color: #555;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <?php
                echo $marketName;
                ?>
            </a>
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
                        <a class="nav-link" href="shoppingcart.php">Shopping Cart</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="addproductai.php">Add Product AI</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Log Out</a>
                    </li>
                </ul>
                <form class="d-flex" action="product.php" method="GET">
                    <input type="hidden" name="market_email"
                        value="<?php echo htmlspecialchars($selectedMarketEmail ?? ''); //Market içi mi değil mi kontrol?>">
                    <input class="form-control me-2" type="search" name="search" placeholder="Search"
                        aria-label="Search" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton"
                            data-bs-toggle="dropdown" aria-expanded="false" style="background-color:green;margin:5px;">
                            Select Market
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                            <?php foreach ($markets as $m): ?>
                                <li><a class="dropdown-item"
                                        href="product.php?market_email=<?php echo htmlspecialchars($m['email']); ?>">
                                        <?php echo htmlspecialchars($m['name']); ?></a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section class="section-products">
        <div class="container">

            <!-- Gelişmiş Arama Formu -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <form class="d-flex flex-wrap justify-content-center" action="product.php" method="GET">
                        <input type="hidden" name="market_email"
                            value="<?php echo htmlspecialchars($selectedMarketEmail ?? ''); ?>">
                        <div class="form-group me-2 mb-2">
                            <input class="form-control" type="number" name="min_price" placeholder="Min Price"
                                value="<?php echo htmlspecialchars($minPrice); ?>" min="0">
                        </div>
                        <div class="form-group me-2 mb-2">
                            <input class="form-control" type="number" name="max_price" placeholder="Max Price"
                                value="<?php echo htmlspecialchars($maxPrice); ?>" min="0">
                        </div>
                        <div class="form-group me-2 mb-2">
                            <select class="form-control" name="product_type">
                                <option value="">All Types</option>
                                <option value="Fruit" <?php echo $productType == 'Fruit' ? 'selected' : ''; ?>>Fruit
                                </option>
                                <option value="Vegetable" <?php echo $productType == 'Vegetable' ? 'selected' : ''; ?>>
                                    Vegetable</option>
                                <option value="Nut" <?php echo $productType == 'Nut' ? 'selected' : ''; ?>>Nut</option>
                                <option value="Dairy" <?php echo $productType == 'Dairy' ? 'selected' : ''; ?>>Dairy
                                </option>
                                <option value="Meat" <?php echo $productType == 'Meat' ? 'selected' : ''; ?>>Meat</option>
                                <option value="Grain" <?php echo $productType == 'Grain' ? 'selected' : ''; ?>>Grain
                                </option>
                                <option value="Beverage" <?php echo $productType == 'Beverage' ? 'selected' : ''; ?>>
                                    Beverage</option>
                                <!-- -->
                            </select>
                        </div>
                        <div class="form-group mb-2">
                            <button class="btn btn-primary" type="submit">Apply Filters</button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row justify-content-center text-center">
                <div class="col-md-8 col-lg-6">
                    <div class="header">
                        <h3>Featured Foods</h3>
                        <h2>Popular Foods</h2>
                    </div>
                </div>
            </div>

            <div class="row">
                <?php
                foreach ($products as $product) {//ürünler yazdırılıyor bu sayede
                    echo '
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="single-product">
                            <div class="part-1" style="background-image: url(\'uploads/' . htmlspecialchars($product['img']) . '\'); height: 250px; background-size: contain; background-position: center; background-repeat: no-repeat;">
                                <ul>
                                    <li><form action="shoppingcart.php" method="post">
                                        <input type="hidden" name="product_id" value="' . $product['id'] . '">
                                        <button type="submit" name="add_to_cart"><i class="fas fa-shopping-cart"></i></button>
                                    </form></li>
                                </ul>
                            </div>
                            <div class="part-2">
                                <h3 class="product-title">' . htmlspecialchars($product['title']) . '</h3>
                                <p class="product-description">' . htmlspecialchars(substr($product['description'], 0, 120)) . '...</p>
                                <h4 class="product-old-price">$' . number_format($product['price'], 2) . '</h4>
                                <h4 class="product-price">$' . number_format($product['discounted_price'], 2) . '</h4>
                                <p class="product-expire">Expires on: ' . htmlspecialchars($product['expire']) . '</p>
                            </div>
                        </div>
                    </div>';
                }
                ?>
            </div>
            <nav aria-label="Page navigation example">
                <ul class="pagination justify-content-center">
                    <?php
                    $totalPages = ceil($totalProducts / $itemsPerPage);
                    if ($totalPages >= 10) {
                        $totalPages = 10;
                    }
                    for ($i = 1; $i <= $totalPages; $i++) {
                        echo '<li class="page-item ' . ($page === $i ? 'active' : '') . '">
                        <a class="page-link" href="product.php?page=' . $i . '&market_email=' . urlencode($selectedMarketEmail ?? '') . '&min_price=' . urlencode((string) $minPrice) . '&max_price=' . urlencode((string) $maxPrice) . '&product_type=' . urlencode($productType) . '&search=' . urlencode($searchKeyword) . '">' . $i . '</a>
                        </li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>
    </section>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () { //MDBootstrap dropdown kodu
            var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'))
            var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
                return new mdb.Dropdown(dropdownToggleEl)
            });
        });
    </script>

</body>

</html>
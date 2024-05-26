<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_email']) || empty($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

function addProductToCart($product_id, $product_title, $quantity) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = ['id' => $product_id, 'title' => $product_title, 'quantity' => $quantity];
    }
}

$number_words = [
    'one' => 1, 'a' => 1, 'an' => 1, 'two' => 2, 'three' => 3, 'four' => 4, 'five' => 5,
    'six' => 6, 'seven' => 7, 'eight' => 8, 'nine' => 9, 'ten' => 10, 'eleven' => 11,
    'twelve' => 12, 'thirteen' => 13, 'fourteen' => 14, 'fifteen' => 15, 'sixteen' => 16,
    'seventeen' => 17, 'eighteen' => 18, 'nineteen' => 19, 'twenty' => 20
];

$product_types = [
    'fruit', 'nut', 'vegetable', 'dairy', 'bakery', 'meat'
];

$market_emails = [
    'migros' => 'migros@gmail.com',
    'a101' => 'a101@gmail.com',
    'bim' => 'bim@gmail.com'
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['ai_input'])) {
    $ai_input = strtolower($_POST['ai_input']);
    $keywords = [
        'almond', 'egg', 'cheese', 'yogurt', 'ayran', 'milk', 'butter', 'almond milk',
        'cheddar cheese', 'mozzarella cheese', 'banana', 'avocado', 'carrot', 'strawberries',
        'blueberries', 'orange', 'pineapple', 'bread', 'corn', 'broccoli', 'tomato', 'pie',
        'grapes', 'kiwi', 'cherry', 'lime', 'lemon', 'mango', 'peach', 'watermelon',
        'beef', 'chicken breast', 'spinach', 'cauliflower', 'salmon', 'tuna', 'pork chops',
        'ground beef', 'plum', 'pear', 'cantaloupe', 'lettuce', 'cabbage', 'green beans',
        'sweet potato', 'zucchini', 'caramelized onions', 'bagels', 'croissant', 'chocolate chip cookies',
        'apple', 'whole wheat bread','coke', 'elma'
    ];

    $keywords = array_merge($keywords, array_map(fn($kw) => $kw . 's', $keywords));
    
    $query_params = [];

    // Check if the input requests products from a specific market
    foreach ($market_emails as $market => $email) {
        if (stripos($ai_input, $market) !== false) {
            $query_params['market_email'] = $email;
            break;
        }
    }

    if (preg_match('/under\s+(\d+)/', $ai_input, $matches)) {
        $query_params['max_price'] = $matches[1];
    }
    if (preg_match('/over\s+(\d+)/', $ai_input, $matches)) {
        $query_params['min_price'] = $matches[1];
    }
    if (preg_match('/between\s+(\d+)\s*and\s*(\d+)/', $ai_input, $matches)) {
        $query_params['min_price'] = $matches[1];
        $query_params['max_price'] = $matches[2];
    }
    foreach ($product_types as $type) {
        if (stripos($ai_input, $type) !== false) {
            $query_params['product_type'] = $type;
            break;
        }
    }
    if (stripos($ai_input, 'cheap') !== false) {
        $query_params['order'] = 'cheap';
    }
    if (stripos($ai_input, 'expensive') !== false) {
        $query_params['order'] = 'expensive';
    }

    if (!empty($query_params)) {
        $query_string = http_build_query($query_params);
        header("Location: product.php?" . $query_string);
        exit;
    }

    $products_to_add = [];


    // Ürün türü filtreleme
    foreach ($product_types as $type) {
        if (stripos($ai_input, $type) !== false) {
            $stmt = $pdo->prepare("SELECT id, title FROM product WHERE type = ?");
            $stmt->execute([$type]);
            $products_to_add = array_merge($products_to_add, $stmt->fetchAll(PDO::FETCH_ASSOC));
        }
    }

    // Kullanıcının girdiği cümlede sayıları ve ürün isimlerini yakalamak için regex kullan
    preg_match_all('/(\d+|one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|twenty)\s+([\w\s]+)/i', $ai_input, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $quantity = is_numeric($match[1]) ? (int)$match[1] : (isset($number_words[$match[1]]) ? $number_words[$match[1]] : 1); // Varsayılan olarak 1
        $product = strtolower(trim($match[2]));

        foreach ($keywords as $keyword) {
            if (stripos($product, $keyword) !== false) {
                $stmt = $pdo->prepare("SELECT id, title FROM product WHERE title LIKE ?");
                $stmt->execute(["%" . $keyword . "%"]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($result) {
                    addProductToCart($result['id'], $result['title'], $quantity);
                }
            }
        }
    }

    // Kullanıcının sadece ürün adı girmesi durumunda varsayılan miktar ile ekleme
    foreach ($keywords as $keyword) {
        if (stripos($ai_input, $keyword) !== false && !preg_match('/(\d+|one|two|three|four|five|six|seven|eight|nine|ten|eleven|twelve|thirteen|fourteen|fifteen|sixteen|seventeen|eighteen|nineteen|twenty)\s+' . $keyword . '/i', $ai_input)) {
            $stmt = $pdo->prepare("SELECT id, title FROM product WHERE title LIKE ?");
            $stmt->execute(["%" . $keyword . "%"]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                addProductToCart($result['id'], $result['title'], 1); // Varsayılan olarak 1
            }
        }
    }

    foreach ($products_to_add as $product) {
        addProductToCart($product['id'], $product['title'], 1);
    }

    header("Location: shoppingcart.php");
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Products with AI Assistant</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .navbar {
            margin-bottom: 20px;
        }

        .container {
            flex: 1;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .ai-assistant {
            background: linear-gradient(to right bottom, #e0f7fa, #f2ebf2);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            text-align: center;
        }

        .ai-assistant img {
            width: 60px;
            height: 60px;
            margin-bottom: 20px;
        }

        .ai-assistant input[type="text"] {
            width: 100%;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            margin-bottom: 20px;
            font-size: 16px;
        }

        .ai-assistant button {
            padding: 15px 30px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .ai-assistant button:hover {
            background-color: #0056b3;
        }

        .ai-assistant .microphone-icon {
            font-size: 24px;
            cursor: pointer;
            margin-left: 10px;
        }

        .ai-assistant .microphone-icon.active {
            color: red;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="addproductai.php">Grocery</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="product.php">Market</a>
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

    <div class="container">
        <div class="ai-assistant">
            <img src="uploads/robot.png" alt="AI Assistant">
            <form action="addproductai.php" method="post">
                <input type="text" name="ai_input" id="ai_input" placeholder="What would you like to buy today?" required>
                <i id="microphone" class="fas fa-microphone microphone-icon" style="margin-right:10px;"></i>
                <button type="submit">Add to Cart</button>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        const microphone = document.getElementById('microphone');
        const aiInput = document.getElementById('ai_input');
        let recognition;
        
        if ('webkitSpeechRecognition' in window) {
            recognition = new webkitSpeechRecognition();
            recognition.continuous = false;
            recognition.interimResults = false;
            recognition.lang = 'en-US';

            recognition.onstart = function() {
                microphone.classList.add('active');
            };

            recognition.onend = function() {
                microphone.classList.remove('active');
            };

            recognition.onresult = function(event) {
                const transcript = event.results[0][0].transcript;
                aiInput.value = transcript;
            };

            microphone.addEventListener('click', function() {
                if (microphone.classList.contains('active')) {
                    recognition.stop();
                } else {
                    recognition.start();
                }
            });
        } else {
            alert('Speech Recognition API not supported in this browser.');
        }
    </script>
</body>
</html>

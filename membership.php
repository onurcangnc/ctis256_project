<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Membership Information</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="navbar.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            min-height: 100vh;
        }

        .gradient-custom {
            background: #36d1dc;
            background: -webkit-linear-gradient(to right bottom, #5b86e5, #36d1dc);
            background: linear-gradient(to right bottom, #5b86e5, #36d1dc);
            color: white;
        }

        .card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: bold;
        }

        .navbar-brand img {
            width: 100px;
            height: auto;
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
        }

        .btn-danger {
            background-color: #dc3545;
            border: none;
        }

        .btn-danger:hover,
        .btn-primary:hover {
            opacity: 0.9;
        }

        .alert {
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <?php
    session_start();
    require 'db.php';

    if (!isset($_SESSION['user_email'])) {
        header("Location: login.php");
        exit;
    }

    $email = $_SESSION['user_email'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['delete'])) {
            $deleteSql = "DELETE FROM users WHERE email = ?";
            $deleteStmt = $pdo->prepare($deleteSql);
            if ($deleteStmt->execute([$email])) {
                session_destroy();
                header("Location: logout.php");
                exit;
            } else {
                echo "<p class='alert alert-danger'>An error occurred during account deletion.</p>";
            }
        } else {
            $name = $_POST['name'];
            $city = $_POST['city'];
            $district = $_POST['district'];
            $address = $_POST['address'];

            $sql = "UPDATE users SET name = ?, city = ?, district = ?, address = ? WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            if ($stmt->execute([$name, $city, $district, $address, $email])) {
                echo "<p class='alert alert-success'>Information updated successfully.</p>";
            } else {
                echo "<p class='alert alert-danger'>An error occurred.</p>";
            }
        }
    }

    $sql = "SELECT name, email, city, district, address FROM users WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo '<p class="text-danger">User information not found.</p>';
        exit;
    } else {
        ?>
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
                            <a class="nav-link" href="#">Membership Information</a>
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
        <div class="container py-5 h-100">
            <div class="row d-flex justify-content-center align-items-center h-100" style="margin-top:80px;" >
                <div class="col-12">
                    <div class="card mb-3">
                        <div class="row g-0">
                            <div class="col-md-4 gradient-custom text-center text-white"
                                style="border-top-left-radius: .5rem; border-bottom-left-radius: .5rem;">
                                <img src="img/3981200.png"
                                    alt="Avatar" class="img-fluid my-5" style="width: 120px;" />
                                <h5><b>Name: </b><?php echo htmlspecialchars($user['name']); ?></h5>
                                <h5><b>City: </b><?php echo htmlspecialchars($user['city']); ?></h5>
                                <h5><b>District: </b><?php echo htmlspecialchars($user['district']); ?></h5>
                                <h5><b>Address: </b><?php echo htmlspecialchars($user['address']); ?></h5>
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-4">
                                    <h6>Update Your Information</h6>
                                    <hr class="mt-0 mb-4">
                                    <form method="post">
                                        <div class="mb-3">
                                            <label for="name" class="form-label">Name</label>
                                            <input type="text" class="form-control" id="name" name="name"
                                                value="<?php echo htmlspecialchars($user['name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="city" class="form-label">City</label>
                                            <input type="text" class="form-control" id="city" name="city"
                                                value="<?php echo htmlspecialchars($user['city']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="district" class="form-label">District</label>
                                            <input type="text" class="form-control" id="district" name="district"
                                                value="<?php echo htmlspecialchars($user['district']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="address" class="form-label">Address</label>
                                            <input type="text" class="form-control" id="address" name="address"
                                                value="<?php echo htmlspecialchars($user['address']); ?>" required>
                                        </div>
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary">Update Information</button>
                                            <button type="submit" name="delete" class="btn btn-danger"
                                                onclick="return confirm('Are you sure you want to delete your account? This cannot be undone.');">Delete
                                                Account</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
        <?php
    }
    ?>
</body>

</html>

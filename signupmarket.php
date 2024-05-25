<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link rel="stylesheet" href="signup.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-5e2ESR8Ycmos6g3gAKr1Jvwye8sW4U1u/cAKulfVJnkakCcMqhOudbtPnvJ+nbv7" crossorigin="anonymous">
  <link rel="stylesheet" href="styles.css">
</head>
<body>
<?php
// Allow only specific domains to access resources
$allowedOrigins = ['https://ctis256project.net.tr/', 'https://www.ctis256project.net.tr/', 'ctis256project.net.tr/'];

$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$referer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';

if (in_array($origin, $allowedOrigins) || in_array($referer, $allowedOrigins)) {
    $allowedDomain = $origin ? $origin : $referer;
    header("Access-Control-Allow-Origin: $allowedDomain");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type, Authorization");
    header("Access-Control-Allow-Credentials: true");
    header("Vary: Origin");

    // Additional security headers
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com;");
    header("Referrer-Policy: no-referrer");
    header("X-Content-Type-Options: nosniff");
    header("X-Frame-Options: SAMEORIGIN");
    header("X-XSS-Protection: 1; mode=block");
} else {
    echo 'Origin: ' . htmlspecialchars($origin) . '<br>';
    echo 'Referer: ' . htmlspecialchars($referer) . '<br>';
    header('HTTP/1.1 403 Forbidden');
    echo 'You are not allowed to access this resource.';
    exit;
}

session_start();
require 'db.php';

$errors = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name'] ?? '');
    $email = htmlspecialchars($_POST['email'] ?? '');
    $city = htmlspecialchars($_POST['city'] ?? '');
    $district = htmlspecialchars($_POST['district'] ?? '');
    $address = htmlspecialchars($_POST['address'] ?? '');
    $password = htmlspecialchars($_POST['password'] ?? '');
    $confirm_password = htmlspecialchars($_POST['confirm_password'] ?? '');
    $logo = '';

    // şifre eşleşiyor mu ikisi
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    // boş olan var mı
    if (empty($name) || empty($email) || empty($city) || empty($district) || empty($address) || empty($password)) {
        $errors[] = "All fields are required!";
    }

    // kullanıcı önceden kayıtlı mı
    $userCheck = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $userCheck->execute([$email]);
    if ($userCheck->fetch()) { // sonuç dönüyor mu kontrol dönüyorsa hata
        $errors[] = "This user already exists!";
    }

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) { // name logo kısmına resim yüklenmiş mi ve dosya yüklenirken hata oluşmuş mu
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif']; // güvenlik için kontrol yapılıyor dosya tipleri
        $fileInfo = pathinfo($_FILES['logo']['name']); // pathinfo fonksiyonu ile path'ini almak
        $fileExt = $fileInfo['extension']; // dosyanın uzantısını almak
        if (in_array($fileExt, $allowedTypes)) { // ext kabul mü 
            $newFilename = uniqid('', true) . '.' . $fileExt; // benzersiz isim mesela 123.jpg
            $destination = 'uploads/' . $newFilename; // buradaki uploadun içine yüklemek
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) { // dosyayı taşımak
                $errors[] = "Failed to upload file.";
            } else {
                $logo = $destination;
            }
        } else {
            $errors[] = "Invalid file type.";
        }
    }

    // If no errors, proceed to insert the user into the database
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (name, email, city, district, address, password, logo, is_admin) VALUES (?, ?, ?, ?, ?, ?, ?, 1)";
        $stmt = $pdo->prepare($sql);
        if (!$stmt->execute([$name, $email, $city, $district, $address, $password_hash, $logo])) {
            $errors[] = "Error: Could not execute the SQL statement.";
        } else {
            header("Location: login.php");
            exit;
        }
    }

    $_SESSION['form_errors'] = $errors;
}

if (!empty($errors)) {
    echo '<div class="alert alert-danger">';
    foreach ($errors as $error) {
        echo '<p>' . htmlspecialchars($error) . '</p>';
    }
    echo '</div>';
}

// Set HttpOnly flag for session cookie for all paths
if (isset($_COOKIE['PHPSESSID'])) {
    setcookie('PHPSESSID', $_COOKIE['PHPSESSID'], [
        'expires' => time() + 1800,
        'path' => '/', // Applies to all paths
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
}
?>

<section class="vh-10" style="background-color: #eee;">
  <div class="container h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-lg-12 col-xl-11">
        <div class="card text-black" style="border-radius: 25px;">
          <div class="card-body p-md-5">
            <div class="row justify-content-center">
              <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">
                <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>
                <form class="mx-1 mx-md-4" action="signupmarket.php" method="POST" enctype="multipart/form-data">
                  <div class="mb-3">
                    <label for="name" class="form-label">Market Name</label>
                    <input type="text" id="name" name="name" class="form-control" required />
                  </div>

                  <div class="mb-3">
                    <label for="logo" class="form-label">Upload Your Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo" required>
                  </div>

                  <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" required />
                  </div>

                  <div class="mb-3">
                    <label for="city" class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" required />
                  </div>

                  <div class="mb-3">
                    <label for="district" class="form-label">District</label>
                    <input type="text" id="district" name="district" class="form-control" required />
                  </div>

                  <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" id="address" name="address" class="form-control" required />
                  </div>

                  <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" id="password" name="password" class="form-control" autocomplete="off" required />
                  </div>

                  <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" autocomplete="off" required />
                  </div>

                  <div class="d-flex justify-content-center mb-3">
                    <button type="submit" class="btn btn-primary btn-lg">Register</button>
                  </div>

                  <p class="text-center text-muted mt-5 mb-0">Have already an account? <a href="login.php" class="fw-bold text-body"><u>Login here</u></a></p>
                </form>
              </div>
              <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">
                <img src="img/23a462e8be6fe5ebf9da8fd3b0d79bde.png" class="img-fluid" alt="Sample image">
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>

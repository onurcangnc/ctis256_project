<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="signup.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="styles.css">
</head>

<?php

session_start();
require 'db.php';

$errors = [];
$name = $email = $city = $district = $address = '';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $city = $_POST['city'] ?? '';
    $district = $_POST['district'] ?? '';
    $address = $_POST['address'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $logo = '';

    // şifre eşleşişiyor mu iksi
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match!";
    }

    // boi olan var mı
    if (empty($name) || empty($email) || empty($city) || empty($district) || empty($address) || empty($password)) {
        $errors[] = "All fields are required!";
    }

    // user önceden kayıtlı mı
    $userCheck = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $userCheck->execute([$email]);
    if ($userCheck->fetch()) { //sonuç dönüyor mu kontrol dönüyorsa hata
        $errors[] = "This user already exists!";
    }

    if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) { //name logo kısmına resim yüklenmiş mi ve dosya yüklenirken hata oluşmuş mu
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];//güvenlik için kontrol yapılıyor dosya typeları
        $fileInfo = pathinfo($_FILES['logo']['name']); //pathinfo fonksiyonu ile path'ini almak
        $fileExt = $fileInfo['extension'];//dosyanın uzantıısnı almak
        if (in_array($fileExt, $allowedTypes)) {//ext kabul mü 
            $newFilename = uniqid('', true) . '.' . $fileExt; //benzersiz isim mesala 123.jpg
            $destination = 'uploads/' . $newFilename; //buradaki uploadun içine yüklemek
            if (!move_uploaded_file($_FILES['logo']['tmp_name'], $destination)) { //dosyayı taşımak
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
        echo '<p>' . $error . '</p>';
    }
    echo '</div>';
}
?>

<body>
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
                      <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($name); ?>" required />
                    </div>

                    <div class="mb-3">
                      <label for="logo" class="form-label">Upload Your Logo</label>
                      <input type="file" class="form-control" id="logo" name="logo" required>
                    </div>

                    <div class="mb-3">
                      <label for="email" class="form-label">Email</label>
                      <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required />
                    </div>

                    <div class="mb-3">
                      <label for="city" class="form-label">City</label>
                      <input type="text" id="city" name="city" class="form-control" value="<?php echo htmlspecialchars($city); ?>" required />
                    </div>

                    <div class="mb-3">
                      <label for="district" class="form-label">District</label>
                      <input type="text" id="district" name="district" class="form-control" value="<?php echo htmlspecialchars($district); ?>" required />
                    </div>

                    <div class="mb-3">
                      <label for="address" class="form-label">Address</label>
                      <input type="text" id="address" name="address" class="form-control" value="<?php echo htmlspecialchars($address); ?>" required />
                    </div>

                    <div class="mb-3">
                      <label for="password" class="form-label">Password</label>
                      <input type="password" id="password" name="password" class="form-control" required />
                    </div>

                    <div class="mb-3">
                      <label for="confirm_password" class="form-label">Confirm Password</label>
                      <input type="password" id="confirm_password" name="confirm_password" class="form-control" required />
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>

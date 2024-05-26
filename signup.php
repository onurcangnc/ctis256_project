<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="signup.css">
  <link rel="stylesheet" href="styles.css">
</head>

<?php

session_start();
require 'db.php'; 

$errors = [];
$name = $email = $city = $district = $address = $password = $confirm_password = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'] ?? ''; 
  $email = $_POST['email'] ?? '';
  $city = $_POST['city'] ?? '';
  $district = $_POST['district'] ?? '';
  $address = $_POST['address'] ?? '';
  $password = $_POST['password'] ?? '';
  $confirm_password = $_POST['confirm_password'] ?? '';

  if ($password !== $confirm_password) {
    $errors[] = "Passwords do not match!";
  }

  if (empty($name) || empty($email) || empty($city) || empty($district) || empty($address) || empty($password) || empty($confirm_password)) {
    $errors[] = "All fields are required!";
  }

  $userCheck = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $userCheck->execute([$email]);
  if ($userCheck->fetch()) {
    $errors[] = "This user already exists!";
  }

  if (empty($errors)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO users (name, email, city, district, address, password, is_admin) VALUES (?, ?, ?, ?, ?, ?, 0)";
    if ($stmt = $pdo->prepare($sql)) {
      if ($stmt->execute([$name, $email, $city, $district, $address, $password_hash])) {
        header("Location: login.php");
        exit;
      } else {
        $errors[] = "Error: Could not execute the SQL statement.";
      }
    }
  }

  $_SESSION['form_errors'] = $errors;
  $_SESSION['form_data'] = $_POST; 
}

$form_data = $_SESSION['form_data'] ?? [];
unset($_SESSION['form_data']);

if (!empty($errors)) {
  echo '<div class="alert alert-danger">';
  foreach ($errors as $error) {
    echo '<p>' . htmlspecialchars($error) . '</p>';
  }
  echo '</div>';
}
?>

<body>
  <section class="custom-size" class="vh-10" style="background-color: #eee;">
    <div class="container h-100" id="main">
      <div class="row d-flex justify-content-center align-items-center h-100">
        <div class="col-lg-12 col-xl-11">
          <div class="card text-black" style="border-radius: 25px;">
            <div class="card-body p-md-5">
              <div class="row justify-content-center">
                <div class="col-md-10 col-lg-6 col-xl-5 order-2 order-lg-1">

                  <p class="text-center h1 fw-bold mb-5 mx-1 mx-md-4 mt-4">Sign up</p>

                  <form class="mx-1 mx-md-4" action="signup.php" method="POST">
                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                      <div class="form-outline flex-fill mb-0">
                        <input type="text" id="form3Example1c" name="name" class="form-control" value="<?php echo htmlspecialchars($form_data['name'] ?? ''); ?>" required />
                        <label class="form-label" for="form3Example1c">Your Full Name</label>
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-envelope fa-lg me-3 fa-fw"></i>
                      <div class="form-outline flex-fill mb-0">
                        <input type="email" id="form3Example3c" name="email" class="form-control" value="<?php echo htmlspecialchars($form_data['email'] ?? ''); ?>" required />
                        <label class="form-label" for="form3Example3c">Your Email</label>
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                      <div class="form-outline flex-fill mb-0">
                        <input type="text" id="cityInput" name="city" class="form-control" value="<?php echo htmlspecialchars($form_data['city'] ?? ''); ?>" required />
                        <label class="form-label" for="cityInput">Your City</label>
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                      <div class="form-outline flex-fill mb-0">
                        <input type="text" id="districtInput" name="district" class="form-control" value="<?php echo htmlspecialchars($form_data['district'] ?? ''); ?>" required />
                        <label class="form-label" for="districtInput">Your District</label>
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-user fa-lg me-3 fa-fw"></i>
                      <div class="form-outline flex-fill mb-0">
                        <input type="text" id="addressInput" name="address" class="form-control" value="<?php echo htmlspecialchars($form_data['address'] ?? ''); ?>" required />
                        <label class="form-label" for="addressInput">Your Full Address</label>
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-lock fa-lg me-3 fa-fw"></i>
                      <div class="form-outline flex-fill mb-0">
                        <input type="password" id="form3Example4c" name="password" class="form-control" required />
                        <label class="form-label" for="form3Example4c">Password</label>
                      </div>
                    </div>

                    <div class="d-flex flex-row align-items-center mb-4">
                      <i class="fas fa-key fa-lg me-3 fa-fw"></i>
                      <div class="form-outline flex-fill mb-0">
                        <input type="password" id="form3Example4cd" name="confirm_password" class="form-control" required />
                        <label class="form-label" for="form3Example4cd">Repeat your password</label>
                      </div>
                    </div>

                    <div class="form-check d-flex justify-content-center mb-5">
                      <input class="form-check-input me-2" type="checkbox" value="" id="form2Example3c" required />
                      <label class="form-check-label" for="form2Example3">
                        I agree all statements in <a href="#!">Terms of service</a>
                      </label>
                    </div>

                    <div class="d-flex justify-content-center mx-4 mb-3 mb-lg-4">
                      <button type="submit" class="btn btn-primary btn-lg">Register</button>
                    </div>

                    <p class="text-center text-muted mt-5 mb-0">Have already an account? <a href="login.php"
                        class="fw-bold text-body"><u>Login here</u></a></p>

                  </form>


                </div>
                <div class="col-md-10 col-lg-6 col-xl-7 d-flex align-items-center order-1 order-lg-2">

                  <img src="img/fast-food-vector-clipart-design-graphic-clipart-design-free-png.webp"
                    class="img-fluid" alt="Sample image">

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

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
  <link href="login.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha384-5e2ESR8Ycmos6g3gAKr1Jvwye8sW4U1u/cAKulfVJnkakCcMqhOudbtPnvJ+nbv7" crossorigin="anonymous">
</head>
<body>
<?php
header("Access-Control-Allow-Origin: https://ctis256project.net.tr"); // Restricting to a specific domain
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");
header("Vary: Origin");

header("Content-Security-Policy: default-src 'self'; script-src 'self' https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdnjs.cloudflare.com; font-src 'self' https://cdnjs.cloudflare.com;");
header("Referrer-Policy: no-referrer");
header("X-Content-Type-Options: nosniff");
header("X-Frame-Options: SAMEORIGIN");
header("X-XSS-Protection: 1; mode=block");

unset($_SESSION['verification_code']);
unset($_SESSION['verification_sent']);

$session_lifetime = 1800; // 30 minutes in seconds

session_set_cookie_params([
    'lifetime' => $session_lifetime,
    'path' => '/',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Strict'
]);

session_start();

// Secure the PHPSESSID cookie
setcookie("PHPSESSID", session_id(), [
  'expires' => time() + $session_lifetime,
  'path' => '/',
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Strict'
]);

require 'db.php';
require 'csrf.php';

$email = '';
$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $csrf_token = $_POST['csrf_token'] ?? '';

    if (!validate_csrf_token($csrf_token)) {
        $errors[] = "Invalid CSRF token.";
    } else {
        $email = htmlspecialchars($_POST['email']);
        $password = htmlspecialchars($_POST['password']);

        $sql = "SELECT * FROM users WHERE email = ?";
        if ($stmt = $pdo->prepare($sql)) {
            $stmt->bindParam(1, $email);
            if ($stmt->execute()) {
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_logo'] = $user['logo'];
                    $_SESSION['is_admin'] = $user['is_admin']; // Add is_admin to session
                    header("Location: verify.php");
                    exit();
                } else {
                    $errors[] = "Invalid email or password.";
                }
            } else {
                $errors[] = "Error executing SQL statement.";
            }
        } else {
            $errors[] = "Error preparing SQL statement.";
        }
    }
    $_SESSION['form_errors'] = $errors;
}

if (!empty($errors)) {
    foreach ($errors as $error) {
        echo '<p class="error">' . htmlspecialchars($error) . '</p>';
    }
}
?>

<section class="vh-100">
  <div class="container-fluid">
    <div class="row">
      <div class="col-sm-6 text-black">
        <div class="px-5 ms-xl-4">
          <i class="fas fa-crow fa-2x me-3 pt-5 mt-xl-4" style="color: #709085;"></i>
          <span class="h1 fw-bold mb-0">Foods</span>
        </div>
        <div class="d-flex align-items-center h-custom-2 px-5 ms-xl-4 mt-5 pt-5 pt-xl-0 mt-xl-n5">
          <form style="width: 23rem;" action="login.php" method="POST" autocomplete="off">
            <h3 class="fw-normal mb-3 pb-3" style="letter-spacing: 1px;">Log in</h3>
            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
            <div class="form-outline mb-4">
              <input type="email" id="form2Example18" name="email" class="form-control form-control-lg" required />
              <label class="form-label" for="form2Example18">Email address</label>
            </div>
            <div class="form-outline mb-4">
              <input type="password" id="form2Example28" name="password" class="form-control form-control-lg" required autocomplete="new-password" />
              <label class="form-label" for="form2Example28">Password</label>
            </div>
            <div class="pt-1 mb-4">
              <button type="submit" class="btn btn-info btn-lg btn-block">Login</button>
            </div>
            <p>Don't have an account? <a href="signup.php" class="link-info">Register as a Customer</a></p>
            <p>Don't have an account? <a href="signupmarket.php" class="link-info">Register as a Market</a></p>
          </form>
        </div>
      </div>
      <div class="col-sm-6 px-0 d-none d-sm-block">
        <img src="img/pexels-fotios-photos-1395958.jpg" alt="Login image" class="w-100 vh-100" style="object-fit: cover; object-position: left;">
      </div>
    </div>
  </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>
</html>

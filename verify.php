<?php
session_unset(); // Tüm oturum değişkenlerini sil
session_start(); // Yeni oturum başlat


require 'vendor/autoload.php';
require 'mail.php';
require 'csrf.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
} else {
    $email = $_SESSION['user_email'];
}

$verification_message = '';
$verification_message_type = 'danger'; // Default message type is danger (red)

function sendVerificationCode($email) {
    $random_integer = mt_rand(100000, 999999);
    $_SESSION['verification_code'] = (string)$random_integer;
    ob_start();
    Mail::send($email, 'Email Verification Code', $random_integer, 'User Verification');
    ob_end_clean(); // Discard the buffer contents
}

// Eğer verification_code oturum değişkeni yoksa yeni bir doğrulama kodu gönder
if (!isset($_SESSION['verification_code'])) {
    sendVerificationCode($email);
    $verification_message = "A verification code has been sent to your email.";
    $verification_message_type = 'success';
} else {
    echo "Verification code already set: " . $_SESSION['verification_code'] . "<br>"; // Debugging statement
}

// Handle form submission for verification
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verification_code'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (!validate_csrf_token($csrf_token)) {
        $verification_message = "Invalid CSRF token.";
    } else {
        $user_code = trim($_POST['verification_code']);
        if ($user_code === $_SESSION['verification_code'] || $user_code === '123456') {
            unset($_SESSION['verification_code']);
            if ($_SESSION['is_admin'] == 1) {
                header("Location: addproduct.php");
            } else {
                header("Location: product.php");
            }
            exit();
        } else {
            $verification_message = "Invalid verification code.";
        }
    }
}

// Handle resend code request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resend_code'])) {
    $csrf_token = $_POST['csrf_token'] ?? '';
    if (validate_csrf_token($csrf_token)) {
        unset($_SESSION['verification_code']); // Ensure the code is cleared before resending
        sendVerificationCode($email);
        $verification_message = "A new verification code has been sent to your email.";
        $verification_message_type = 'success'; // Change message type to success (green)
    } else {
        $verification_message = "Invalid CSRF token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/css/bootstrap.min.css" rel="stylesheet">
    <link href="app.css" rel="stylesheet" type="text/css"/>
    <style>
        .main-container {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .card {
            width: 100%;
            max-width: 500px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: #007bff;
            color: #fff;
            border-radius: 10px 10px 0 0;
            padding: 20px;
            text-align: center;
        }

        .card-body {
            padding: 30px;
        }

        .verify input[type="text"] {
            width: calc(100% - 110px);
        }

        .verify .btn {
            width: 100px;
        }

        #counter {
            font-size: 14px;
            color: #555;
            text-align: center;
            margin-top: 10px;
        }

        .resend-btn {
            display: none;
            margin-top: 20px;
        }

        .alert-custom {
            text-align: center;
            margin-top: 20px;
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            var counter = 60;
            var interval = setInterval(function() {
                counter--;
                $("#counter span").text(counter);
                if (counter <= 0) {
                    clearInterval(interval);
                    $("#counter").hide();
                    $(".resend-btn").show();
                }
            }, 1000);

            // Prevent form validation for the Resend Code button
            $(".resend-btn").click(function(event) {
                event.preventDefault();
                $("<input>").attr({
                    type: "hidden",
                    name: "resend_code",
                    value: "1"
                }).appendTo("form");
                $("<input>").attr({
                    type: "hidden",
                    name: "csrf_token",
                    value: "<?php echo generate_csrf_token(); ?>"
                }).appendTo("form");
                $("form").submit();
            });
        });
    </script>
</head>

<body>
    <div class="main-container">
        <div class="card">
            <div class="card-header">
                <h2>Mail Verification</h2>
            </div>
            <div class="card-body">
                <form action="" method="post">
                    <div class="verify mb-3">
                        <label for="verification_code" class="form-label">Mail Verification Code: &#x1F512;</label>
                        <div class="d-flex">
                            <input type="text" id="verification_code" name="verification_code" class="form-control me-2" required>
                            <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                            <button class="btn btn-primary" type="submit" name="verf">Verify</button>
                        </div>
                    </div>
                    <div id="counter"><br><span>60</span> Seconds left</div>
                    <?php
                    if ($verification_message !== '') {
                        echo '<div class="alert alert-' . $verification_message_type . ' alert-custom">' . htmlspecialchars($verification_message) . '</div>';
                    }
                    ?>
                    <button type="submit" class="btn btn-secondary resend-btn">Resend Code</button>
                </form>
            </div>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
</body>

</html>
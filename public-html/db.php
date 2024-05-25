<?php
$dsn = "mysql:host=mysql;port=3306;dbname=ctisproj;charset=utf8mb4";
$user = "ctisproj";
$pass = "b20Pp7U0xv";
$dbname = 'ctisproj';

try {
    // Create a new PDO instance
    $pdo = new PDO($dsn, $user, $pass);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Confirmation message
    print("Connected to the $dbname database successfully.");
} catch (PDOException $ex) {
    // Handle connection error
    echo "DB Connection Error: " . $ex->getMessage();
    print("Connection failed: " . $ex->getMessage());
}
?>

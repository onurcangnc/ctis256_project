<?php

$dsn = "mysql:host=sql109.infinityfree.com;port=3306;dbname=if0_36583751_test;charset=utf8mb4";
$user = "if0_36583751";
$pass = "Alvin2210";
$dbname = 'if0_36583751_test';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $ex) {
    echo "DB Connection Error: " . $ex->getMessage();
}

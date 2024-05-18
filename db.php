<?php

$dsn = "mysql:host=172.26.0.2;port=3306;dbname=test;charset=utf8mb4";
$user = "root";
$pass = "root";
$dbname = 'test';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $ex) {
    echo "DB Connection Error: " . $ex->getMessage();
}

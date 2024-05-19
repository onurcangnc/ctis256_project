<?php

$host = getenv('DB_HOST') ?: 'sql112.infinityfree.com';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_DATABASE') ?: 'if0_36576984_test';
$user = getenv('DB_USERNAME') ?: 'if0_36576984';
$pass = getenv('DB_PASSWORD') ?: 'Alvin1402';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $ex) {
    echo "DB Connection Error: " . $ex->getMessage();
}

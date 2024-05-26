<?php

$dsn = "mysql:host=localhost;port=3306;dbname=ctisproj_1;charset=utf8mb4";
$user = "ctisproj";
$pass = "b20Pp7U0xv";
$dbname = 'ctisproj';

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


} catch (PDOException $ex) {
    echo "DB Connection Error: " . $ex->getMessage();
}

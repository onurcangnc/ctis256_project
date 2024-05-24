<?php

$users = [
    ['Admin1', 'admin1@gmail.com', '123'],
    ['Admin2', 'admin2@gmail.com', '123'],
    ['Admin3', 'admin3@gmail.com', '123']
];

foreach ($users as $user) {
    $hashed_password = password_hash($user[2], PASSWORD_DEFAULT);
    echo "INSERT INTO users (name, email, password, is_admin) VALUES ('{$user[0]}', '{$user[1]}', '$hashed_password', 1);<br>";
}
?>
<?php

require('./includes/connect.php');

$admin_pass = 'admin123';
$hashed_pass = password_hash($admin_pass, PASSWORD_DEFAULT);

$query = "UPDATE users SET password = :password WHERE email = 'admin123@gmail.com'";
$statement = $db->prepare($query);
$statement->bindParam(':password', $hashed_pass);
$statement->execute();


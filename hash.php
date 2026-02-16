<?php
$password = "admin123";

// Hash the password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

echo $hashedPassword;

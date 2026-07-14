<?php
require_once "../config/db.php";

$username = "admin";
$email = "admin@gmail.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$stmt = $pdo->prepare("INSERT INTO users (username,email,password,role) VALUES (?,?,?,?)");
$stmt->execute([$username,$email,$password,$role]);

echo "Admin created successfully";
?>
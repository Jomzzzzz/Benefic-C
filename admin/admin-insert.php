<?php
include('../includes/db.php');

$username = "CjKuji";
$password = "admin123";
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
$stmt->bind_param("ss", $username, $hashedPassword);
$stmt->execute();

echo "Admin inserted.";

<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'subic_hostel_db';

// Connect
$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("DB connection failed: " . $conn->connect_error);
}
$conn->set_charset("utf8mb4");

$username = 'Admin';
$raw_password = 'subicbayhostel';
$email = 'subicbayhostel@gmail.com';  // ✅ Add your admin email here
$hashed_password = password_hash($raw_password, PASSWORD_DEFAULT);

// Check if already exists
$stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "✅ Admin already exists.";
} else {
    // Insert with email
    $stmt = $conn->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $hashed_password, $email);
    if ($stmt->execute()) {
        echo "✅ Admin created successfully!";
    } else {
        echo "❌ Failed to insert admin: " . $stmt->error;
    }
}
$stmt->close();
$conn->close();

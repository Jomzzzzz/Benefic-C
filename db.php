<?php
// Database Configuration

$host = getenv('DB_HOST') ?: 'localhost';
$user = getenv('DB_USER') ?: 'root';
$pass = getenv('DB_PASS') ?: '';
$name = getenv('DB_NAME') ?: 'subic_hostel_db';

$mysqli = new mysqli($host, $user, $pass, $name);

// Check connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
// Set charset
$conn->set_charset("utf8mb4");

// SMTP Email Configuration (PHPMailer)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', '202310393@gordoncollege.edu.ph');      // ✅ Replace with your Gmail
define('SMTP_PASS', 'mssk snal zpcx pwzs');        // ✅ Replace with your Gmail App Password
define('FROM_EMAIL', SMTP_USER);
define('FROM_NAME', 'Subic Bay Hostel');

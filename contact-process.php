
<?php
include('includes/db.php'); // adjust path as needed


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $message = trim($_POST['message']);

    // Simple validation
    if ($name && $email && $message) {
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $message);

        if ($stmt->execute()) {
            $stmt->close();
            // Redirect to thank you page or back with success
            header("Location: contact.php");
            exit;
        } else {
            $error = "Something went wrong. Please try again.";
        }
    } else {
        $error = "All fields are required.";
    }
}
?>

<!-- Optional fallback HTML if user visits the page directly -->
<!DOCTYPE html>
<html>

<head>
    <title>Contact Submission</title>
</head>

<body>
    <p><?= isset($error) ? htmlspecialchars($error) : "Please submit the form." ?></p>
    <a href="contact.php">← Back to Contact Page</a>
</body>

</html>
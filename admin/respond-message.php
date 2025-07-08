<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['to_email']) && !empty($_POST['subject']) && !empty($_POST['message'])) {
    $to = $_POST['to_email'];
    $subject = $_POST['subject'];
    $body = $_POST['message'];

    $mail = new PHPMailer(true);
    try {
        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = SMTP_USER;
        $mail->Password = SMTP_PASS;
        $mail->SMTPSecure = 'tls';
        $mail->Port = SMTP_PORT;

        // Email headers and content
        $mail->setFrom(FROM_EMAIL, FROM_NAME);
        $mail->addAddress($to);

        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($body);
        $mail->AltBody = $body;

        $mail->send();

        // Mark message as responded
        $stmt = $conn->prepare("UPDATE contact_messages SET responded_at = NOW() WHERE email = ?");
        $stmt->bind_param("s", $to);
        $stmt->execute();
        $stmt->close();

        header("Location: contacts.php?status=sent");
        exit;
    } catch (Exception $e) {
        error_log("Mail error: " . $mail->ErrorInfo);
        header("Location: contacts.php?status=error");
        exit;
    }
} else {
    // Redirect back if invalid access or empty fields
    header("Location: contacts.php?status=invalid");
    exit;
}

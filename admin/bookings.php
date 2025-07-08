<?php
session_start();
require '../vendor/autoload.php';
include('../includes/db.php'); // contains DB + SMTP constants

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

// Handle POST for confirming or canceling booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['action'])) {
  $id = intval($_POST['id']);
  $status = $_POST['action'] === 'confirm' ? 'confirmed' : ($_POST['action'] === 'cancel' ? 'cancelled' : 'pending');

  // Update booking status
  $stmt = $conn->prepare("UPDATE bookings SET status = ? WHERE id = ?");
  $stmt->bind_param("si", $status, $id);
  $stmt->execute();
  $stmt->close();

  // Get customer info
  $stmt = $conn->prepare("SELECT full_name, email, phone FROM bookings WHERE id = ?");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $stmt->bind_result($full_name, $email, $phone);
  $stmt->fetch();
  $stmt->close();

  // Send email
  $mail = new PHPMailer(true);
  try {
    $mail->isSMTP();
    $mail->Host       = SMTP_HOST;
    $mail->SMTPAuth   = true;
    $mail->Username   = SMTP_USER;
    $mail->Password   = SMTP_PASS;
    $mail->SMTPSecure = 'tls';
    $mail->Port       = SMTP_PORT;

    $mail->setFrom(FROM_EMAIL, FROM_NAME);
    $mail->addAddress($email, $full_name);

    $mail->isHTML(true);
    $mail->Subject = "Your Booking has been " . ucfirst($status);
    $mail->Body    = "
      <h3>Dear $full_name,</h3>
      <p>Your booking at <strong>Subic Bay Hostel</strong> has been <strong style='color:#DF5219;'>$status</strong>.</p>
      <p>Thank you for choosing us!</p>
      <br><small style='color:gray;'>This is an automated message. Please do not reply.</small>
    ";
    $mail->AltBody = "Dear $full_name,\nYour booking has been $status.\n– Subic Bay Hostel";

    $mail->send();
  } catch (Exception $e) {
    error_log("PHPMailer Error: " . $mail->ErrorInfo);
  }

  // Send SMS using Textbelt
  $message = "Hello $full_name! Your booking at Subic Bay Hostel has been $status. Thank you!";
  $sms = [
    'phone'   => $phone, // make sure this starts with +63
    'message' => $message,
    'key'     => 'textbelt' // free key (1 SMS/day)
  ];

  $context = stream_context_create([
    'http' => [
      'method'  => 'POST',
      'header'  => "Content-type: application/x-www-form-urlencoded",
      'content' => http_build_query($sms)
    ]
  ]);

  $result = file_get_contents("https://textbelt.com/text", false, $context);
  $response = json_decode($result, true);

  if (!$response['success']) {
    error_log("Textbelt SMS failed: " . $response['error']);
  }
}

// Load all bookings
$bookings = $conn->query("SELECT * FROM bookings ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Manage Bookings</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-[#EEE9E6] text-gray-800" x-data="{ menuOpen: false }">

  <div class="bg-white shadow sticky top-0 z-50 p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-[#DF5219]">📋 Manage Bookings</h1>
    <a href="dashboard.php" class="text-[#DF5219] hover:text-[#FFA358]">← Back to Dashboard</a>
  </div>

  <div class="p-6">
    <div class="bg-white rounded-xl shadow overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#DF5219] text-white">
          <tr>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Room</th>
            <th class="px-4 py-2">Guests</th>
            <th class="px-4 py-2">Check-in</th>
            <th class="px-4 py-2">Check-out</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($row = $bookings->fetch_assoc()): ?>
            <tr class="border-b hover:bg-[#fdf4f1]">
              <td class="px-4 py-2"><?= htmlspecialchars($row['full_name']) ?></td>
              <td class="px-4 py-2"><?= $row['room_type'] ?></td>
              <td class="px-4 py-2"><?= $row['adults'] + $row['children'] ?> guest(s)</td>
              <td class="px-4 py-2"><?= $row['check_in'] ?></td>
              <td class="px-4 py-2"><?= $row['check_out'] ?></td>
              <td class="px-4 py-2 capitalize font-semibold text-<?= $row['status'] === 'confirmed' ? 'green' : ($row['status'] === 'cancelled' ? 'red' : 'yellow') ?>-600">
                <?= $row['status'] ?>
              </td>
              <td class="px-4 py-2">
                <div class="flex gap-2">
                  <form method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="action" value="confirm">
                    <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs">✅ Confirm</button>
                  </form>
                  <form method="POST">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="action" value="cancel">
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">❌ Cancel</button>
                  </form>
                </div>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

</body>

</html>
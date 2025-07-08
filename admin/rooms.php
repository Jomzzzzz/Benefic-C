<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

// Automatically move expired bookings to booking_logs
$today = date('Y-m-d');

$expiredStmt = $conn->prepare("SELECT * FROM bookings WHERE status = 'confirmed' AND check_out < ?");
$expiredStmt->bind_param("s", $today);
$expiredStmt->execute();
$expiredResult = $expiredStmt->get_result();

while ($expired = $expiredResult->fetch_assoc()) {
    // Insert into log table with moved_at = now()
    $insertLog = $conn->prepare("INSERT INTO booking_logs 
        (full_name, email, phone, room_type, check_in, check_out, adults, children, created_at, moved_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    
    $adults = (int)$expired['adults'];
    $children = (int)$expired['children'];

    $insertLog->bind_param(
        "ssssssiis",
        $expired['full_name'],
        $expired['email'],
        $expired['phone'],
        $expired['room_type'],
        $expired['check_in'],
        $expired['check_out'],
        $adults,
        $children,
        $expired['created_at']
    );

    if (!$insertLog->execute()) {
        error_log("Failed to insert into booking_logs: " . $insertLog->error);
    }

    // Remove from bookings
    $delete = $conn->prepare("DELETE FROM bookings WHERE id = ?");
    $delete->bind_param("i", $expired['id']);
    if (!$delete->execute()) {
        error_log("Failed to delete expired booking: " . $delete->error);
    }
}

// Fetch only confirmed bookings
$stmt = $conn->prepare("SELECT * FROM bookings WHERE status = 'confirmed' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Confirmed Bookings</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#EEE9E6] text-gray-800">

<!-- Header -->
<div class="bg-white shadow sticky top-0 z-50 p-4 flex justify-between items-center">
  <h1 class="text-2xl font-bold text-[#DF5219]">✅ Confirmed Bookings</h1>
  <a href="dashboard.php" class="text-[#DF5219] hover:text-[#FFA358]">← Back to Dashboard</a>
</div>

<!-- Booking List -->
<div class="p-6">
  <div class="bg-white rounded-xl shadow p-4 overflow-x-auto">
    <table class="min-w-full text-sm">
      <thead class="bg-[#DF5219] text-white">
        <tr>
          <th class="px-4 py-2">Name</th>
          <th class="px-4 py-2">Email</th>
          <th class="px-4 py-2">Phone</th>
          <th class="px-4 py-2">Room Type</th>
          <th class="px-4 py-2">Guests</th>
          <th class="px-4 py-2">Check-in</th>
          <th class="px-4 py-2">Check-out</th>
          <th class="px-4 py-2">Booked On</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($booking = $result->fetch_assoc()): ?>
          <tr class="border-b hover:bg-[#fdf4f1]">
            <td class="px-4 py-2 font-semibold"><?= htmlspecialchars($booking['full_name']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($booking['email']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($booking['phone']) ?></td>
            <td class="px-4 py-2"><?= htmlspecialchars($booking['room_type']) ?></td>
            <td class="px-4 py-2"><?= $booking['adults'] + $booking['children'] ?> guest(s)</td>
            <td class="px-4 py-2"><?= $booking['check_in'] ?></td>
            <td class="px-4 py-2"><?= $booking['check_out'] ?></td>
            <td class="px-4 py-2"><?= date('F j, Y', strtotime($booking['created_at'])) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
</div>

</body>
</html>

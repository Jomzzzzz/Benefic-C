<?php
session_start();

if (!isset($_SESSION['last_payment_method'])) {
  header("Location: index.php");
  exit;
}

$payment_method = $_SESSION['last_payment_method'];
unset($_SESSION['last_payment_method']); 

function renderPaymentInstructions($method) {
  switch ($method) {
    case 'bank_transfer':
      return '
        <div class="bg-[#FFF6F1] p-6 rounded shadow mt-6 text-left">
          <h2 class="text-xl font-bold text-[#DF5219] mb-2">Bank Transfer Details</h2>
          <p>Please transfer the amount to:</p>
          <ul class="mt-2 text-sm leading-relaxed">
            <li><strong>Bank:</strong> BDO</li>
            <li><strong>Account Name:</strong> Subic Bay Hostel</li>
            <li><strong>Account Number:</strong> 1234-5678-9012</li>
          </ul>
          <p class="mt-4">Once paid, send proof to <strong>0915-535-9844</strong> (SMS/Viber).</p>
        </div>';
    case 'gcash':
      return '
        <div class="bg-[#FFF6F1] p-6 rounded shadow mt-6 text-center">
          <h2 class="text-xl font-bold text-[#DF5219] mb-2">GCash Payment</h2>
          <img src="assets/images/gcash-qr.jpg" alt="GCash QR" class="mx-auto mb-4 w-52 rounded">
          <p class="text-sm">Send to: <strong>0915-535-9844</strong><br>Account: Subic Bay Hostel</p>
        </div>';
    case 'pay_at_hotel':
    default:
      return '
        <div class="bg-[#FFF6F1] p-6 rounded shadow mt-6 text-left">
          <h2 class="text-xl font-bold text-[#DF5219] mb-2">Pay at Hotel</h2>
          <p class="text-sm">No need to pay now — just settle your bill at check-in. See you soon!</p>
        </div>';
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Booking Confirmed</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    // Redirect to home after 15 seconds
    setTimeout(() => {
      window.location.href = 'index.php';
    }, 15000);
  </script>
</head>
<body class="bg-[#EEE9E6] text-gray-800">
  <main class="min-h-screen flex flex-col items-center justify-center p-6">
    <div class="max-w-xl w-full bg-white rounded-xl p-8 shadow-md text-center">
      <h1 class="text-3xl font-bold text-[#DF5219] mb-4">🎉 Booking Confirmed!</h1>
      <p class="mb-2">We’ve received your booking and it’s now being processed.</p>
      <p class="text-sm text-gray-600">A confirmation message has been sent to your email and phone.</p>
      <?= renderPaymentInstructions($payment_method) ?>
      <a href="index.php" class="inline-block mt-6 bg-[#DF5219] text-white hover:bg-[#FFA358] px-6 py-2 rounded-full transition" rel="noopener noreferrer">Back to Home</a>
    </div>
  </main>
</body>
</html>

<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

$stmt = $conn->prepare("SELECT * FROM booking_logs ORDER BY moved_at DESC");
$stmt->execute();
$result = $stmt->get_result();

$logs_by_month = [];
while ($log = $result->fetch_assoc()) {
  $monthYear = date('F Y', strtotime($log['moved_at']));
  $logs_by_month[$monthYear][] = $log;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Booking Log Book</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/lucide@latest"></script>
  <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <style>
    ::-webkit-scrollbar { height: 8px; }
    ::-webkit-scrollbar-thumb { background: #DF5219; border-radius: 4px; }
    thead th { position: sticky; top: 0; background-color: #DF5219; }
  </style>
</head>
<body class="bg-[#EEE9E6] text-[#0A1F44] font-sans" x-data="{ showModal: false, details: {} }" @keydown.escape.window="showModal = false">

<!-- Header -->
<header class="bg-white shadow sticky top-0 z-50 p-4 flex justify-between items-center">
  <h1 class="text-2xl font-extrabold text-[#DF5219] flex items-center gap-2">
    <i data-lucide="book-open" class="w-6 h-6"></i> Booking Log Book
  </h1>
  <a href="dashboard.php" class="text-[#DF5219] font-medium hover:text-[#FFA358] transition duration-200 flex items-center gap-1">
    <i data-lucide="arrow-left" class="w-4 h-4"></i> Back to Dashboard
  </a>
</header>

<!-- Log Book Content -->
<main class="p-6 max-w-7xl mx-auto">
  <?php if (empty($logs_by_month)): ?>
    <p class="text-center text-gray-600 mt-10">No logs found.</p>
  <?php else: ?>
    <?php foreach ($logs_by_month as $monthYear => $logs): ?>
      <section class="mb-12">
        <h2 class="text-2xl font-bold text-[#DF5219] mb-4 border-b pb-2"><?= htmlspecialchars($monthYear) ?></h2>
        
        <div class="bg-white rounded-2xl shadow-lg p-4 overflow-x-auto">
          <table class="w-full text-sm text-left border-collapse">
            <thead class="text-white text-xs uppercase tracking-wider">
              <tr class="text-left">
                <th class="px-4 py-3"><i data-lucide="user" class="inline w-4 h-4 mr-1"></i>Name</th>
                <th class="px-4 py-3"><i data-lucide="mail" class="inline w-4 h-4 mr-1"></i>Email</th>
                <th class="px-4 py-3"><i data-lucide="phone" class="inline w-4 h-4 mr-1"></i>Phone</th>
                <th class="px-4 py-3">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
              <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-[#fdf4f1] transition duration-200">
                  <td class="px-4 py-3 font-semibold"><?= htmlspecialchars($log['full_name']) ?></td>
                  <td class="px-4 py-3 break-words"><?= htmlspecialchars($log['email']) ?></td>
                  <td class="px-4 py-3"><?= htmlspecialchars($log['phone']) ?></td>
                  <td class="px-4 py-3">
  <span @click="details = <?= htmlspecialchars(json_encode($log)) ?>; showModal = true"
        class="inline-flex items-center gap-1 text-[#DF5219] hover:text-[#FFA358] cursor-pointer text-sm">
    <i data-lucide="eye" class="w-4 h-4"></i> View
  </span>
</td>

                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>

          <!-- PDF Download Button -->
          <form action="download_pdf.php" method="post" class="mt-6 text-right">
            <input type="hidden" name="monthYear" value="<?= htmlspecialchars($monthYear) ?>">
          <button type="submit"
        class="inline-flex items-center gap-2 text-[#DF5219] hover:text-[#FFA358] transition text-sm">
  <i data-lucide="download" class="w-4 h-4"></i> Download PDF
</button>

          </form>
        </div>
      </section>
    <?php endforeach; ?>
  <?php endif; ?>
</main>

<!-- Booking Details Modal -->
<div x-show="showModal" x-cloak class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 transition">
  <div
    class="bg-white rounded-2xl shadow-2xl p-6 max-w-lg w-full relative animate-fade-in"
    @click.outside="showModal = false"
  >
    <!-- Header -->
    <div class="flex items-center justify-between border-b pb-3 mb-5">
      <h3 class="text-xl font-bold text-[#DF5219] flex items-center gap-2">
        <i data-lucide="info" class="w-5 h-5"></i> Booking Details
      </h3>
      <button @click="showModal = false"
              class="text-gray-400 hover:text-[#DF5219] transition">
        <i data-lucide="x" class="w-5 h-5"></i>
      </button>
    </div>

    <!-- Body Content -->
    <div class="space-y-4 text-sm text-[#0A1F44]">

     <div class="flex items-start gap-2">
        <i data-lucide="user" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Name:</strong> <span x-text="details.full_name"></span></p>
      </div>
      
      <div class="flex items-start gap-2">
        <i data-lucide="bed-double" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Room Type:</strong> <span x-text="details.room_type"></span></p>
      </div>

      <div class="flex items-start gap-2">
        <i data-lucide="users" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Guests:</strong> <span x-text="(parseInt(details.adults) + parseInt(details.children)) + ' guest(s)'"></span></p>
      </div>

      <div class="flex items-start gap-2">
        <i data-lucide="calendar-days" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Check-in:</strong> <span x-text="new Date(details.check_in).toLocaleDateString()"></span></p>
      </div>

      <div class="flex items-start gap-2">
        <i data-lucide="calendar-clock" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Check-out:</strong> <span x-text="new Date(details.check_out).toLocaleDateString()"></span></p>
      </div>

      <div class="flex items-start gap-2">
        <i data-lucide="clock" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Booked On:</strong> <span x-text="new Date(details.created_at).toLocaleDateString()"></span></p>
      </div>

      <div class="flex items-start gap-2">
        <i data-lucide="log-out" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Moved to Log:</strong> <span x-text="new Date(details.moved_at).toLocaleDateString()"></span></p>
      </div>

      <div class="flex items-start gap-2">
        <i data-lucide="phone" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Phone:</strong> <span x-text="details.phone"></span></p>
      </div>

      <div class="flex items-start gap-2">
        <i data-lucide="mail" class="w-4 h-4 text-[#DF5219] mt-0.5"></i>
        <p><strong>Email:</strong> <span x-text="details.email"></span></p>
      </div>

     
    </div>
  </div>
</div>

<script>
  lucide.createIcons();
</script>

</body>
</html>


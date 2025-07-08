<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>

<!-- Main Content -->
<div class="flex-1 min-h-screen">
  <header class="bg-white dark:bg-gray-800 shadow p-4 flex justify-between items-center">
    <h2 class="text-xl font-semibold text-orange">Dashboard</h2>
    <div class="flex items-center gap-4">
      <span class="text-sm">Hi, <strong><?= htmlspecialchars($admin) ?></strong></span>
      <button @click="darkMode = !darkMode" class="hover:text-orange transition">
        <i :data-lucide="darkMode ? 'sun' : 'moon'" class="w-5 h-5"></i>
      </button>
    </div>
  </header>

  <main class="p-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
    <a href="bookings.php" class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg p-6 transition">
      <h3 class="text-lg font-semibold text-orange mb-1">📋 Manage Bookings</h3>
      <p class="text-gray-600 dark:text-gray-400">View, confirm, or cancel guest bookings.</p>
    </a>
    <a href="rooms.php" class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg p-6 transition">
      <h3 class="text-lg font-semibold text-orange mb-1">🛏 Room Types</h3>
      <p class="text-gray-600 dark:text-gray-400">Manage room types, rates, and availability.</p>
    </a>
    <a href="contacts.php" class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg p-6 transition">
      <h3 class="text-lg font-semibold text-orange mb-1">✉️ Contact Messages</h3>
      <p class="text-gray-600 dark:text-gray-400">Read and respond to inquiries.</p>
    </a>
    <a href="admins.php" class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg p-6 transition">
      <h3 class="text-lg font-semibold text-orange mb-1">👤 Admin Accounts</h3>
      <p class="text-gray-600 dark:text-gray-400">Manage admin users and permissions.</p>
    </a>
    <a href="change-password.php" class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg p-6 transition">
      <h3 class="text-lg font-semibold text-orange mb-1">🔒 Change Password</h3>
      <p class="text-gray-600 dark:text-gray-400">Update your login credentials.</p>
    </a>
    <a href="../index.php" class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg p-6 transition">
      <h3 class="text-lg font-semibold text-orange mb-1">🌐 Visit Website</h3>
      <p class="text-gray-600 dark:text-gray-400">Go to the homepage of your site.</p>
    </a>
    <a href="logbook.php" class="bg-white dark:bg-gray-800 rounded-xl shadow hover:shadow-lg p-6 transition">
      <h3 class="text-lg font-semibold text-orange mb-1">📚 View Booking Log Book</h3>
      <p class="text-gray-600 dark:text-gray-400">See all completed bookings for the month.</p>
    </a>
  </main>
</div>

<?php include('includes/footer.php'); ?>

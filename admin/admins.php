<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

// Add new admin
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'], $_POST['email'])) {
  $username = trim($_POST['username']);
  $email = trim($_POST['email']);

  // Server-side Gmail validation
  if (!preg_match('/^[a-zA-Z0-9._%+-]+@gmail\.com$/', $email)) {
    die("❌ Only Gmail addresses are allowed.");
  }

  $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

  $stmt = $conn->prepare("INSERT INTO admins (username, password, email) VALUES (?, ?, ?)");
  $stmt->bind_param("sss", $username, $password, $email);
  $stmt->execute();
  $stmt->close();

  header("Location: admins.php");
  exit;
}

// Delete admin
if (isset($_GET['delete']) && $_GET['delete'] !== $_SESSION['admin']) {
  $username = $_GET['delete'];

  $stmt = $conn->prepare("DELETE FROM admins WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $stmt->close();

  header("Location: admins.php");
  exit;
}

// Fetch admins
$admins = $conn->query("SELECT * FROM admins ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Users</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#EEE9E6] text-gray-800">

  <!-- Header -->
  <div class="bg-white shadow sticky top-0 z-50 p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-[#DF5219]">👤 Manage Admins</h1>
    <a href="dashboard.php" class="text-[#DF5219] hover:text-[#FFA358]">← Back to Dashboard</a>
  </div>

  <!-- Add Admin -->
  <div class="p-6">
    <div class="bg-white p-6 rounded-xl shadow max-w-xl mx-auto mb-8">
      <h2 class="text-xl font-semibold text-[#DF5219] mb-4">Add New Admin</h2>
      <form method="POST" class="space-y-4">
        <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#DF5219]">
        <input type="email" name="email" placeholder="example@gmail.com" pattern=".+@gmail\.com" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#DF5219]">
        <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#DF5219]">
        <button type="submit" class="bg-[#DF5219] hover:bg-[#FFA358] text-white px-6 py-2 rounded-full font-semibold transition">Add Admin</button>
      </form>
    </div>

    <!-- Admin List -->
    <div class="bg-white p-6 rounded-xl shadow">
      <h2 class="text-xl font-semibold text-[#DF5219] mb-4">Current Admins</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm border">
          <thead class="bg-[#DF5219] text-white">
            <tr>
              <th class="px-4 py-2 text-left">Username</th>
              <th class="px-4 py-2 text-left">Email</th>
              <th class="px-4 py-2 text-left">Created</th>
              <th class="px-4 py-2 text-left">Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($admin = $admins->fetch_assoc()): ?>
              <tr class="border-b hover:bg-[#fdf4f1]">
                <td class="px-4 py-2"><?= htmlspecialchars($admin['username']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($admin['email']) ?></td>
                <td class="px-4 py-2 text-gray-500"><?= date("F j, Y g:i A", strtotime($admin['created_at'])) ?></td>
                <td class="px-4 py-2">
                  <?php if ($admin['username'] !== $_SESSION['admin']): ?>
                    <a href="?delete=<?= urlencode($admin['username']) ?>" onclick="return confirm('Delete this admin?')" class="text-red-600 hover:underline text-sm">Delete</a>
                  <?php else: ?>
                    <span class="text-gray-400 text-sm italic">You</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</body>

</html>
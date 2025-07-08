<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $username = $_POST['username'];
  $password = $_POST['password'];

  $stmt = $conn->prepare("SELECT * FROM admins WHERE username = ?");
  $stmt->bind_param("s", $username);
  $stmt->execute();
  $result = $stmt->get_result();

  if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    if (password_verify($password, $admin['password'])) {
      $_SESSION['admin'] = $admin['username'];
      header("Location: dashboard.php");
      exit;
    }
  }

  $error = "Invalid username or password.";
}
?>

<!DOCTYPE html>
<html>

<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#EEE9E6] flex items-center justify-center h-screen">
  <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-sm">
    <h1 class="text-2xl font-bold text-[#DF5219] mb-4 text-center">Admin Login</h1>
    <?php if (isset($error)): ?>
      <div class="text-red-600 text-sm mb-3 text-center"><?= $error ?></div>
    <?php endif; ?>
    <form method="POST" class="space-y-4">
      <input type="text" name="username" placeholder="Username" required class="w-full px-4 py-2 border rounded">
      <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded">
      <button type="submit" class="w-full bg-[#DF5219] hover:bg-[#FFA358] text-white py-2 rounded-full font-semibold transition">Login</button>
    </form>
  </div>
</body>

</html>
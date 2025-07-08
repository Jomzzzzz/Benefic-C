<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    $adminUsername = $_SESSION['admin'];

    // Fetch admin's current hashed password
    $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $adminUsername);
    $stmt->execute();
    $stmt->bind_result($hashedPassword);
    $stmt->fetch();
    $stmt->close();

    // Validate current password
    if (!password_verify($current, $hashedPassword)) {
        $message = "❌ Current password is incorrect.";
        $messageType = 'error';
    } elseif ($new !== $confirm) {
        $message = "❌ New passwords do not match.";
        $messageType = 'error';
    } else {
        // Update password
        $newHashed = password_hash($new, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE admins SET password = ? WHERE username = ?");
        $stmt->bind_param("ss", $newHashed, $adminUsername);

        if ($stmt->execute()) {
            $message = "✅ Password updated successfully.";
            $messageType = 'success';
        } else {
            $message = "❌ Failed to update password.";
            $messageType = 'error';
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Change Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#EEE9E6] text-gray-800">

    <!-- Top Navigation -->
    <div class="bg-white shadow sticky top-0 z-50 p-4 flex justify-between items-center">
        <h1 class="text-2xl font-bold text-[#DF5219]">🔐 Change Password</h1>
        <a href="dashboard.php" class="text-[#DF5219] hover:text-[#FFA358]">← Back to Dashboard</a>
    </div>

    <!-- Form -->
    <div class="p-6 max-w-xl mx-auto">
        <div class="bg-white rounded-xl shadow p-6">
            <?php if ($message): ?>
                <div class="<?= $messageType === 'success' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?> p-4 mb-4 rounded">
                    <?= htmlspecialchars($message) ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="grid gap-4">
                <div>
                    <label class="block font-semibold text-[#DF5219] mb-1">Current Password</label>
                    <input type="password" name="current_password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#DF5219]">
                </div>
                <div>
                    <label class="block font-semibold text-[#DF5219] mb-1">New Password</label>
                    <input type="password" name="new_password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#DF5219]">
                </div>
                <div>
                    <label class="block font-semibold text-[#DF5219] mb-1">Confirm New Password</label>
                    <input type="password" name="confirm_password" required class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-[#DF5219]">
                </div>
                <button type="submit" class="bg-[#DF5219] hover:bg-[#FFA358] text-white font-semibold py-2 px-6 rounded-full transition">
                    Update Password
                </button>
            </form>
        </div>
    </div>

</body>

</html>
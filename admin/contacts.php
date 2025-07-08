<?php
session_start();
include('../includes/db.php');

if (!isset($_SESSION['admin'])) {
  header("Location: login.php");
  exit;
}

$messages = $conn->query("SELECT * FROM contact_messages ORDER BY submitted_at DESC");
?>

<!DOCTYPE html>
<html lang="en" x-data="modalHandler()" xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Contact Messages</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="//unpkg.com/alpinejs" defer></script>
</head>

<body class="bg-[#EEE9E6] text-gray-800">

  <!-- Header -->
  <div class="bg-white shadow sticky top-0 z-50 p-4 flex justify-between items-center">
    <h1 class="text-2xl font-bold text-[#DF5219]">✉️ Contact Messages</h1>
    <a href="dashboard.php" class="text-[#DF5219] hover:text-[#FFA358]">← Back to Dashboard</a>
  </div>

  <!-- Table -->
  <div class="p-6">
    <div class="bg-white rounded-xl shadow overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-[#DF5219] text-white">
          <tr>
            <th class="px-4 py-2">Name</th>
            <th class="px-4 py-2">Email</th>
            <th class="px-4 py-2">Submitted</th>
            <th class="px-4 py-2">Status</th>
            <th class="px-4 py-2">Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($messages->num_rows > 0): ?>
            <?php while ($row = $messages->fetch_assoc()): ?>
              <tr class="border-b hover:bg-[#fdf4f1]">
                <td class="px-4 py-2"><?= htmlspecialchars($row['name']) ?></td>
                <td class="px-4 py-2"><?= htmlspecialchars($row['email']) ?></td>
                <td class="px-4 py-2 text-sm text-gray-500"><?= date("M d, Y h:i A", strtotime($row['submitted_at'])) ?></td>
                <td class="px-4 py-2">
                  <?php if ($row['responded_at']): ?>
                    <span class="inline-block px-2 py-1 text-green-700 bg-green-100 rounded-full text-xs font-medium">✅ Responded</span>
                  <?php else: ?>
                    <span class="inline-block px-2 py-1 text-red-700 bg-red-100 rounded-full text-xs font-medium">⏳ Pending</span>
                  <?php endif; ?>
                </td>
                <td class="px-4 py-2">
                  <button
                    @click="viewMessage('<?= htmlspecialchars($row['name']) ?>', '<?= htmlspecialchars($row['email']) ?>', '<?= date("M d, Y h:i A", strtotime($row['submitted_at'])) ?>', `<?= htmlspecialchars($row['message']) ?>`, <?= $row['responded_at'] ? 'true' : 'false' ?>)"
                    class="flex items-center gap-1 text-blue-600 hover:text-blue-800 text-sm font-medium">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                      stroke="currentColor">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0zM2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 
                            8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    View
                  </button>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="5" class="text-center py-4 text-gray-500">No messages found.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal -->
  <div x-show="show" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50">
    <div class="bg-white rounded-2xl w-full max-w-lg p-6 shadow-2xl relative space-y-4">
      <button @click="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-red-600 text-xl">✖</button>

      <div class="flex items-center gap-2">
        <svg class="h-6 w-6 text-[#DF5219]" fill="none" stroke="currentColor" stroke-width="2"
          viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 
                   0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        <h2 class="text-xl font-semibold text-[#DF5219]">Message Detail</h2>
      </div>

      <div class="text-sm space-y-1">
        <p><strong>Name:</strong> <span x-text="data.name"></span></p>
        <p><strong>Email:</strong> <span x-text="data.email"></span></p>
        <p><strong>Submitted:</strong> <span x-text="data.date"></span></p>
      </div>

      <div class="bg-gray-50 p-4 rounded border text-sm text-gray-700 whitespace-pre-wrap" x-text="data.message"></div>

      <!-- Toggle Respond Form -->
      <template x-if="!data.responded">
        <div class="text-right">
          <button @click="showRespondForm = !showRespondForm" class="text-sm text-[#DF5219] font-medium hover:underline">
            💬 Respond
          </button>
        </div>
      </template>

      <!-- Respond Form -->
      <form x-show="showRespondForm" method="POST" action="respond-message.php" class="space-y-3" x-cloak>
        <input type="hidden" name="to_email" :value="data.email">
        <input type="text" name="subject" placeholder="Subject" required
          class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-[#DF5219]">
        <textarea name="message" rows="4" placeholder="Type your response..." required
          class="w-full px-4 py-2 border rounded focus:ring-2 focus:ring-[#DF5219]"></textarea>
        <div class="flex justify-end gap-2">
          <button type="button" @click="closeModal" class="bg-gray-200 px-4 py-2 rounded hover:bg-gray-300 text-sm">Cancel</button>
          <button type="submit" class="bg-[#DF5219] text-white px-4 py-2 rounded hover:bg-[#FFA358] text-sm">Send</button>
        </div>
      </form>

      <!-- Message Already Responded -->
      <template x-if="data.responded">
        <div class="text-center text-green-700 text-sm font-medium mt-2">
          ✅ You have already responded to this message.
        </div>
      </template>
    </div>
  </div>

  <script>
    function modalHandler() {
      return {
        show: false,
        showRespondForm: false,
        data: {
          name: '',
          email: '',
          date: '',
          message: '',
          responded: false
        },
        viewMessage(name, email, date, message, responded) {
          this.data = {
            name,
            email,
            date,
            message,
            responded
          };
          this.showRespondForm = false;
          this.show = true;
        },
        closeModal() {
          this.show = false;
          this.showRespondForm = false;
        }
      }
    }
  </script>

</body>

</html>
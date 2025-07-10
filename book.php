<?php
session_start();
include('includes/db.php');

$message = '';
$messageType = '';

$full_name = $email = $phone = $check_in = $check_out = $message_field = '';
$total_guests = 1;
$standard_capsules = 0;
$deluxe_capsules = 0;
$agree = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $total_guests = intval($_POST['total_guests'] ?? 1);
    $standard_capsules = intval($_POST['standard_capsules'] ?? 0);
    $deluxe_capsules = intval($_POST['deluxe_capsules'] ?? 0);
    $message_field = trim($_POST['message_field'] ?? '');
    $agree = isset($_POST['agree']);

    $errors = [];

    // Full name validation
    if (!$full_name) {
        $errors[] = "Full name is required.";
    } elseif (strlen($full_name) < 3) {
        $errors[] = "Full name must be at least 3 characters.";
    } elseif (strlen($full_name) > 100) {
        $errors[] = "Full name must not exceed 100 characters.";
    } elseif (!preg_match("/^[a-zA-ZñÑ\s'.-]+$/u", $full_name)) {
        $errors[] = "Full name can only contain letters, spaces, hyphens, apostrophes, and periods.";
    }

    // Email validation
    if (!$email) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } else {
        $email_domain = strtolower(substr(strrchr($email, "@"), 1));
        if ($email_domain !== 'gmail.com') {
            $errors[] = "Only valid Gmail addresses are accepted.";
        } else {
            $stmt = $conn->prepare("SELECT id FROM bookings WHERE email = ? AND status != 'checked_out'");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $errors[] = "This Gmail already has an active reservation.";
            }
            $stmt->close();
        }
    }

    // Phone validation
    if (!$phone) {
        $errors[] = "Phone number is required.";
    } elseif (!preg_match("/^(\+63|0)9\d{9}$/", $phone)) {
        $errors[] = "Phone must be a valid PH mobile number (e.g. 09123456789 or +639123456789).";
    }

    // Dates
    if (!$check_in || !$check_out) {
        $errors[] = "Check-in and Check-out dates are required.";
    } elseif ($check_in >= $check_out) {
        $errors[] = "Check-out date must be after Check-in date.";
    }

    // Guests
    if ($total_guests < 1) {
        $errors[] = "Total guests must be at least 1.";
    }

    if ($standard_capsules < 0 || $deluxe_capsules < 0) {
        $errors[] = "Capsule quantities cannot be negative.";
    }

    if (!$agree) {
        $errors[] = "You must agree to the Terms & Conditions.";
    }

    if (!empty($errors)) {
        $message = "❌ " . implode("<br>", $errors);
        $messageType = 'error';
    } else {
        $stmt = $conn->prepare("SELECT id FROM bookings WHERE email = ? AND status = 'pending'");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $_SESSION['pending_booking'] = $email;
            header("Location: book.php");
            exit;
        }

        $conn->begin_transaction();
        try {
            $stmt = $conn->prepare("
                INSERT INTO bookings 
                (full_name, email, phone, check_in, check_out, total_guests, standard_capsules, deluxe_capsules, special_request, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->bind_param(
                "sssssiiss",
                $full_name, $email, $phone, $check_in, $check_out,
                $total_guests, $standard_capsules, $deluxe_capsules, $message_field
            );
            $stmt->execute();
            $conn->commit();

            $_SESSION['booking_success'] = true;
            header("Location: book.php");
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            $message = "❌ Database error: " . $e->getMessage();
            $messageType = 'error';
        }
    }
}
?>

<?php include('includes/header.php'); ?>
<section class="py-16 px-4 min-h-screen flex justify-center items-start bg-[#FFFCFB]"
         x-data="{
            termsOpen: false,
            agreed: <?= $agree ? 'true' : 'false' ?>,
            scrolledToEnd: false,
            showAlert: false,
            openModal() {
                this.termsOpen = true;
                this.scrolledToEnd = false;
            },
            checkScroll(event) {
                const el = event.target;
                if (el.scrollHeight - el.scrollTop - el.clientHeight < 5) {
                    this.scrolledToEnd = true;
                }
            },
            finish() {
                this.agreed = true;
                this.termsOpen = false;
            },
            validateForm(event) {
                if (!this.agreed) {
                    event.preventDefault();
                    this.showAlert = true;
                    setTimeout(() => this.showAlert = false, 3000);
                }
            }
         }">

  <div class="w-full max-w-3xl bg-white shadow-xl rounded-2xl p-8 sm:p-12">
    <h1 class="text-4xl font-bold mb-8 text-center">Room Reservation</h1>

    <?php if ($message): ?>
      <div class="<?= $messageType === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?> p-4 rounded mb-6 text-center">
        <?= $message ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-8" @submit="validateForm">
      <div class="relative">
        <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>"
               class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
               placeholder="Full Name" required pattern="[A-Za-z\s'.-]+"
               title="Name can include letters, spaces, hyphens, apostrophes and dots."/>
        <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                     peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-3.5">
          Full Name
        </label>
      </div>

      <div class="relative">
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"
               class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
               placeholder="Email" required />
        <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                     peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-3.5">
          Email
        </label>
      </div>

      <div class="relative">
        <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>"
               class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
               placeholder="Phone" required pattern="^(\+63|0)9\d{9}$"
               title="Valid PH mobile number e.g. 09123456789 or +639123456789"/>
        <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                     peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-3.5">
          Phone Number
        </label>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="relative">
          <input type="date" name="check_in" value="<?= htmlspecialchars($check_in) ?>" min="<?= date('Y-m-d') ?>"
                 class="peer w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black" required />
          <label class="absolute left-0 -top-3.5 text-gray-600 text-sm">Check-in</label>
        </div>
        <div class="relative">
          <input type="date" name="check_out" value="<?= htmlspecialchars($check_out) ?>" min="<?= date('Y-m-d') ?>"
                 class="peer w-full border-b-2 border-gray-400 py-4 focus:outline-none focus:border-black" required />
          <label class="absolute left-0 -top-3.5 text-gray-600 text-sm">Check-out</label>
        </div>
      </div>

      <div class="relative">
    <input type="number" name="total_guests"
           class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
           placeholder="Total Guests" />
    <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                 peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-3.5">
      Total Guests
    </label>
</div>


      <div class="space-y-6">
        <h3 class="text-xl font-semibold mb-4">Capsule Type</h3>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-4">
          <label for="standard_capsules" class="font-medium mb-2 sm:mb-0">Standard Capsule</label>
          <div class="flex items-center space-x-2">
            <input type="number" id="standard_capsules" name="standard_capsules"
                   min="0"
                   class="w-20 border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-gray-300"
                   value="<?= htmlspecialchars($standard_capsules) ?>"/>
            <span class="text-gray-500">pcs</span>
          </div>
        </div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b pb-4">
          <label for="deluxe_capsules" class="font-medium mb-2 sm:mb-0">Deluxe Capsule</label>
          <div class="flex items-center space-x-2">
            <input type="number" id="deluxe_capsules" name="deluxe_capsules"
                   min="0"
                   class="w-20 border rounded px-2 py-1 focus:outline-none focus:ring-2 focus:ring-gray-300"
                   value="<?= htmlspecialchars($deluxe_capsules) ?>"/>
            <span class="text-gray-500">pcs</span>
          </div>
        </div>
      </div>

      <div class="relative">
        <textarea name="message_field" rows="4"
                  class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
                  placeholder="Special requests"><?= htmlspecialchars($message_field) ?></textarea>
        <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                     peer-placeholder-shown:top-4 peer-placeholder-shown:text-base peer-focus:-top-3.5">
          Special Request
        </label>
      </div>

      <div class="flex items-start gap-3">
        <input type="checkbox" id="agree" name="agree"
               :checked="agreed"
               @click.prevent="termsOpen = true"
               class="w-5 h-5 accent-black cursor-pointer rounded focus:ring-2 focus:ring-black transition" />
        <label for="agree" class="text-sm text-gray-800 leading-5 cursor-pointer select-none" @click="termsOpen = true">
          I agree to the <span class="underline text-black hover:text-gray-600 transition">Terms & Conditions</span>
        </label>
      </div>
      <template x-if="showAlert">
        <p class="text-xs text-red-600 mt-2">⚠ Please agree to the Terms & Conditions before submitting.</p>
      </template>

      <button type="submit"
              class="w-full bg-black hover:bg-gray-800 text-white font-semibold py-3 rounded-lg shadow transition">
        Submit Booking
      </button>
    </form>

    <!-- Modal -->
    <!-- Modal -->
     <div x-show="termsOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">

      <div class="bg-white w-full max-w-2xl rounded-2xl shadow-xl overflow-hidden relative flex flex-col">
        <button @click="termsOpen = false"
                class="absolute top-4 right-4 text-gray-400 hover:text-black transition text-xl">
          ✕
        </button>
        <div class="bg-black text-white py-5 px-6 rounded-t-2xl">
          <h2 class="text-xl font-bold uppercase tracking-wide text-center">Terms & Conditions</h2>
        </div>
        <div class="px-6 py-6 overflow-y-auto max-h-[60vh] text-gray-800 space-y-5 text-sm leading-relaxed"
             @scroll="checkScroll">
          <p class="text-center">Welcome to Subic Bay Hostel & Dormitory. Please read carefully.</p>
          <div>
            <h3 class="text-black font-semibold mb-2">Booking & Rates</h3>
            <ul class="list-disc list-inside space-y-1">
              <li>Discounts available for extended stays & groups.</li>
              <li>PHP 600/night Standard, PHP 900/night Deluxe Capsule.</li>
              <li>280 Standard & 64 Deluxe capsules available.</li>
            </ul>
          </div>
          <div>
            <h3 class="text-black font-semibold mb-2">Stay Options</h3>
            <ul class="list-disc list-inside space-y-1">
              <li>We accept daily, weekly, monthly, and long-term stays.</li>
              <li>Group discounts are also available.</li>
            </ul>
          </div>
          <div>
            <h3 class="text-black font-semibold mb-2">Reservation & Payments</h3>
            <ul class="list-disc list-inside space-y-1">
              <li>PHP 200 advance payment (refundable).</li>
              <li>Remaining balance due upon check-in.</li>
            </ul>
          </div>
          <div>
            <h3 class="text-black font-semibold mb-2">Cancellation Policy</h3>
            <ul class="list-disc list-inside space-y-1">
              <li>Free cancellation allowed up to 24 hours before check-in.</li>
              <li>Late cancellations or no-shows may result in forfeiture of the PHP 200 advance payment.</li>
            </ul>
          </div>
          <div>
            <h3 class="text-black font-semibold mb-2">Need Help?</h3>
            <p>For any questions, visit our <a href="contact.php" class="underline text-red-700 hover:text-red-900 transition">Contact Page</a>.</p>
          </div>
          <p class="text-center text-xs text-gray-500">
            By completing this process, you agree to these terms.
          </p>
        </div>
        <div class="relative bg-gray-100 py-4 px-6 rounded-b-2xl">
          <div class="h-2 w-full bg-gray-300 rounded-full overflow-hidden mb-4">
            <div class="h-full bg-black transition-all duration-300"
                 :style="`width: ${scrolledToEnd ? '100%' : '30%'}`"></div>
          </div>
          <button :disabled="!scrolledToEnd"
                  @click="finish()"
                  class="w-full py-3 rounded-lg font-semibold transition
                         bg-black text-white hover:bg-gray-800
                         disabled:bg-gray-300 disabled:cursor-not-allowed">
            I Understand & Agree
          </button>
          <div class="text-xs mt-2 text-gray-500 text-center" x-show="!scrolledToEnd">
            Scroll to the bottom to enable
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<?php include('includes/footer.php'); ?>

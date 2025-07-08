<?php
session_start();
include('includes/db.php');

$message = '';
$messageType = '';
$full_name = $email = $phone = $check_in = $check_out = $room_type = $message_field = '';
$number_of_rooms = 1;
$adults = 1;
$children = 0;
$agree = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $full_name = trim($_POST['full_name']);
  $email = trim($_POST['email']);
  $phone = trim($_POST['phone']);
  $check_in = $_POST['check_in'] ?? '';
  $check_out = $_POST['check_out'] ?? '';
  $room_type = $_POST['room_type'] ?? '';
  $number_of_rooms = intval($_POST['number_of_rooms']);
  $adults = intval($_POST['adults']);
  $children = intval($_POST['children']);
  $message_field = trim($_POST['message_field']);
  $agree = isset($_POST['agree']);

  if (!$full_name || !$email || !$phone || !$check_in || !$check_out || !$room_type || !$number_of_rooms || !$agree) {
    $message = "❌ Please fill in all required fields.";
    $messageType = 'error';
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "❌ Invalid email format.";
    $messageType = 'error';
  } else {
    $stmt = $conn->prepare("INSERT INTO bookings 
      (full_name, email, phone, check_in, check_out, room_type, number_of_rooms, adults, children, message) 
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssiiss", $full_name, $email, $phone, $check_in, $check_out, $room_type, $number_of_rooms, $adults, $children, $message_field);

    if ($stmt->execute()) {
      header("Location: book-success.php");
      exit;
    } else {
      $message = "❌ Booking failed. Please try again later.";
      $messageType = 'error';
    }
    $stmt->close();
  }
}
include('includes/header.php');
?>

<section class="py-16 px-4 min-h-screen flex justify-center items-start bg-[#FFFCFB]">
  <div class="w-full sm:max-w-sm md:max-w-md lg:max-w-lg xl:max-w-xl 2xl:max-w-2xl bg-white shadow-xl rounded-2xl p-8 sm:p-12">
    <h1 class="text-4xl font-bold mb-8 text-center">Room Reservation</h1>

    <?php if ($message): ?>
      <div class="<?= $messageType === 'error' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800' ?> p-4 rounded mb-6 text-center">
        <?= htmlspecialchars($message) ?>
      </div>
    <?php endif; ?>

    <form method="POST" class="space-y-8" novalidate>
      <div class="relative">
        <input type="text" name="full_name" value="<?= htmlspecialchars($full_name) ?>"
               class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
               placeholder="Full Name" required />
        <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                     peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                     peer-placeholder-shown:top-4 peer-focus:-top-3.5 peer-focus:text-sm">
          Fullname
        </label>
      </div>

      <div class="relative">
        <input type="email" name="email" value="<?= htmlspecialchars($email) ?>"
               class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
               placeholder="Email" required />
        <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                     peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                     peer-placeholder-shown:top-4 peer-focus:-top-3.5 peer-focus:text-sm">
          Email address
        </label>
      </div>

      <div class="relative">
        <input type="tel" name="phone" value="<?= htmlspecialchars($phone) ?>"
               class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
               placeholder="Phone" required pattern="^(\+63|0)9\d{9}$" />
        <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                     peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                     peer-placeholder-shown:top-4 peer-focus:-top-3.5 peer-focus:text-sm">
          Phone number
        </label>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="relative">
          <input type="date" name="check_in" value="<?= htmlspecialchars($check_in) ?>"
                 min="<?= date('Y-m-d') ?>"
                 class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
                 required />
          <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                       peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                       peer-placeholder-shown:top-4 peer-focus:-top-3.5 peer-focus:text-sm">
            Check in
          </label>
        </div>

        <div class="relative">
          <input type="date" name="check_out" value="<?= htmlspecialchars($check_out) ?>"
                 min="<?= date('Y-m-d') ?>"
                 class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
                 required />
          <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                       peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400 
                       peer-placeholder-shown:top-4 peer-focus:-top-3.5 peer-focus:text-sm">
            Check out
          </label>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="relative">
          <select name="room_type" required
                  class="peer w-full border-b-2 border-gray-400 bg-transparent py-4 focus:outline-none focus:border-black">
            <option value="" disabled <?= !$room_type ? 'selected' : '' ?>>Choose an option</option>
            <option value="Standard Capsule" <?= $room_type === 'Standard Capsule' ? 'selected' : '' ?>>Standard Capsule</option>
            <option value="Deluxe Capsule" <?= $room_type === 'Deluxe Capsule' ? 'selected' : '' ?>>Deluxe Capsule</option>
          </select>
          <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                       peer-focus:-top-3.5 peer-focus:text-sm peer-placeholder-shown:top-4 
                       peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400">
            Room type
          </label>
        </div>

        <div class="relative">
          <select name="number_of_rooms" required
                  class="peer w-full border-b-2 border-gray-400 bg-transparent py-4 focus:outline-none focus:border-black">
            <?php for ($i=1;$i<=5;$i++): ?>
              <option value="<?= $i ?>" <?= $number_of_rooms==$i ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
          </select>
          <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                       peer-focus:-top-3.5 peer-focus:text-sm peer-placeholder-shown:top-4 
                       peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400">
            Number of rooms
          </label>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div class="relative">
          <select name="adults" required
                  class="peer w-full border-b-2 border-gray-400 bg-transparent py-4 focus:outline-none focus:border-black">
            <?php for ($i=1;$i<=5;$i++): ?>
              <option value="<?= $i ?>" <?= $adults==$i ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
          </select>
          <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                       peer-focus:-top-3.5 peer-focus:text-sm peer-placeholder-shown:top-4 
                       peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400">
            Adults
          </label>
        </div>

        <div class="relative">
          <select name="children" required
                  class="peer w-full border-b-2 border-gray-400 bg-transparent py-4 focus:outline-none focus:border-black">
            <?php for ($i=0;$i<=5;$i++): ?>
              <option value="<?= $i ?>" <?= $children==$i ? 'selected' : '' ?>><?= $i ?></option>
            <?php endfor; ?>
          </select>
          <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                       peer-focus:-top-3.5 peer-focus:text-sm peer-placeholder-shown:top-4 
                       peer-placeholder-shown:text-base peer-placeholder-shown:text-gray-400">
            Children
          </label>
        </div>
      </div>

      <div class="relative">
        <textarea name="message_field" rows="4"
                  class="peer w-full border-b-2 border-gray-400 py-4 placeholder-transparent focus:outline-none focus:border-black"
                  placeholder="Special requests"><?= htmlspecialchars($message_field) ?></textarea>
        <label class="absolute left-0 -top-3.5 text-gray-600 text-sm transition-all 
                     peer-placeholder-shown:top-4 peer-placeholder-shown:text-base 
                     peer-placeholder-shown:text-gray-400 peer-focus:-top-3.5 peer-focus:text-sm">
          Message
        </label>
      </div>

      <div class="flex items-center space-x-3">
        <input type="checkbox" name="agree" id="agree" class="accent-black w-5 h-5" <?= $agree ? 'checked' : '' ?> required>
        <label for="agree" class="text-sm text-gray-700">I agree to the terms & conditions</label>
      </div>

      <button type="submit"
              class="w-full bg-black hover:bg-gray-900 text-white font-bold py-4 rounded-lg transition duration-300">
        Submit
      </button>
    </form>
  </div>
</section>

<!-- CTA -->
<section class="py-20 bg-[#F5F5F5] text-black text-center">
  <div class="max-w-4xl mx-auto px-6">
    <h2 class="text-3xl md:text-4xl font-bold mb-4"><?= $text['cta_title'] ?></h2>
    <p class="mb-4 text-lg">📞 0999-996-6852 &nbsp;&nbsp; | &nbsp;&nbsp; 0915-535-9844</p>
    <div class="flex justify-center gap-6 text-2xl my-4">
      <a href="https://www.facebook.com/subicbay.hostelanddormitory.33" class="hover:text-red hover:scale-125 transition" target="_blank"><i class="fi fi-brands-facebook"></i></a>
      <a href="https://www.tiktok.com/@subicbayhostel" class="hover:text-red hover:scale-125 transition" target="_blank"><i class="fi fi-brands-tik-tok"></i></a>
      <a href="https://www.instagram.com/subicbayhostelanddormitory/" class="hover:text-red hover:scale-125 transition" target="_blank"><i class="fi fi-brands-instagram"></i></a>
    </div>
    <p class="mb-6 text-base"><?= $text['cta_desc'] ?></p>
    <a href="book.php" class="inline-block text-white bg-[black] hover:bg-[black] hover:text-white px-4 py-2 rounded-full font-semibold transition-transform hover:scale-125">
      <?= $text['book_now'] ?>
    </a>
  </div>
</section>

<?php include('includes/footer.php'); ?>
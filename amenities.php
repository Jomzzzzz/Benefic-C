<?php include('includes/header.php'); ?>

<!-- External Styles -->
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

<style>
  [x-cloak] { display: none; }
</style>

<!-- Hero Section -->
<section class="bg-[#EEE9E6] py-20 text-center">
  <div class="max-w-4xl mx-auto px-6" data-aos="fade-up">
    <h1 class="text-4xl md:text-5xl font-bold text-[#DF5219] mb-4">Our Amenities</h1>
    <p class="text-lg md:text-xl text-gray-700 font-medium">Capsule Comfort: We Redefine the Way You Stay!</p>
    <p class="mt-4 text-base md:text-lg text-gray-600 max-w-2xl mx-auto">
      The first-ever capsule hotel inside Subic Bay Freeport Zone, offering premium accommodations and perks designed for smart travelers.
    </p>
  </div>
</section>

<!-- Amenities Section -->
<section class="bg-white py-16" x-data="{ lightboxOpen: false, lightboxImage: '' }">
  <div class="max-w-6xl mx-auto px-4 space-y-20">

    <?php
    $amenities = [
      [
        'title' => 'Ultra-Comfy Capsule Beds',
        'desc' => 'Sleep soundly in our private, cozy capsules designed for ultimate rest and privacy.',
        'images' => ['capsule-bed.jpg', 'capsule-bed.jpg', 'capsule-bed.jpg']
      ],
      [
        'title' => 'Free High-Speed Wi-Fi',
        'desc' => 'Stay connected with fast, reliable internet throughout the property.',
        'images' => ['freewifi.jpg', 'freewifi.jpg', 'freewifi.jpg']
      ],
      [
        'title' => 'Hot & Cold Showers',
        'desc' => 'Enjoy refreshing hot or cold showers anytime of day.',
        'images' => ['shower.jpg', 'shower.jpg', 'shower.jpg']
      ],
      [
        'title' => 'Prime Subic Location',
        'desc' => 'Located in the heart of Subic Bay Freeport Zone — close to everything.',
        'images' => ['sbhd-maps.jpg', 'sbhd-maps.jpg', 'sbhd-maps.jpg']
      ],
      [
        'title' => 'Free Parking',
        'desc' => 'Secure, hassle-free parking for our guests.',
        'images' => ['parking.jpg', 'parking.jpg', 'parking.jpg']
      ],
      [
        'title' => 'Competent, Friendly Staff',
        'desc' => 'Experience genuine Filipino hospitality with our courteous team.',
        'images' => ['staff.jpg', 'staff.jpg', 'staff.jpg']
      ]
    ];

    $i = 0;
    foreach ($amenities as $amenity) {
      $reverse = $i % 2 != 0 ? 'md:flex-row-reverse' : '';
      echo '
      <div class="grid md:grid-cols-2 gap-10 items-center '.$reverse.'">
        <div data-aos="'.($i % 2 == 0 ? 'fade-right' : 'fade-left').'" data-aos-delay="100" data-aos-duration="1200">
          <h3 class="text-2xl md:text-3xl font-semibold text-[#222222] mb-4">'.$amenity['title'].'</h3>
          <p class="text-base leading-relaxed">'.$amenity['desc'].'</p>
        </div>
        <div class="flex justify-center gap-6">';
        
        foreach ($amenity['images'] as $index => $image) {
          $delay = 300 + ($index * 150);
          echo '
          <div class="overflow-hidden rounded-lg shadow-lg transform transition duration-500 ease-out-cubic hover:scale-105"
               data-aos="fade-up" data-aos-delay="'.$delay.'" data-aos-duration="1200">
            <img src="assets/images/'.$image.'" alt="'.$amenity['title'].'"
                 class="w-36 md:w-44 aspect-square object-cover cursor-pointer"
                 @click="lightboxOpen = true; lightboxImage = \'assets/images/'.$image.'\'">
          </div>';
        }
        
      echo '</div>
      </div>';
      $i++;
    }
    ?>

  </div>

  <!-- Lightbox Modal -->
  <div x-show="lightboxOpen" x-transition.opacity
       class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50"
       @keydown.escape.window="lightboxOpen = false"
       x-cloak>
    <div @click.away="lightboxOpen = false" class="relative max-w-3xl mx-auto">
      <img :src="lightboxImage" alt="Preview" class="rounded-xl max-h-[90vh] mx-auto shadow-lg">
      <button @click="lightboxOpen = false"
              class="absolute top-2 right-2 text-white bg-black bg-opacity-50 rounded-full p-1 hover:bg-opacity-75">
        ✕
      </button>
    </div>
  </div>
</section>

<script>
  AOS.init({
    duration: 1200,
    easing: "ease-out-cubic",
    once: true
  });
</script>

<?php include('includes/footer.php'); ?>

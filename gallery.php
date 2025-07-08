<?php include('includes/header.php'); ?>


<section class="py-16 bg-[#EEE9E6] min-h-screen">
  <div class="container mx-auto px-4">
    <h2 class="text-3xl font-bold text-[#DF5219] text-center mb-8">Gallery</h2>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6" x-data="{ selected: null }">
      <?php
        $dir = 'assets/images/gallery/';
        $images = glob($dir . '*.{jpg,jpeg,png,gif}', GLOB_BRACE);

        foreach ($images as $img):
      ?>
        <div>
          <img src="<?= $img ?>" alt="Gallery Image" class="rounded-xl shadow hover:scale-105 transition cursor-pointer" @click="selected = '<?= $img ?>'">
        </div>
      <?php endforeach; ?>

      <!-- Lightbox -->
      <div x-show="selected" class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50" x-transition>
        <div class="relative">
          <img :src="selected" class="max-h-[80vh] rounded-lg shadow-2xl">
          <button @click="selected = null" class="absolute top-2 right-2 text-white text-2xl font-bold">×</button>
        </div>
      </div>
    </div>
  </div>
</section>

<?php include('includes/footer.php'); ?>

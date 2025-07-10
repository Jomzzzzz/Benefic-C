<?php include('includes/header.php'); ?>
<link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn-uicons.flaticon.com/uicons-brands/css/uicons-brands.css">
<style>
  [x-cloak] { display: none !important; }
  html { scroll-behavior: smooth; }
</style>

<section x-data="{ lightboxOpen: false, lightboxImage: '', modalOpen: false, activeRoom: {} }">
  <!-- Hero -->
  <section class="relative py-32 text-center bg-cover bg-center bg-no-repeat" style="background-image: url('assets/images/rooms/mission-1.jpg');" data-aos="fade-down">
    <div class="absolute inset-0 bg-black bg-opacity-20"></div>
    <div class="relative z-10 max-w-4xl mx-auto px-6 md:px-8 text-white">
      <h1 class="text-4xl md:text-5xl font-bold mb-6 drop-shadow-lg">Our Capsules</h1>
      <p class="text-lg md:text-xl leading-relaxed italic drop-shadow-md">
        Choose from our clean, modern capsule rooms — designed for privacy, comfort, and affordability.
      </p>
    </div>
  </section>

<div x-data="{
  modalOpen: false,
  lightboxOpen: false,
  lightboxImage: '',
  activeRoom: {},
  rooms: [
    {
      img: 'assets/images/rooms/standard1.png',
      title: 'Standard Capsule',
      desc: 'Price starts at PHP 600.00',
      extra: true
    },
    {
      img: 'assets/images/rooms/deluxe1.png',
      title: 'Deluxe Capsule',
      desc: 'Price starts at PHP 900.00',
      extra: true
    }
  ]
}">
  <!-- Intro Section -->
  <section class="bg-[#FFFCFB] py-24 px-4 text-[#222]">
    <div class="max-w-6xl mx-auto grid md:grid-cols-2 gap-16 items-center">
      <div data-aos="fade-right">
        <p class="text-sm text-gray-500 uppercase mb-3">Recharge Your Soul: Tranquility Starts Here</p>
        <h2 class="text-4xl md:text-5xl leading-tight mb-6">Capsule Comfort:<br>We Redefine the Way You Stay!</h2>
        <p class="text-base text-gray-600 max-w-xl">The First Ever Capsule Hotel inside Subic Bay Freeport Zone giving great value for your hard-earned money with great accommodations and guest perks!</p>
      </div>
      <div class="flex justify-center gap-6 flex-wrap mt-8">
        <template x-for="(room, i) in rooms" :key="i">
          <div class="text-center" :data-aos="i === 0 ? 'fade-right' : 'zoom-in'" :data-aos-delay="(i+2)*100">
            <img 
              :src="room.img" 
              :alt="room.title"
              class="w-48 md:w-60 aspect-square hover:scale-[1.05] object-cover shadow-lg cursor-pointer transition"
              @click.stop="lightboxOpen = true; lightboxImage = room.img"
            >
            <p class="mt-3 text-sm text-gray-600 font-medium" x-text="room.title"></p>
            <button 
              @click="modalOpen = true; activeRoom = room"
              class="cursor-pointer rounded-md bg-[#0F0F0F] px-3 py-1 text-sm text-white shadow-lg shadow-neutral-500/20 transition active:scale-[.95]"
            >
              BOOK NOW!
            </button>
          </div>
        </template>
      </div>
    </div>
  </section>

  <!-- Modal -->
  <div 
    x-show="modalOpen"
    x-transition 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    @click.self="modalOpen = false"
    style="display: none;"
  >
    <div 
      class="relative bg-white rounded-3xl shadow-2xl p-8 max-w-2xl w-full mx-4"
      @click.stop
      x-transition:enter="transition ease-out duration-300"
      x-transition:enter-start="opacity-0 translate-y-10"
      x-transition:enter-end="opacity-100 translate-y-0"
      x-transition:leave="transition ease-in duration-200"
      x-transition:leave-start="opacity-100 translate-y-0"
      x-transition:leave-end="opacity-0 translate-y-10"
    >
      <button @click="modalOpen = false" class="absolute top-4 right-4 text-gray-400 hover:text-[#F94144] transition">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none"
             viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
        </svg>
      </button>

      <h3 class="text-3xl font-bold mb-3 text-[#F94144]" x-text="activeRoom.title"></h3>
      <p class="text-gray-600 text-lg mb-6" x-text="activeRoom.desc"></p>

      <template x-if="activeRoom.extra">
        <div class="space-y-6">
          <div>
            <h4 class="text-lg font-semibold mb-2">Also available:</h4>
            <div class="flex flex-wrap gap-3">
              <span class="bg-gray-100 rounded-full px-4 py-1 text-sm text-gray-700">Weekly</span>
              <span class="bg-gray-100 rounded-full px-4 py-1 text-sm text-gray-700">Monthly</span>
              <span class="bg-gray-100 rounded-full px-4 py-1 text-sm text-gray-700">Yearly</span>
              <span class="bg-gray-100 rounded-full px-4 py-1 text-sm text-gray-700">Group Discount!</span>
            </div>
          </div>
          <div>
            <h4 class="text-lg font-semibold mb-2">Maximum Capacity:</h4>
            <p class="text-gray-600">344 Capsules</p>
          </div>
          <div>
            <h4 class="text-lg font-semibold mb-2">Inclusions:</h4>
            <ul class="grid grid-cols-2 gap-3 text-gray-600">
              <template x-for="item in [
                'Lockable space','Free unlimited Wi-Fi','Airconditioned capsule wing',
                'Water & electricity','Laundry services','Purified water',
                'Spacious parking space','Cafeteria'
              ]">
                <li class="flex items-center gap-2">
                  <span class="h-2 w-2 bg-[#F94144] rounded-full"></span> <span x-text="item"></span>
                </li>
              </template>
            </ul>
          </div>
        </div>
      </template>
     <a href="book.php">

  <button
            class="cursor-pointer mt-6 bg-[#F94144] px-10 py-2 text-sm text-white shadow-lg shadow-neutral-500/20 transition active:scale-[.95]">
            Book
          </button> </a>

      <div class="mt-8 text-right">
      </div>
    </div>
  </div>

  <!-- Lightbox -->
  <div 
    x-show="lightboxOpen"
    x-transition 
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/80"
    @click="lightboxOpen = false"
    style="display: none;"
  >
    <img :src="lightboxImage" alt="Room" class="max-h-[90vh] max-w-[90vw] rounded-lg shadow-lg">
  </div>
</div>



  <!-- Standard Capsules -->
  <section class="bg-[#F5F5F5] py-10 px-4 text-[#222]">
    <div class="max-w-6xl mx-auto mb-12 text-center" data-aos="fade-up">
      <h2 class="text-4xl md:text-5xl font-semibold mb-2"><span class="underline decoration-[#F94144]">Standard</span> Capsule</h2>
      <p class="max-w-3xl mx-auto text-base text-gray-600">
        Our competitively priced capsule rooms are equipped to give you a comfortable stay that is within your budget. 
        Our rooms are clean and may fit your short/long term stay needs.
      </p>
    </div>
    <div class="flex justify-center gap-6 flex-wrap max-w-6xl mx-auto">
      <template x-for="(room, i) in [
        {img:'assets/images/rooms/standard1.png', lightbox:'assets/images/rooms/standard1.png', title:'Premier Double', desc:'A spacious room with elegant decor and a comfortable double bed.'},
        {img:'assets/images/rooms/standard2.png', lightbox:'assets/images/rooms/standard2.png', title:'Junior Suite', desc:'A stylish suite featuring a separate seating area.'},
        {img:'assets/images/rooms/standard3.png', lightbox:'assets/images/rooms/standard3.png', title:'Deluxe King', desc:'Enjoy the luxury of a king-sized bed and modern amenities.'},
        {img:'assets/images/rooms/mission-1.jpg', lightbox:'assets/images/rooms/mission-1.jpg', title:'Premier Twin', desc:'Perfect for friends or colleagues, this twin room offers comfort.'}
      ]" :key="i">
        <div class="text-center" data-aos="fade-up" :data-aos-delay="(i+1)*100">
          <img :src="room.img" :alt="room.title"
               class="w-48 md:w-60 aspect-square hover:scale-[1.05] object-cover shadow-lg cursor-pointer transition"
               @click="lightboxOpen = true; lightboxImage = room.lightbox">
          <p class="mt-3 text-sm text-gray-600 font-medium" x-text="room.title"></p>
          <button @click="modalOpen = true; activeRoom = room"
                  class="cursor-pointer rounded-md bg-[#0F0F0F] px-6 py-1 text-sm text-white shadow-lg shadow-neutral-500/20 transition active:scale-[.95]">
            MORE INFO
          </button>
        </div>
      </template>
    </div>
  </section>

  <!-- Deluxe Capsules -->
  <section class="bg-[#FFFCFB] py-10 px-4 text-[#222]">
    <div class="max-w-6xl mx-auto mb-12 text-center" data-aos="fade-up">
      <h2 class="text-4xl md:text-5xl font-semibold mb-4"><span class="underline decoration-[#F94144]">Deluxe</span> Capsule</h2>
      <p class="max-w-3xl mx-auto text-base text-gray-600">
        Great for budget travelers and groups. Discounts available for big groups, school trips, team buildings, and sports activities.
      </p>
    </div>
    <div class="flex justify-center gap-6 flex-wrap max-w-6xl mx-auto">
      <template x-for="(room, i) in [
        {img:'assets/images/rooms/deluxe1.png', lightbox:'assets/images/rooms/deluxe1.png', title:'Premier Double', desc:'A spacious room with elegant decor and a comfortable double bed.'},
        {img:'assets/images/rooms/deluxe2.png', lightbox:'assets/images/rooms/deluxe2.png', title:'Junior Suite', desc:'A stylish suite featuring a separate seating area.'},
        {img:'assets/images/rooms/deluxe3.png', lightbox:'assets/images/rooms/deluxe3.png', title:'Deluxe King', desc:'Enjoy the luxury of a king-sized bed and modern amenities.'},
        {img:'assets/images/rooms/mission-1.jpg', lightbox:'assets/images/rooms/mission-1.jpg', title:'Premier Twin', desc:'Perfect for friends or colleagues, this twin room offers comfort.'}
      ]" :key="i">
        <div class="text-center" data-aos="fade-up" :data-aos-delay="(i+1)*100">
          <img :src="room.img" :alt="room.title"
               class="w-48 md:w-60 aspect-square hover:scale-[1.05] object-cover shadow-lg cursor-pointer transition"
               @click="lightboxOpen = true; lightboxImage = room.lightbox">
          <p class="mt-3 text-sm text-gray-600 font-medium" x-text="room.title"></p>
           <button @click="modalOpen = true; activeRoom = room"
                  class="cursor-pointer rounded-md bg-[#0F0F0F] px-6 py-1 text-sm text-white shadow-lg shadow-neutral-500/20 transition active:scale-[.95]">
            MORE INFO
          </button>
        </div>
      </template>
    </div>
  </section>

  <!-- Shared Lightbox -->
  <div x-show="lightboxOpen" x-transition @keydown.escape.window="lightboxOpen = false"
       class="fixed inset-0 bg-black/80 flex items-center justify-center z-50 px-4"
       x-cloak @click.self="lightboxOpen = false">
    <img :src="lightboxImage" class="max-h-[90vh] rounded-xl shadow-2xl" alt="Enlarged Room">
  </div>

  <!-- Shared More Info Modal -->
  <div x-show="modalOpen" x-transition @keydown.escape.window="modalOpen = false"
       class="fixed inset-0 bg-black/70 flex items-center justify-center z-50 px-4"
       x-cloak @click.self="modalOpen = false">
    <div class="bg-white max-w-lg w-full p-6 relative shadow-2xl border border-gray-200">
      <button @click="modalOpen = false"
              class="absolute top-4 right-4 text-gray-500 hover:text-red-500 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
      <div class="space-y-4">
        <img :src="activeRoom.lightbox" alt="Room Image"
             class="w-full h-64 object-cover border border-gray-300 shadow">
        <h3 class="text-2xl font-bold text-[#222]" x-text="activeRoom.title"></h3>
        <p class="text-gray-600 leading-relaxed" x-text="activeRoom.desc"></p>
      </div>
      <div class="mt-6 text-right">
        <button @click="modalOpen = false"
                class="inline-block bg-[#DF5219] text-white py-2 px-6 font-semibold hover:bg-[#F94144] transition">
          Close
        </button>
      </div>
    </div>
  </div>
</section>
<?php include('includes/cta.php'); ?>

<script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init({ duration: 800, once: true });</script>
<?php include('includes/footer.php'); ?>

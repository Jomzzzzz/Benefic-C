<?php include('lang.php'); ?>

<!DOCTYPE html>
<html lang="<?= $lang; ?>">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Subic Bay Hostel & Dormitory</title>

  <script src="https://cdn.tailwindcss.com"></script>
  <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />

  <style>
    body { font-family: 'Inter', sans-serif; opacity: 0; transition: opacity 0.2s; }
    body.loaded { opacity: 1; }
    [x-cloak] { display: none !important; }
  </style>
  <script>
    document.addEventListener("DOMContentLoaded", () => document.body.classList.add("loaded"));
  </script>
</head>

<body class="bg-white text-gray-800">

<header 
  x-data="{ scrolled: false, open: false, activeLink: window.location.pathname }"
  @scroll.window="scrolled = window.scrollY > 10"
  :class="scrolled ? 'bg-white shadow-md py-2' : 'bg-transparent py-4'"
  class="fixed top-0 inset-x-0 z-50 transition-all duration-500 ease-in-out">

  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
    <!-- Logo -->
    <a href="index.php" 
       class="flex items-center gap-2 transition-transform duration-300"
       :class="scrolled ? 'scale-90' : 'scale-100'">
      <img src="assets/images/sbhd-logo.png" alt="Subic Bay Hostel & Dormitory Logo" class="h-10 sm:h-12 w-auto">
    </a>

    <!-- Desktop Nav -->
    <nav class="hidden md:flex space-x-6 lg:space-x-8 items-center text-base font-medium">
      <?php
        $links = [
          'index.php' => $text['home'],
          'about.php' => $text['about'],
          'rooms.php' => $text['rooms'],
          'book.php' => $text['book'],
          'contact.php' => $text['contact']
        ];
        foreach ($links as $file => $label):
      ?>
        <a href="<?= $file ?>"
           :class="{ 'text-[#F94144] border-b-2 border-[#F94144]': activeLink.includes('<?= pathinfo($file, PATHINFO_FILENAME); ?>') }"
           class="pb-0 hover:text-[#F94144] transition">
          <?= $label; ?>
        </a>
      <?php endforeach; ?>
      <a href="facilities.php"
         :class="{ 'text-[#F94144]': activeLink.includes('facilities') }"
         class="pb-0 hover:text-[#F94144] transition"><?= $text['facilities'] ?? 'Facilities'; ?></a>
      <a href="hawker.php"
         :class="{ 'text-[#F94144]': activeLink.includes('food') }"
         class="pb-0 hover:text-[#F94144] transition"><?= $text['foodcourt'] ?? 'Food Court'; ?></a>
    </nav>

    <!-- Desktop CTA -->
    <a href="book.php"
       class="hidden md:inline-block bg-[#F94144] text-white px-5 py-2 rounded-full text-sm font-semibold hover:bg-red-600 transition duration-300 hover:scale-105 ml-3 lg:ml-6">
      <?= $text['book']; ?>
    </a>

    <!-- Mobile Menu Button -->
    <button @click="open = !open"
            class="md:hidden bg-[#F94144] text-white px-4 py-2 rounded-lg text-sm flex items-center gap-1 focus:outline-none">
      Menu
      <svg class="w-4 h-4" fill="none" viewBox="0 0 10 6">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
      </svg>
    </button>
  </div>

  <!-- Mobile Nav -->
  <div x-show="open"
       x-transition:enter="transition transform duration-300 ease-out"
       x-transition:enter-start="-translate-y-4 opacity-0"
       x-transition:enter-end="translate-y-0 opacity-100"
       x-transition:leave="transition transform duration-200 ease-in"
       x-transition:leave-start="translate-y-0 opacity-100"
       x-transition:leave-end="-translate-y-4 opacity-0"
       @click.outside="open = false"
       class="md:hidden absolute top-full left-0 w-full bg-white shadow-lg z-40 rounded-b-lg">
    <div class="flex flex-col px-6 py-4 space-y-3">
      <?php foreach ($links as $file => $label): ?>
        <a href="<?= $file ?>"
           :class="{ 'text-[#F94144]': activeLink.includes('<?= pathinfo($file, PATHINFO_FILENAME); ?>') }"
           class="hover:text-[#F94144] transition"><?= $label; ?></a>
      <?php endforeach; ?>
      <a href="facilities.php"
         :class="{ 'text-[#F94144]': activeLink.includes('facilities') }"
         class="hover:text-[#F94144] transition"><?= $text['facilities'] ?? 'Facilities'; ?></a>
      <a href="hawker.php"
         :class="{ 'text-[#F94144]': activeLink.includes('food') }"
         class="hover:text-[#F94144] transition"><?= $text['foodcourt'] ?? 'Food Court'; ?></a>

      <!-- Language dropdown -->
      <div x-data="{ openLang: false }" class="relative">
        <button @click="openLang = !openLang"
                class="flex items-center justify-between w-full hover:text-[#F94144] transition">
          <span><?= $text['language']; ?></span>
          <svg :class="{ 'rotate-180': openLang }" class="w-4 h-4 transition-transform duration-300" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.084l3.71-3.854a.75.75 0 111.08 1.04l-4.25 4.41a.75.75 0 01-1.08 0l-4.25-4.41a.75.75 0 01.02-1.06z" clip-rule="evenodd"/>
          </svg>
        </button>
        <div x-show="openLang" x-transition
             class="mt-2 bg-white border border-gray-200 shadow-xl rounded-md absolute left-0 w-40 text-sm z-50">
          <a href="?lang=en" data-lang="en" class="block px-3 py-2 hover:bg-gray-100">🇬🇧 English</a>
          <a href="?lang=tl" data-lang="tl" class="block px-3 py-2 hover:bg-gray-100">🇵🇭 Tagalog</a>
          <a href="?lang=ja" data-lang="ja" class="block px-3 py-2 hover:bg-gray-100">🇯🇵 Japanese</a>
          <a href="?lang=zh" data-lang="zh" class="block px-3 py-2 hover:bg-gray-100">🇨🇳 Chinese</a>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- Language Persistence -->
<script>
  document.addEventListener('DOMContentLoaded', () => {
    const savedLang = localStorage.getItem('sbhd-lang');
    const currentLang = new URLSearchParams(window.location.search).get('lang');
    if (!currentLang && savedLang) {
      window.location.href = '?lang=' + savedLang;
    }
    document.querySelectorAll('[data-lang]').forEach(link => {
      link.addEventListener('click', () => {
        localStorage.setItem('sbhd-lang', link.dataset.lang);
      });
    });
  });
</script>

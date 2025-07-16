<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <title>ZedMemes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind CSS (Utility-first CSS Framework) -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <!-- Font Awesome (For Icons) -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <!-- Custom Styles -->
  <!-- <link rel="stylesheet" href="./css/style.css"> -->

  <!-- Moment.js (For Date/Time Formatting) -->
  <script src="https://momentjs.com/downloads/moment.js"></script>

  <!-- jQuery Library -->
  <!-- CDN version (commented out - optional alternative) -->
  <!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->

  <!-- Local version (recommended if working offline) -->
  <script src="./assets/Jquery/jquery-3.7.1.min.js"></script>


</head>


<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Header -->
  <header
    class="sticky top-0 z-50 text-white px-5 py-5 flex items-center justify-between flex-wrap gap-4"
    style="background: linear-gradient(to right, #4c7feeff, #7ba3f9ff, #8847f8ff);">
    <a href="index.php">
      <h1 class="text-xl font-bold whitespace-nowrap">ZedMemes</h1>
    </a>

    <div class="flex items-center flex-wrap gap-2 text-sm">
      <?php if (!isset($_SESSION['user_id'])): ?>
        <button
          onclick="$('#loginModal').removeClass('hidden')"
          class="bg-white text-blue-600 px-4 py-1 rounded hover:bg-gray-100 transition">
          <i class="fa-solid fa-right-to-bracket"></i> Signin
        </button>
        <button
          onclick="$('#registerModal').removeClass('hidden')"
          class="bg-white text-blue-600 px-4 py-1 rounded hover:bg-gray-100 transition">
          <i class="fa-solid fa-right-to-bracket"></i> Signup
        </button>
      <?php else: ?>
        <button
          onclick="$('#uploadModal').removeClass('hidden')"
          class="bg-white text-blue-600 px-4 py-1 rounded hover:bg-gray-100 transition">
          <i class="fas fa-file-upload"></i> Upload Meme
        </button>
        <a
          href="php/auth/logout.php"
          class=" hover:text-gray-400 transition"
          title="Logout">
          <button
            class="bg-white text-blue-600 px-4 py-1 rounded hover:bg-gray-100 transition">
            <i class="fa-solid fa-right-from-bracket"></i> Logout
          </button>
        </a>

        <span class="mr-2 whitespace-nowrap">
          <h2>
            Hi, <?= htmlspecialchars($_SESSION['username']) ?>
          </h2>
        </span>


      <?php endif; ?>
    </div>
  </header>

  <!-- Meme Feed -->
  <main id="memeFeed" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3 p-3">
    <!-- Loaded by AJAX -->
  </main>

  <!-- Login Modal -->
  <div id="loginModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl w-[90%] max-w-sm p-6 space-y-4 animate-fade-in">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-blue-700">Login</h2>
        <button onclick="$('#loginModal').addClass('hidden')" class="text-gray-400 hover:text-red-500 text-lg">&times;</button>
      </div>

      <form id="loginForm" class="space-y-3">
        <input
          type="email"
          name="email"
          placeholder="Email"
          required
          class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-blue-300" />

        <div class="relative">
          <input
            type="password"
            name="password"
            id="loginPassword"
            placeholder="Password"
            required
            class="w-full px-3 py-2 border rounded-md pr-10 focus:outline-none focus:ring focus:ring-blue-300" />
          <button
            type="button"
            onclick="togglePassword('loginPassword', this)"
            class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-500 hover:text-blue-600">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>

        <button
          type="submit"
          class="w-full bg-blue-600 text-white py-2 rounded-md hover:bg-blue-700 transition">
          Login
        </button>
      </form>

      <button onclick="$('#loginModal').addClass('hidden')" class="block text-center text-sm text-gray-500 hover:text-gray-700">
        Cancel
      </button>
    </div>
  </div>

  <!-- Register Modal -->
  <div id="registerModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl w-[90%] max-w-sm p-6 space-y-4 animate-fade-in">
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-green-700">Sign Up</h2>
        <button onclick="$('#registerModal').addClass('hidden')" class="text-gray-400 hover:text-red-500 text-lg">&times;</button>
      </div>

      <form id="registerForm" class="space-y-3">
        <input
          type="text"
          name="username"
          placeholder="Username"
          required
          class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-green-300" />

        <input
          type="email"
          name="email"
          placeholder="Email"
          required
          class="w-full px-3 py-2 border rounded-md focus:outline-none focus:ring focus:ring-green-300" />

        <!-- Password Field -->
        <div class="relative">
          <input
            type="password"
            name="password"
            id="registerPassword"
            placeholder="Password"
            required
            class="w-full px-3 py-2 border rounded-md pr-10 focus:outline-none focus:ring focus:ring-green-300" />
          <button
            type="button"
            onclick="togglePassword('registerPassword', this)"
            class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-500 hover:text-green-600">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>

        <!-- Confirm Password Field -->
        <div class="relative">
          <input
            type="password"
            name="password_confirm"
            id="registerConfirmPassword"
            placeholder="Confirm Password"
            required
            class="w-full px-3 py-2 border rounded-md pr-10 focus:outline-none focus:ring focus:ring-green-300" />
          <button
            type="button"
            onclick="togglePassword('registerConfirmPassword', this)"
            class="absolute inset-y-0 right-2 flex items-center text-sm text-gray-500 hover:text-green-600">
            <i class="fa-solid fa-eye"></i>
          </button>
        </div>

        <button
          type="submit"
          class="w-full bg-green-600 text-white py-2 rounded-md hover:bg-green-700 transition">
          Sign Up
        </button>
      </form>

      <button onclick="$('#registerModal').addClass('hidden')" class="block text-center text-sm text-gray-500 hover:text-gray-700">
        Cancel
      </button>
    </div>
  </div>

  <!-- Upload Modal -->
  <div id="uploadModal" class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl shadow-xl w-[90%] max-w-md p-6 space-y-5 animate-fade-in">

      <div class="flex justify-between items-center">
        <h2 class="text-xl font-bold text-purple-700">Upload Meme</h2>
        <button onclick="$('#uploadModal').addClass('hidden')" class="text-gray-400 hover:text-red-500 text-lg">&times;</button>
      </div>

      <form id="uploadForm" enctype="multipart/form-data" class="space-y-4">

        <!-- Custom File Input -->
        <div class="w-full">
          <label for="memeUpload" class="block text-sm font-medium text-gray-700 mb-1">Select a meme</label>

          <label
            for="memeUpload"
            class="flex flex-col items-center justify-center gap-3 cursor-pointer border-2 border-dashed border-purple-400 rounded-lg px-6 py-8 text-purple-600 hover:border-purple-600 hover:bg-purple-50 transition text-center">
            <i class="fa-solid fa-upload text-3xl"></i>
            <span class="text-sm font-medium">Click to choose an image file</span>
            <span id="fileNameDisplay" class="text-xs text-gray-500"></span>
          </label>

          <input
            id="memeUpload"
            type="file"
            name="meme_image"
            accept="image/*"
            required
            class="hidden"
            onchange="showFileName(this)" />
        </div>

        <!-- Upload Button -->
        <button
          type="submit"
          class="w-full bg-purple-600 text-white py-2 rounded-md hover:bg-purple-700 transition">
          Upload
        </button>
      </form>

      <button onclick="$('#uploadModal').addClass('hidden')" class="block text-center text-sm text-gray-500 hover:text-gray-700">
        Cancel
      </button>
    </div>
  </div>
  <script src="./js/main.js" defer></script>
</body>

</html>
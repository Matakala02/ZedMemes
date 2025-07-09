<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ZedMemes</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <!-- Tailwind CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">

  <!-- Optional custom styles -->
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- jQuery -->
  <script src="assets/js/jquery-3.7.1.min.js"></script>

  <!-- Main JS -->
  <script src="assets/js/main.js" defer></script>
</head>

<body class="bg-gray-100 min-h-screen flex flex-col">

  <!-- Header -->
  <header class="bg-blue-600 text-white p-4 flex justify-between items-center">
    <h1 class="text-xl font-bold">ZedMemes</h1>
    <div>
      <?php if (!isset($_SESSION['user_id'])): ?>
        <button onclick="$('#loginModal').removeClass('hidden')" class="bg-white text-blue-600 px-3 py-1 rounded mr-2">Login</button>
        <button onclick="$('#registerModal').removeClass('hidden')" class="bg-white text-blue-600 px-3 py-1 rounded">Sign Up</button>
      <?php else: ?>
        <span class="mr-2">Hi, <?= htmlspecialchars($_SESSION['username']) ?></span>
        <a href="auth/logout.php" class="underline mr-4">Logout</a>
        <button onclick="$('#uploadModal').removeClass('hidden')" class="bg-white text-blue-600 px-3 py-1 rounded">Upload Meme</button>
      <?php endif; ?>
    </div>
  </header>

  <!-- Meme Feed -->
  <main id="memeFeed" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 p-4">
    <!-- Loaded by AJAX -->
  </main>

  <!-- Login Modal -->
  <div id="loginModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded shadow w-80">
      <h2 class="text-lg font-bold mb-4">Login</h2>
      <form id="loginForm">
        <input type="email" name="email" placeholder="Email" required class="w-full mb-2 p-2 border">
        <input type="password" name="password" placeholder="Password" required class="w-full mb-4 p-2 border">
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded w-full">Login</button>
      </form>
      <button onclick="$('#loginModal').addClass('hidden')" class="mt-2 text-sm text-gray-500">Cancel</button>
    </div>
  </div>

  <!-- Register Modal -->
  <div id="registerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded shadow w-80">
      <h2 class="text-lg font-bold mb-4">Sign Up</h2>
      <form id="registerForm">
        <input type="text" name="username" placeholder="Username" required class="w-full mb-2 p-2 border">
        <input type="email" name="email" placeholder="Email" required class="w-full mb-2 p-2 border">
        <input type="password" name="password" placeholder="Password" required class="w-full mb-4 p-2 border">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded w-full">Sign Up</button>
      </form>
      <button onclick="$('#registerModal').addClass('hidden')" class="mt-2 text-sm text-gray-500">Cancel</button>
    </div>
  </div>

  <!-- Upload Modal -->
  <div id="uploadModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden">
    <div class="bg-white p-6 rounded shadow w-80">
      <h2 class="text-lg font-bold mb-4">Upload Meme</h2>
      <form id="uploadForm" enctype="multipart/form-data">
        <input type="file" name="meme_image" accept="image/*" required class="w-full mb-4">
        <button type="submit" class="bg-purple-600 text-white px-4 py-2 rounded w-full">Upload</button>
      </form>
      <button onclick="$('#uploadModal').addClass('hidden')" class="mt-2 text-sm text-gray-500">Cancel</button>
    </div>
  </div>

</body>
</html>
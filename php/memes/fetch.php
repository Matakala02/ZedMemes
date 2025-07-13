<?php
session_start();
require_once "../../db.php";

// Define your base URL for easier path management
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/ZedMemes/');


$sql = "SELECT memes.id AS meme_id, memes.image_path, users.username
        FROM memes
        JOIN users ON memes.user_id = users.id
        ORDER BY memes.uploaded_at DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($meme = $result->fetch_assoc()) {
        $meme_id = $meme['meme_id'];
        $img = htmlspecialchars($meme['image_path']);
        $username = htmlspecialchars($meme['username']);

        // Get reaction counts
        $count_stmt = $conn->prepare("
            SELECT 
                SUM(type = 'like') AS likes,
                SUM(type = 'upvote') AS upvotes
            FROM reactions
            WHERE meme_id = ?
        ");
        $count_stmt->bind_param("i", $meme_id);
        $count_stmt->execute();
        $counts = $count_stmt->get_result()->fetch_assoc();

        $likes = $counts['likes'] ?? 0;
        $upvotes = $counts['upvotes'] ?? 0;

        // Meme card HTML output
        echo '
        <div class="bg-white rounded shadow p-3">
          <img src="' . BASE_URL . $img . '" alt="'. $img .'" class="w-full h-auto rounded mb-2">
          <p class="text-sm text-gray-600 mb-2">Posted by ' . $username . '</p>
          
          <div class="flex items-center justify-between text-sm">
            <button class="react-btn text-blue-600" data-meme="' . $meme_id . '" data-type="like">
              👍 <span id="like-count-' . $meme_id . '">' . $likes . '</span>
            </button>
            <button class="react-btn text-green-600" data-meme="' . $meme_id . '" data-type="upvote">
              🔺 <span id="upvote-count-' . $meme_id . '">' . $upvotes . '</span>
            </button>
            <button class="share-btn text-purple-600" data-link="' . BASE_URL . $img . '">🔗</button>
            <button class="download-btn text-gray-700" data-url="' . BASE_URL . $img . '">⬇️</button>
          </div>
        </div>
        ';
    }
} else {
    echo '<p class="col-span-full text-center text-gray-500">No memes posted yet.</p>';
}
?>

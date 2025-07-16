<?php
session_start();
require_once "../../db.php";

define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/ZedMemes/');
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "GET") {
  $get_memes_with_users_query = "
    SELECT 
      memes.meme_id,
      memes.image_path,
      memes.uploaded_at,
      users.user_id,
      users.username,
      COUNT(CASE WHEN reactions.reaction_type = 'like' THEN 1 END) AS like_count,
      COUNT(CASE WHEN reactions.reaction_type = 'upvote' THEN 1 END) AS upvote_count
    FROM memes
    JOIN users ON memes.user_id = users.user_id
    LEFT JOIN reactions ON memes.meme_id = reactions.meme_id
    GROUP BY memes.meme_id
    ORDER BY memes.uploaded_at DESC
  ";

  $result = $conn->query($get_memes_with_users_query);

  if ($result && $result->num_rows > 0) {
    $memes = [];

    while ($row = $result->fetch_assoc()) {
      $memes[] = [
        "meme" => [
          "meme_id"     => $row['meme_id'],
          "meme_url"    => BASE_URL . $row['image_path'],
          "uploaded_at" => $row['uploaded_at'],
        ],
        "user" => [
          "username" => $row['username']
        ],
        "reactions" => [
          "like"   => (int)$row['like_count'],
          "upvote" => (int)$row['upvote_count']
        ]
      ];
    }

    echo json_encode([
      "success" => true,
      "data" => $memes
    ]);
  } else {
    echo json_encode([
      "success" => false,
      "message" => "No memes found."
    ]);
  }

  exit;
}

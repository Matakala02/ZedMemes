<?php
session_start();
require_once "../../db.php";
header('Content-Type: application/json');

// Define base URL for meme preview
define('BASE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/ZedMemes/');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["success" => false, "message" => "You must be logged in to upload."]);
        exit;
    }
    $user_id = $_SESSION['user_id'];

    // Check uploaded file
    if (!isset($_FILES['meme_image']) || $_FILES['meme_image']['error'] !== UPLOAD_ERR_OK) {
        echo json_encode(["success" => false, "message" => "Image upload failed."]);
        exit;
    }

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $file_type = $_FILES['meme_image']['type'];

    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(["success" => false, "message" => "Only JPG, PNG, GIF, and WEBP are allowed."]);
        exit;
    }

    // Prepare upload path
    $filename = time() . "_" . basename($_FILES['meme_image']['name']);
    $target_dir = "../../uploads/";
    $target_path = $target_dir . $filename;

    // Create upload folder if it doesn't exist
    if (!is_dir($target_dir)) {
        if (!mkdir($target_dir, 0755, true)) {
            echo json_encode(["success" => false, "message" => "Failed to create uploads directory."]);
            exit;
        }
    }

    // Move uploaded file
    if (move_uploaded_file($_FILES['meme_image']['tmp_name'], $target_path)) {
        $relative_path = "uploads/" . $filename;

        // Insert meme
        $insert_meme_stmt = $conn->prepare("
            INSERT INTO memes (user_id, image_path) 
            VALUES (?, ?)
        ");
        $insert_meme_stmt->bind_param("is", $user_id, $relative_path);

        if ($insert_meme_stmt->execute()) {
            $new_meme_id = $insert_meme_stmt->insert_id;

            // Fetch newly inserted meme
            $fetch_meme_stmt = $conn->prepare("
                SELECT memes.meme_id, memes.image_path, memes.uploaded_at,
                       users.username,
                       COUNT(CASE WHEN reactions.reaction_type = 'like' THEN 1 END) AS like_count,
                       COUNT(CASE WHEN reactions.reaction_type = 'upvote' THEN 1 END) AS upvote_count
                FROM memes
                JOIN users ON memes.user_id = users.user_id
                LEFT JOIN reactions ON memes.meme_id = reactions.meme_id
                WHERE memes.meme_id = ?
                GROUP BY memes.meme_id
            ");
            $fetch_meme_stmt->bind_param("i", $new_meme_id);
            $fetch_meme_stmt->execute();
            $result = $fetch_meme_stmt->get_result();

            if ($result && $row = $result->fetch_assoc()) {
                $added_meme = [
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

                echo json_encode([
                    "success" => true,
                    "data" => $added_meme
                ]);
                exit;
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Failed to fetch inserted meme."
                ]);
                exit;
            }
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Database insert failed."
            ]);
            exit;
        }
    } else {
        echo json_encode(["success" => false, "message" => "Failed to move uploaded file."]);
        exit;
    }
}

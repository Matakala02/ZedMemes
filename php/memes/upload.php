<?php
session_start();
require_once "../../db.php";
header('Content-Type: application/json');

// Check login
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "You must be logged in to upload."]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Check if file is sent
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

// Move file to assets/memes/
$filename = time() . "_" . basename($_FILES['meme_image']['name']);
$target_dir = "../../uploads/";
$target_path = $target_dir . $filename;

if (!is_dir($target_dir)) {
    if (!mkdir($target_dir, 0755, true)) {
        echo json_encode(["success" => false, "message" => "Failed to create uploads directory."]);
        exit;
    }
}

if (move_uploaded_file($_FILES['meme_image']['tmp_name'], $target_path)) {
    $relative_path = "uploads/" . $filename;

    // Save to DB
    $stmt = $conn->prepare("INSERT INTO memes (user_id, image_path) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $relative_path);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false, "message" => "Database insert failed."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Failed to move uploaded file."]);
}
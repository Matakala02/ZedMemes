<?php
session_start();
require_once "../db.php";
header('Content-Type: application/json');

// Must be logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "message" => "Login required to react."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$meme_id = intval($_POST['meme_id']);
$type = $_POST['type'];

// Validate type
if (!in_array($type, ['like', 'upvote'])) {
    echo json_encode(["success" => false, "message" => "Invalid reaction type."]);
    exit;
}

// Check if already reacted
$check = $conn->prepare("SELECT id FROM reactions WHERE meme_id = ? AND user_id = ? AND type = ?");
$check->bind_param("iis", $meme_id, $user_id, $type);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    // Already reacted → remove
    $del = $conn->prepare("DELETE FROM reactions WHERE meme_id = ? AND user_id = ? AND type = ?");
    $del->bind_param("iis", $meme_id, $user_id, $type);
    $del->execute();
} else {
    // Not reacted → insert
    $insert = $conn->prepare("INSERT INTO reactions (meme_id, user_id, type) VALUES (?, ?, ?)");
    $insert->bind_param("iis", $meme_id, $user_id, $type);
    $insert->execute();
}

// Return new count
$count = $conn->prepare("SELECT COUNT(*) AS total FROM reactions WHERE meme_id = ? AND type = ?");
$count->bind_param("is", $meme_id, $type);
$count->execute();
$res = $count->get_result()->fetch_assoc();

echo json_encode([
    "success" => true,
    "count" => $res['total']
]);
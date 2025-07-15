<?php
session_start();
require_once "../../db.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!isset($_SESSION['user_id'])) {
        echo json_encode([
            "success" => false,
            "message" => "Login required to react."
        ]);
        exit;
    }

    $user_id = $_SESSION['user_id'];
    $request_body = json_decode(file_get_contents('php://input'), true);

    $meme_id = $request_body['meme_id'] ?? null;
    $reaction_type = $request_body['reaction_type'] ?? null;

    // Validate meme ID
    if (!$meme_id) {
        echo json_encode([
            "success" => false,
            "message" => "Meme ID is required."
        ]);
        exit;
    }

    // Validate reaction type
    $valid_reactions = ['like', 'upvote'];
    if (!in_array($reaction_type, $valid_reactions)) {
        echo json_encode([
            "success" => false,
            "message" => "Invalid reaction type."
        ]);
        exit;
    }

    // Check if user already reacted to this meme with this reaction type
    $check_reaction_stmt = $conn->prepare("
        SELECT reaction_id 
        FROM reactions 
        WHERE meme_id = ? AND user_id = ? AND reaction_type = ?
    ");
    $check_reaction_stmt->bind_param("iis", $meme_id, $user_id, $reaction_type);
    $check_reaction_stmt->execute();
    $check_reaction_stmt->store_result();

    if ($check_reaction_stmt->num_rows > 0) {
        // If already reacted → remove reaction
        $delete_reaction_stmt = $conn->prepare("
            DELETE FROM reactions 
            WHERE meme_id = ? AND user_id = ? AND reaction_type = ?
        ");
        $delete_reaction_stmt->bind_param("iis", $meme_id, $user_id, $reaction_type);
        $delete_reaction_stmt->execute();
    } else {
        // Not yet reacted → insert new reaction
        $add_reaction_stmt = $conn->prepare("
            INSERT INTO reactions (meme_id, user_id, reaction_type) 
            VALUES (?, ?, ?)
        ");
        $add_reaction_stmt->bind_param("iis", $meme_id, $user_id, $reaction_type);
        $add_reaction_stmt->execute();
    }

    // Get updated total count for that reaction type
    $reaction_count_stmt = $conn->prepare("
        SELECT COUNT(*) AS total 
        FROM reactions 
        WHERE meme_id = ? AND reaction_type = ?
    ");
    $reaction_count_stmt->bind_param("is", $meme_id, $reaction_type);
    $reaction_count_stmt->execute();

    $reaction_count_result = $reaction_count_stmt->get_result()->fetch_assoc();

    echo json_encode([
        "success" => true,
        "count" => (int)$reaction_count_result['total']
    ]);
}

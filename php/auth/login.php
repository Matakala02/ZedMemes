<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "../../db.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    // Making sure inputs are not empty.
    if (empty($email)) {
        echo json_encode(["success" => false, "message" => "Email is required."]);
        exit;
    }

    if (empty($password)) {
        echo json_encode(["success" => false, "message" => "Password is required."]);
        exit;
    }

    // Check if user exists
    $get_user_by_email_query = "SELECT * FROM users WHERE email = ?";
    $user_stmt = $conn->prepare($get_user_by_email_query);
    $user_stmt->bind_param("s", $email);
    $user_stmt->execute();
    $user_results = $user_stmt->get_result();

    if ($user_results && $user_results->num_rows === 1) {
        $user = $user_results->fetch_assoc();
        if (password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            echo json_encode(["success" => true, "user" => $user["username"]]);
        } else {
            echo json_encode(["success" => false, "message" => "Incorrect password."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "No user found with that email."]);
    }
}

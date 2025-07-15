<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "../../db.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST['username'] ?? "");
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";
    $password_confirm = $_POST['password_confirm'] ?? "";


    // Validate inputs
    if (empty($username)) {
        echo json_encode(["success" => false, "message" => "Username is required."]);
        exit;
    }
    if (empty($email)) {
        echo json_encode(["success" => false, "message" => "Email is required."]);
        exit;
    }
    if (empty($password)) {
        echo json_encode(["success" => false, "message" => "Password is required."]);
        exit;
    }
    if (empty($password_confirm)) {
        echo json_encode(["success" => false, "message" => "Confirm password is required."]);
        exit;
    }

    if ($password !== $password_confirm) {
        echo json_encode(["success" => false, "message" => "Confirm password do not match."]);
        exit;
    }

    // Check for existing username
    $username_check_stmt = $conn->prepare("SELECT user_id FROM users WHERE username = ?");
    $username_check_stmt->bind_param("s", $username);
    $username_check_stmt->execute();
    $username_check_stmt->store_result();

    if ($username_check_stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Username already taken."]);
        exit;
    }

    // Check for existing email
    $email_check_stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $email_check_stmt->bind_param("s", $email);
    $email_check_stmt->execute();
    $email_check_stmt->store_result();

    if ($email_check_stmt->num_rows > 0) {
        echo json_encode(["success" => false, "message" => "Email already registered."]);
        exit;
    }

    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert new user into the database
    $insert_user_stmt = $conn->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $insert_user_stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($insert_user_stmt->execute()) {
        $_SESSION['user_id'] = $insert_user_stmt->insert_id;
        $_SESSION['username'] = $username;
        echo json_encode(["success" => true, "user" => $username]);
    } else {
        echo json_encode(["success" => false, "message" => "User registration failed."]);
    }
}

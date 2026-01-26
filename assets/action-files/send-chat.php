<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../../includes/connection.php");

try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'message' => 'Not logged in'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    $userID = (int)$_SESSION['user_id'];
    
    $stmt = $connection->prepare("SELECT username FROM users WHERE id = ?");
    $stmt->execute([$userID]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $name = $row['username'] ?? 'User';


    $meeting_code = $_POST['code'] ?? null;
    $message = trim($_POST['message'] ?? '');

    if (!$meeting_code || $message === '') {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Invalid input'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Get meeting ID
    $stmt = $connection->prepare("SELECT id FROM meetings WHERE code = ?");
    $stmt->execute([$meeting_code]);
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$meeting) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Meeting not found'], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Insert message
    $stmt = $connection->prepare("
        INSERT INTO chat_messages (meeting_id, user_id, name, message)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([(int)$meeting['id'], $userID, $name, $message]);

    echo json_encode(['status' => 'success'], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status" => "error",
        "message" => "Server error in send-chat.php",
        "detail" => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

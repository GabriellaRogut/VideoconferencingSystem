<?php
header('Content-Type: application/json; charset=utf-8');
require_once("../../includes/connection.php");

try {
    $meeting_code = $_GET['code'] ?? null;
    $last_id = isset($_GET['last_id']) ? (int)$_GET['last_id'] : 0;

    if (!$meeting_code) {
        echo json_encode([]);
        exit;
    }

    // Get meeting ID by code
    $stmt = $connection->prepare("
        SELECT id
        FROM meetings
        WHERE code = ?
        LIMIT 1
    ");
    $stmt->execute([$meeting_code]);
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$meeting) {
        echo json_encode([]);
        exit;
    }

    // Fetch messages + sender username
    $stmt = $connection->prepare("
        SELECT
            cm.id,
            cm.meeting_id,
            cm.user_id,
            u.username AS name,
            cm.message,
            cm.sent_at
        FROM chat_messages cm
        JOIN users u ON u.id = cm.user_id
        WHERE cm.meeting_id = ? AND cm.id > ?
        ORDER BY cm.id ASC
    ");
    $stmt->execute([(int)$meeting['id'], $last_id]);
    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);



    // Fetch en/disab
    $stmt = $connection->prepare("
        SELECT chat_enabled
        FROM meetings
        WHERE id = ?
    ");
    $stmt->execute([(int)$meeting['id']]);
    $enabled = $stmt->fetchColumn();


    // echo json_encode([
    //     "chat_enabled" => $enabled,
    //     "messages" => $messages
    // ], JSON_UNESCAPED_UNICODE);

   echo json_encode( [
        "chat_enabled" => $enabled,
        "messages" => $messages
    ], JSON_UNESCAPED_UNICODE);


} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Server error in fetch-chat.php",
        "detail"  => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}

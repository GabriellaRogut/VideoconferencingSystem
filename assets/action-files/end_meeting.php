<?php
    include_once("../../includes/connection.php");

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    $meeting_id = $data['meeting_id'] ?? null;

    if (!$meeting_id) {
        http_response_code(400);
        echo json_encode(['error' => 'Липсва ID на срещата.']);
        exit;
    }

    // Check if current user is host
    $stmt = $connection->prepare("
        SELECT host_id 
        FROM meetings 
        WHERE id = ?
    ");
    $stmt->execute([$meeting_id]);
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$meeting) {
        http_response_code(404);
        echo json_encode(['error' => 'Срещата не съществува.']);
        exit;
    }

    if ($meeting['host_id'] != $_SESSION['user_id']) {
        http_response_code(403);
        echo json_encode(['error' => 'Нямате права да приключите тази среща.']);
        exit;
    }

    // End the meeting 
    $stmt = $connection->prepare("
        UPDATE meetings 
        SET end_time = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$meeting_id]);

    echo json_encode(['success' => true, 'message' => 'Срещата е приключена.']);
    exit;
?>
<?php
    include_once("../../includes/connection.php");

    $userID = $_SESSION['user_id'];
    $code = $_GET['code'] ?? null;

    $stmt = $connection->prepare("
        SELECT id 
        FROM meetings 
        WHERE code = ?
    ");
    $stmt->execute([$code]);
    $meeting = $stmt->fetch();

    if (!$meeting) {
        die("Невалиден код");
    }

    $stmt = $connection->prepare("
    INSERT IGNORE INTO participants (meeting_id, user_id, role)
    VALUES (?, ?, 'participant')
    ");
    $stmt->execute([$meeting['id'], $userID]);

    header("Location: ../../waiting.php?code=$code");
exit;

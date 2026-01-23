<?php
    include_once("../../includes/connection.php");

    $errors_meeting = [];

    $userID = $_SESSION['user_id'];
    $code = $_GET['code'] ?? null;

    $stmt = $connection->prepare("
        SELECT id, status
        FROM meetings 
        WHERE code = ?
    ");
    $stmt->execute([$code]);
    $meeting = $stmt->fetch();

    if (!$meeting) {
        $errors_meeting[] = "Невалиден вход.";
        $_SESSION['errors_meeting'] = $errors_meeting;
        header("Location: ../../meetings.php");
        exit;
    }

    if ($meeting['status'] != "ended") {

        $stmt = $connection->prepare("
            INSERT IGNORE INTO participants (meeting_id, user_id, role)
            VALUES (?, ?, 'joiner')
        ");
        $stmt->execute([$meeting['id'], $userID]);

        header("Location: ../../waiting.php?code=$code");
        exit;
    } else {
        $errors_meeting[] = "Тази среща е вече приключила.";
        $_SESSION['errors_meeting'] = $errors_meeting;

        header("Location: ../../meetings.php");
        exit;
    }
?>
<?php
    include_once("../../includes/connection.php");

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../index.php");
        exit;
    }

    $userID = $_SESSION['user_id'];

    // Generate meeting code
    function generateMeetingCode($length = 6) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }

    $code = generateMeetingCode();

    // Insert meeting
    $stmt = $connection->prepare("
        INSERT INTO meetings (code, host_id, start_time, status)
        VALUES (?, ?, NOW(), 'waiting')
    ");
    $stmt->execute([$code, $userID]);

    $meeting_id = $connection->lastInsertId();

    // Add host as participant
    $stmt = $connection->prepare("
        INSERT INTO participants (meeting_id, user_id, role)
        VALUES (?, ?, 'host')
    ");
    $stmt->execute([$meeting_id, $userID]);

    // Redirect to waiting room
    header("Location: ../../waiting.php?code=$code");
    exit;
?>

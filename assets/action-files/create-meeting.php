<?php
    include_once("../../includes/connection.php");

    $userID = $_SESSION['user_id'];

    // GENERATE MEETING CODE
    function generateMeetingCode($length = 6) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $code = '';
        for ($i = 0; $i < $length; $i++) {
            $code .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $code;
    }

    $code = generateMeetingCode();

    // Set start time to now
    $start_time = new DateTime("now");

    // Compute end time
    $end_time = clone $start_time;
    $end_time->modify("+{$duration_minutes} minutes");

    // Insert meeting
    $stmt = $connection->prepare("
        INSERT INTO meetings (code, host_id, start_time, end_time, duration_minutes, status)
        VALUES (?, ?, ?, ?, ?, 'waiting')
    ");
    $stmt->execute([
        $code,
        $userID,
        $start_time->format('Y-m-d H:i:s'),
        $end_time->format('Y-m-d H:i:s'),
        $duration_minutes
    ]);

    $meeting_id = $connection->lastInsertId();

    // Add host as participant
    $stmt = $connection->prepare("
        INSERT INTO participants (meeting_id, user_id, role)
        VALUES (?, ?, 'host')
    ");
    $stmt->execute([$meeting_id, $userID]);

    header("Location: ../../waiting.php?code=$code");
exit;

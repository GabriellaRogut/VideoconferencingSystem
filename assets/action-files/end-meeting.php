<?php
    include_once("../../includes/connection.php");

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../index.php");
        exit;
    }

    $userID = $_SESSION['user_id'];
    $meeting_code = trim($_GET['code'] ?? '');

    if (!$meeting_code) {
        die("Срещата не е намерена. Подаденият код е: '" . htmlspecialchars($meeting_code) . "'");
    }


    // Fetch meeting info and check if current user is the host
    $stmt = $connection->prepare("
        SELECT * 
        FROM meetings 
        WHERE code = ?
    ");
    $stmt->execute([$meeting_code]);
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$meeting) {
        die("Срещата не е намерена.");
    }

    // Check if the user is host
    if ($meeting['host_id'] != $userID) {
        die("Само организаторът на срещата има право да я приключи.");
    }

    
    // UPDATE MEETING STATUS AND END TIME
    $stmt = $connection->prepare("
        UPDATE meetings 
        SET status = 'ended', end_time = NOW() 
        WHERE id = ?
    ");
    $stmt->execute([$meeting['id']]);


    // INCREMENT MEETING COUNT FOR ALL PARTICIPANTS
    $stmt = $connection->prepare("
        UPDATE users
        SET total_meetings = total_meetings + 1
        WHERE id IN (
            SELECT user_id
            FROM participants
            WHERE meeting_id = ?
        )
    ");
    $stmt->execute([$meeting['id']]);


    // DELETE ALL MESSAGES FROM THE MEETING
    $stmt = $connection->prepare("
        DELETE FROM chat_messages
        WHERE meeting_id = ?
    ");
    $stmt->execute([$meeting['id']]);



    header("Location: ../../meetings.php");
    exit;
?>

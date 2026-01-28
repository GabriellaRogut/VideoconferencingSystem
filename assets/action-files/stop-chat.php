<?php 
    include_once("../../includes/connection.php");

    $meeting_code = $_GET['id'] ?? null;

    $stmt = $connection->prepare("
        UPDATE meetings 
        SET chat_enabled = IF(chat_enabled = 1, 0, 1)
        WHERE id = ?
    ");
    $stmt->execute([$meeting_code]);

?>
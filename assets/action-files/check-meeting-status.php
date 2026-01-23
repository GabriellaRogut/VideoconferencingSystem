<?php
    include_once("../../includes/connection.php");
    
    $code = trim($_GET['code'] ?? '');
    $stmt = $connection->prepare("SELECT status FROM meetings WHERE code = ?");
    $stmt->execute([$code]);
    $meeting = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode(['status' => $meeting['status'] ?? 'ended']);
?>

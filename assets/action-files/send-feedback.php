<?php
include_once("../../includes/connection.php");

try {
    if (!isset($_POST['send-opinion'])) {
        header("Location: ../../index.php");
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $message  = trim($_POST['message'] ?? '');

    // validation
    if ($message === '') {
        header("Location: ../../index.php");
        exit;
    }


    $stmt = $connection->prepare("
        INSERT INTO feedbacks (username, message, created_at, status)
        VALUES (:username, :message, NOW(), :status)
    ");

    $stmt->execute([
        ':username' => $username !== '' ? $username : null,
        ':message'  => $message,
        ':status' => 'pending'
    ]);

    header("Location: ../../index.php");
    exit;

} catch (PDOException $e) {
    header("Location: ../../index.php");
    exit;
}


// make it go to admin for check first
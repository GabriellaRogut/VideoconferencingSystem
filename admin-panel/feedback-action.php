<?php
    include_once("../includes/connection.php");

    if (!isset($_SESSION['is_admin'])) {
        header("Location: ../index.php");
        exit;
    }

    if (isset($_POST['approve'])) {
        $stmt = $connection->prepare("UPDATE feedbacks SET status='approved' WHERE id=:id");
        $stmt->execute([':id' => $_POST['id']]);
    }

    if (isset($_POST['reject'])) {
        $stmt = $connection->prepare("UPDATE feedbacks SET status='rejected' WHERE id=:id");
        $stmt->execute([':id' => $_POST['id']]);
    }

    header("Location: admin.php");
    exit;
?>

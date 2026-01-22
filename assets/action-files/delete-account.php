<?php
    include_once("../../includes/connection.php");

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../index.php");
        exit;
    }

    $userID = $_SESSION['user_id'];

    try {
        $connection->beginTransaction();

        // Delete user
        $stmt = $connection->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userID]);

        // Commit transaction
        $connection->commit();

        // Remove all authentication/session data
        unset($_SESSION['user_id']);
        unset($_SESSION['username']);

        // Add flash message
        $_SESSION['account_deleted'] = "Акаунтът е изтрит успешно.";

        
        header("Location: ../../index.php");
        exit;

    } catch (PDOException $e) {
        $connection->rollBack();
        error_log("Error deleting user: " . $e->getMessage());
        
        $_SESSION['delete_error'] = "Възникна грешка при изтриване на акаунта. Опитайте отново.";
        header("Location: ../../account.php");
        exit;
    }
?>

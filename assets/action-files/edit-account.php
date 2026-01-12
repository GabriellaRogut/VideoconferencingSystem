<?php
session_start();
include("../../includes/connection.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../index.php");
    exit;
}

if (isset($_POST['update_account'])) {

    $userID = $_SESSION['user_id'];
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    $errors_update = [];

    // validation
    if (!$username || !$email) {
        $errors_update[] = "Име и имейл са задължителни.";
    }

    // current password hash
    $stmt = $connection->prepare("
        SELECT password_hash 
        FROM users 
        WHERE id = ?
    ");
    $stmt->execute([$userID]);
    $user = $stmt->fetch();

    // Check if changing password
    if ($new_password) {
        if (!$current_password) {
            $errors_update[] = "Въведете текуща парола.";
        } elseif (!password_verify($current_password, $user['password_hash'])) {
            $errors_update[] = "Текущата парола е грешна.";
        } elseif ($new_password !== $confirm_password) {
            $errors_update[] = "Паролата не съвпада.";
        } else {
            $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
        }
    }

    if (!$errors_update) {
        // Update username and email
        $sql = "
            UPDATE users 
            SET username = ?, email = ?" . ($new_password ? ", password_hash = ?" : "") . " 
            WHERE id = ?
        ";
        $stmt = $connection->prepare($sql);

        if ($new_password) {
            $stmt->execute([$username, $email, $new_password_hash, $userID]);
        } else {
            $stmt->execute([$username, $email, $userID]);
        }

        // Update session
        $_SESSION['username'] = $username;

        header("Location: ../../account.php?success=1");
        exit;
    } else {
        $_SESSION['edit_errors'] = $errors_update;
        header("Location: ../../account.php");
        exit;
    }
}

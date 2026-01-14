<?php
include("../../includes/connection.php");

if (isset($_POST['signup'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $_SESSION['errors_signup'] = [];


    if (!$username || !$email || !$password) {
        $_SESSION['errors_signup'][] = "Попълнете всички полета!";
    }


    $stmt = $connection->prepare("
        SELECT id 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $_SESSION['errors_signup'][] = "Този имейл вече съществува!";
    }


    if (!$_SESSION['errors_signup']) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $connection->prepare("
            INSERT INTO users (username, email, password_hash)
            VALUES (?, ?, ?)
        ")->execute([$username, $email, $password_hash]);

        $_SESSION['user_id'] = $connection->lastInsertId();
        $_SESSION['username'] = $username;

        echo "<script>document.location.href='../../account.php';</script>";
        exit;
    }
}

      echo "<script>document.location.href='../../index.php';</script>";
        exit;
?>
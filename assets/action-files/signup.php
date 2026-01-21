<?php
include_once("../../includes/connection.php");

if (isset($_POST['signup'])) {

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $errors_signup = [];
   

    if (!$username || !$email || !$password) {
        $errors_signup[] = "Попълнете всички полета!";
    }


    $stmt = $connection->prepare("
        SELECT id 
        FROM users 
        WHERE email = ?
    ");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors_signup[] = "Този имейл вече съществува!";
    }


    if (!$errors_signup) {
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
    if ( $errors_signup )
        $_SESSION['errors_signup'] = $errors_signup;

      echo "<script>document.location.href='../../index.php';</script>";
        exit;
?>
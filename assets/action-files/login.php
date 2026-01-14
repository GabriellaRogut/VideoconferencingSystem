<?php
include("../../includes/connection.php");

if (isset($_POST['login'])) {
    // $username_input = trim($_POST['username']);
    $email_input = trim($_POST['email']);
    $password_input = $_POST['password'];

    $_SESSION['errors_login'] = [];

    if (!$email_input || !$password_input) {
        $_SESSION['errors_login'][] = "Попълнете всички полета!";
    }
    

    if ( !$_SESSION['errors_login'] ) {
        $stmt = $connection->prepare("
            SELECT * 
            FROM users 
            WHERE email = ?
        ");
        $stmt->execute([$email_input]);
        $user = $stmt->fetch();

        if ($user && password_verify($password_input, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            // $_SESSION['username'] = $user['username'];

            echo "<script>document.location.href='../../account.php';</script>";
            exit;
        } else {
            $_SESSION['errors_login'][] = "Невалиден вход!";
        }
    }
 

}

         echo "<script>document.location.href='../../index.php';</script>";
          exit;
?>
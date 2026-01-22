<?php
    include_once("../../includes/connection.php");

    if (isset($_POST['login'])) {
        $email_input = trim($_POST['email']);
        $password_input = $_POST['password'];

        $errors_login = [];

        if (!$email_input || !$password_input) {
            $_SESSION['errors_login'][] = "Попълнете всички полета!";
        }
        

        if ( !$errors_login ) {
            $stmt = $connection->prepare("
                SELECT * 
                FROM users 
                WHERE email = ?
            ");
            $stmt->execute([$email_input]);
            $user = $stmt->fetch();

            if ($user && password_verify($password_input, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                
                echo "<script>document.location.href='../../account.php';</script>";
                exit;
            } else {
                $errors_login[] = "Невалиден вход!";
            }
        }
    

    }

    if( $errors_login ) {
        $_SESSION['errors_login'] = $errors_login;
    }
    
    echo "<script>document.location.href='../../index.php';</script>";
    exit;
?>
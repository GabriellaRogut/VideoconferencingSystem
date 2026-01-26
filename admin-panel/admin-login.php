<?php
    include_once("../includes/connection.php");

    if (isset($_POST['admin_login'])) {
        $email_input = trim($_POST['admin_email']);
        $password_input = $_POST['admin_password'];

        $errors_admin = [];

        if (!$email_input || !$password_input) {
            $_SESSION['errors_admin'][] = "Попълнете всички полета!";
        }

        if (!$errors_admin) {
            $stmt = $connection->prepare("
                SELECT * 
                FROM users 
                WHERE email = ? AND role = 'admin'
            ");
            $stmt->execute([$email_input]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password_input, $admin['password_hash'])) {
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['user_id'] = $admin['id'];
                $_SESSION['admin_welcome'] = true;

                echo "<script>document.location.href='admin.php';</script>";
                exit;
            } else {
                $errors_admin[] = "Нямате администраторски достъп!";
            }
        }

        if ($errors_admin) {
            $_SESSION['errors_admin'] = $errors_admin;
        }

        echo "<script>document.location.href='../index.php';</script>";
        exit;
    }
?>

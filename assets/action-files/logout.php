<?php
    session_start();
    session_unset();
    session_destroy();

    session_start();
    $_SESSION['logout_success'] = "Успешно излязохте от акаунта.";

    header("Location: ../../index.php");
    exit;
?>

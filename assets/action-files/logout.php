<?php
session_start();

// Set flash message before destroying session data
$_SESSION['logout_success'] = "Успешно излязохте от акаунта.";

// Destroy user-specific session variables, not the whole session
unset($_SESSION['user_id']);


header("Location: ../../index.php");
exit;
?>

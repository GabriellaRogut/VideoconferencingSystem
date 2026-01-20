<?php
session_start();

// Destroy session data
session_unset();
session_destroy();

// Redirect to homepage
header("Location: ../../index.php");

// ADD "LOGOUT SUCCESSFUL"
exit;
?>
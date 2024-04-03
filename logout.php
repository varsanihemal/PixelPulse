<?php
session_start();
session_unset();
session_destroy();

// Set logout success message
$_SESSION['logout_success'] = "Successfully logged out";

// Redirect to login page
header("Location: login.php");
exit;


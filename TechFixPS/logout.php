<?php
// Start the session
session_start();

// Destroy the session to log out the user
session_unset();  // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to the login page or homepage after logout
header("Location: ./index.php"); // Change the path if you want to redirect to a different page
exit();
?>

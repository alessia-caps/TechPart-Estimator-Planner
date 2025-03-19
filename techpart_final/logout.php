<?php
session_start();
session_destroy();
header("Location: index.php"); // Redirects to homepage after logout
exit();
?>

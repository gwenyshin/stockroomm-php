<?php
session_start();
include "config.php";

if (isset($_SESSION['username'])) {
    log_activity($conn, "Logout", "User logged out");
}
session_destroy();
header("Location: login.php");
?>

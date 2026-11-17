<?php
// auth.php - semak user dah login ke belum + connect database
session_start();
if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}
include "config.php";
?>

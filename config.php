<?php
// config.php - database connection (dipanggil oleh semua page)
mysqli_report(MYSQLI_REPORT_OFF);   // supaya error SQL tak crash page (kita handle sendiri)

$db_host = "localhost";
$db_user = "root";      // default XAMPP
$db_pass = "";          // default XAMPP kosong
$db_name = "stockroom_db";

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error() .
        " <br>Run <a href='setup.php'>setup.php</a> dulu untuk create database.");
}
mysqli_set_charset($conn, "utf8mb4");

// AUDIT LOG: simpan setiap aktiviti user dalam table activity_log
function log_activity($conn, $action, $description) {
    $user_id  = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : "NULL";
    $username = isset($_SESSION['username']) ? $_SESSION['username'] : "system";
    $username    = mysqli_real_escape_string($conn, $username);
    $action      = mysqli_real_escape_string($conn, $action);
    $description = mysqli_real_escape_string($conn, $description);
    mysqli_query($conn, "INSERT INTO activity_log (user_id, username, action, description)
                         VALUES ($user_id, '$username', '$action', '$description')");
}
?>

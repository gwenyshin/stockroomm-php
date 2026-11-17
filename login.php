<?php
session_start();
include "config.php";

$error = "";

// LOGIN VALIDATION (semak username & password dalam database)
if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role']      = $user['role'];
        log_activity($conn, "Login", "User logged in");
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Stockroom Inventory System</title>
</head>
<body>

<h1>Stockroom Inventory System</h1>
<p>Final Assessment - SPG 0473 Web Programming</p>

<h2>Login</h2>

<?php if ($error != "") { echo "<p><strong>$error</strong></p>"; } ?>

<form action="login.php" method="post">
  <p>
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" placeholder="Enter your username" required minlength="3">
  </p>

  <p>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" placeholder="Enter your password" required minlength="6">
  </p>

  <p>
    <input type="checkbox" id="remember" name="remember">
    <label for="remember">Remember me</label>
  </p>

  <p>
    <input type="submit" name="login" value="Login">
    <input type="reset" value="Clear">
  </p>
</form>

<p><em>Demo account: username admin, password admin123</em></p>

</body>
</html>

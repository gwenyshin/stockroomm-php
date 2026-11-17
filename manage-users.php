<?php
include "auth.php";

// RESTRICTION: hanya admin
if ($_SESSION['role'] != 'admin') {
    $_SESSION['msg'] = "Access denied. This page is for Admin only.";
    header("Location: dashboard.php");
    exit;
}

// mesej (flash message)
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
$error = "";

// ADD USER (create account)
if (isset($_POST['add_user'])) {
    $username  = trim($_POST['username']);
    $password  = $_POST['password'];
    $full_name = trim($_POST['full_name']);
    $role      = $_POST['role'];
    if ($role != "admin" && $role != "staff") {
        $role = "staff";
    }

    if (strlen($username) < 3 || strlen($password) < 6) {
        $error = "Username needs at least 3 characters and password at least 6 characters.";
    } else {
        $u    = mysqli_real_escape_string($conn, $username);
        $f    = mysqli_real_escape_string($conn, $full_name);
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (username, password, full_name, role)
                VALUES ('$u', '$hash', '$f', '$role')";
        if (mysqli_query($conn, $sql)) {
            log_activity($conn, "Add User", "Created account $username ($role)");
            $_SESSION['msg'] = "User $username added successfully.";
            header("Location: manage-users.php");
            exit;
        } else {
            $error = "Error: " . mysqli_error($conn);
        }
    }
}

// DELETE USER
if (isset($_POST['delete_user'])) {
    $del_id = (int)$_POST['user_id'];

    if ($del_id == $_SESSION['user_id']) {
        $_SESSION['msg'] = "You cannot delete your own account.";
    } else {
        $row = mysqli_fetch_assoc(mysqli_query($conn, "SELECT username FROM users WHERE user_id = $del_id"));
        if ($row && mysqli_query($conn, "DELETE FROM users WHERE user_id = $del_id")) {
            log_activity($conn, "Delete User", "Deleted account " . $row['username']);
            $_SESSION['msg'] = "User " . $row['username'] . " deleted successfully.";
        } else {
            $_SESSION['msg'] = "Delete user failed.";
        }
    }
    header("Location: manage-users.php");
    exit;
}

$users = mysqli_query($conn, "SELECT user_id, username, full_name, role FROM users ORDER BY user_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Manage Users - Stockroom Inventory System</title>
</head>
<body>

<?php include "nav.php"; ?>

<h2>Manage Users</h2>

<?php
if ($msg != "") {
    echo "<p><strong>" . htmlspecialchars($msg) . "</strong></p>";
}
if ($error != "") {
    echo "<p><strong>$error</strong></p>";
}
?>

<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>ID</th>
    <th>Username</th>
    <th>Full Name</th>
    <th>Role</th>
    <th>Actions</th>
  </tr>
  <?php while ($u = mysqli_fetch_assoc($users)) { ?>
  <tr>
    <td><?php echo $u['user_id']; ?></td>
    <td><?php echo $u['username']; ?></td>
    <td><?php echo $u['full_name']; ?></td>
    <td><?php echo $u['role']; ?></td>
    <td>
      <?php if ($u['user_id'] == $_SESSION['user_id']) { ?>
        (you)
      <?php } else { ?>
      <form action="manage-users.php" method="post" onsubmit="return confirm('Delete this user?')">
        <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
        <input type="submit" name="delete_user" value="Delete">
      </form>
      <?php } ?>
    </td>
  </tr>
  <?php } ?>
</table>

<h3>Create New Account</h3>

<form action="manage-users.php" method="post">
  <p>
    <label for="username">Username:</label><br>
    <input type="text" id="username" name="username" placeholder="Enter username" required minlength="3">
  </p>

  <p>
    <label for="password">Password:</label><br>
    <input type="password" id="password" name="password" placeholder="Enter password" required minlength="6">
  </p>

  <p>
    <label for="full_name">Full Name:</label><br>
    <input type="text" id="full_name" name="full_name" placeholder="Enter full name" required>
  </p>

  <p>
    <label for="role">Role:</label><br>
    <select id="role" name="role">
      <option value="staff">Staff</option>
      <option value="admin">Admin</option>
    </select>
  </p>

  <p>
    <input type="submit" name="add_user" value="Create Account">
    <input type="reset" value="Clear Form">
  </p>
</form>

</body>
</html>

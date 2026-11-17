<?php
include "auth.php";

// RESTRICTION: hanya admin
if ($_SESSION['role'] != 'admin') {
    $_SESSION['msg'] = "Access denied. This page is for Admin only.";
    header("Location: dashboard.php");
    exit;
}

// VIEW activity history (100 terkini)
$logs = mysqli_query($conn, "SELECT * FROM activity_log ORDER BY log_id DESC LIMIT 100");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Audit Log - Stockroom Inventory System</title>
</head>
<body>

<?php include "nav.php"; ?>

<h2>Audit Log / Activity History</h2>
<p>Showing the latest 100 activities.</p>

<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>No.</th>
    <th>Date and Time</th>
    <th>User</th>
    <th>Action</th>
    <th>Description</th>
  </tr>
  <?php
  if (!$logs || mysqli_num_rows($logs) == 0) {
      echo "<tr><td colspan=\"5\">No activity recorded.</td></tr>";
  } else {
      while ($l = mysqli_fetch_assoc($logs)) {
  ?>
  <tr>
    <td><?php echo $l['log_id']; ?></td>
    <td><?php echo $l['log_time']; ?></td>
    <td><?php echo htmlspecialchars($l['username']); ?></td>
    <td><?php echo htmlspecialchars($l['action']); ?></td>
    <td><?php echo htmlspecialchars($l['description']); ?></td>
  </tr>
  <?php } } ?>
</table>

</body>
</html>

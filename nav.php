<h1>Stockroom Inventory System</h1>
<p>Logged in as: <strong><?php echo $_SESSION['full_name']; ?></strong> (<?php echo $_SESSION['role']; ?>)</p>

<nav>
  <a href="dashboard.php">Dashboard</a> |
  <a href="inventory.php">Inventory List</a> |
  <a href="add-item.php">Add Item</a> |
  <?php if ($_SESSION['role'] == 'admin') { ?>
  <a href="manage-users.php">Manage Users</a> |
  <a href="audit-log.php">Audit Log</a> |
  <?php } ?>
  <a href="logout.php">Logout</a>
</nav>
<hr>

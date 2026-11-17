<?php
include "auth.php";

// mesej (flash message) dari page lain
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// CALCULATION: kira jumlah guna SQL
$sql = "SELECT COUNT(*) AS total_items,
               SUM(status = 'In Stock') AS in_stock,
               SUM(status = 'Low Stock') AS low_stock,
               SUM(status = 'Out of Stock') AS out_stock,
               SUM(quantity * unit_price) AS total_value
        FROM items";
$d = mysqli_fetch_assoc(mysqli_query($conn, $sql));

// ambil item pertama untuk Quick Links sample
$first = mysqli_fetch_assoc(mysqli_query($conn, "SELECT MIN(item_id) AS id FROM items"));
$sample_id = $first['id'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - Stockroom Inventory System</title>
</head>
<body>

<?php include "nav.php"; ?>

<h2>Dashboard</h2>
<p>Welcome back, <?php echo $_SESSION['full_name']; ?>. Here is a quick summary of your warehouse stock.</p>

<?php
if ($msg != "") {
    echo "<p><strong>" . htmlspecialchars($msg) . "</strong></p>";
}
?>

<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>Total Items</th>
    <th>In Stock</th>
    <th>Low Stock</th>
    <th>Out of Stock</th>
    <th>Total Stock Value (RM)</th>
  </tr>
  <tr>
    <td><?php echo $d['total_items']; ?></td>
    <td><?php echo $d['in_stock'] + 0; ?></td>
    <td><?php echo $d['low_stock'] + 0; ?></td>
    <td><?php echo $d['out_stock'] + 0; ?></td>
    <td><?php echo number_format($d['total_value'], 2); ?></td>
  </tr>
</table>

<h3>Quick Links</h3>
<ul>
  <li><form action="inventory.php" method="get">
      <input type="submit" value="View Inventory List">
    </form></li>
  <li><form action="add-item.php" method="get">
      <input type="submit" value="Add New Item">
    </form></li>
  <?php if ($sample_id) { ?>
  <li>
    <form action="view-item.php" method="post">
      <input type="hidden" name="id" value="<?php echo $sample_id; ?>">
      <input type="submit" value="View Item Sample">
    </form>
  </li>
  <li>
    <form action="edit-item.php" method="post">
      <input type="hidden" name="id" value="<?php echo $sample_id; ?>">
      <input type="submit" value="Edit Item Sample">
    </form>
  </li>
  <?php } ?>
  <?php if ($_SESSION['role'] == 'admin') { ?>
  <li><form action="manage-users.php" method="get">
      <input type="submit" value="Manage Users (Admin only)">
    </form></li>
  <li><form action="audit-log.php" method="get">
      <input type="submit" value="View Audit Log (Admin only)">
    </form></li>
  <?php } ?>
</ul>

</body>
</html>

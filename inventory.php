<?php
include "auth.php";

// mesej (flash message) dari page lain
$msg = "";
if (isset($_SESSION['msg'])) {
    $msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}

// SEARCH (guna POST)
$search_raw = isset($_POST['search']) ? $_POST['search'] : "";
$search = mysqli_real_escape_string($conn, $search_raw);
$cat = isset($_POST['category']) ? (int)$_POST['category'] : 0;

// VIEW DATA
$sql = "SELECT items.*, categories.category_name
        FROM items
        JOIN categories ON items.category_id = categories.category_id
        WHERE 1=1";
if ($search != "") {
    $sql .= " AND (items.item_name LIKE '%$search%' OR items.sku LIKE '%$search%')";
}
if ($cat > 0) {
    $sql .= " AND items.category_id = $cat";
}
$sql .= " ORDER BY items.item_id";
$result = mysqli_query($conn, $sql);

$categories = mysqli_query($conn, "SELECT * FROM categories");

$grand_total = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Inventory List - Stockroom Inventory System</title>
</head>
<body>

<?php include "nav.php"; ?>

<h2>All Inventory Items</h2>

<?php
if ($msg != "") {
    echo "<p><strong>" . htmlspecialchars($msg) . "</strong></p>";
}
?>

<form action="inventory.php" method="post">
  <label for="search">Search item:</label>
  <input type="search" id="search" name="search" placeholder="Type item name or SKU" value="<?php echo htmlspecialchars($search_raw); ?>">

  <label for="category">Category:</label>
  <select id="category" name="category">
    <option value="0">All Categories</option>
    <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
    <option value="<?php echo $c['category_id']; ?>" <?php if ($cat == $c['category_id']) echo "selected"; ?>><?php echo $c['category_name']; ?></option>
    <?php } ?>
  </select>

  <input type="submit" value="Search">
  <a href="inventory.php">Clear</a>
</form>

<br>

<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>SKU</th>
    <th>Item Name</th>
    <th>Category</th>
    <th>Quantity</th>
    <th>Price (RM)</th>
    <th>Total Value (RM)</th>
    <th>Status</th>
    <th>Actions</th>
  </tr>
  <?php
  if (mysqli_num_rows($result) == 0) {
      echo "<tr><td colspan=\"8\">No items found.</td></tr>";
  }
  while ($row = mysqli_fetch_assoc($result)) {
      // CALCULATION: total value = quantity x price
      $total = $row['quantity'] * $row['unit_price'];
      $grand_total = $grand_total + $total;
  ?>
  <tr>
    <td><?php echo $row['sku']; ?></td>
    <td><?php echo $row['item_name']; ?></td>
    <td><?php echo $row['category_name']; ?></td>
    <td><?php echo $row['quantity']; ?></td>
    <td><?php echo number_format($row['unit_price'], 2); ?></td>
    <td><?php echo number_format($total, 2); ?></td>
    <td><?php echo $row['status']; ?></td>
    <td>
      <form action="view-item.php" method="post">
        <input type="hidden" name="id" value="<?php echo $row['item_id']; ?>">
        <input type="submit" value="View">
      </form>
      <form action="edit-item.php" method="post">
        <input type="hidden" name="id" value="<?php echo $row['item_id']; ?>">
        <input type="submit" value="Edit">
      </form>
      <?php if ($_SESSION['role'] == 'admin') { ?>
      <form action="delete-item.php" method="post" onsubmit="return confirm('Delete this item?')">
        <input type="hidden" name="id" value="<?php echo $row['item_id']; ?>">
        <input type="submit" value="Delete">
      </form>
      <?php } ?>
    </td>
  </tr>
  <?php } ?>
  <tr>
    <td colspan="5"><strong>Grand Total</strong></td>
    <td colspan="3"><strong>RM <?php echo number_format($grand_total, 2); ?></strong></td>
  </tr>
</table>

<br>
<a href="add-item.php">+ Add New Item</a>

</body>
</html>

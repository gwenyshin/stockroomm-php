<?php
include "auth.php";

// id datang dari POST (butang View). Lepas Update, id datang dari session.
$id = 0;
if (isset($_POST['id'])) {
    $id = (int)$_POST['id'];
} elseif (isset($_SESSION['view_id'])) {
    $id = (int)$_SESSION['view_id'];
}

$sql = "SELECT items.*, categories.category_name, suppliers.supplier_name
        FROM items
        JOIN categories ON items.category_id = categories.category_id
        JOIN suppliers ON items.supplier_id = suppliers.supplier_id
        WHERE items.item_id = $id";
$item = mysqli_fetch_assoc(mysqli_query($conn, $sql));

if (!$item) {
    $_SESSION['msg'] = "Item not found.";
    header("Location: inventory.php");
    exit;
}

// CALCULATION
$total = $item['quantity'] * $item['unit_price'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Item - Stockroom Inventory System</title>
</head>
<body>

<?php include "nav.php"; ?>

<h2><?php echo $item['item_name']; ?></h2>

<table border="1" cellpadding="8" cellspacing="0">
  <tr>
    <th>SKU Code</th>
    <td><?php echo $item['sku']; ?></td>
  </tr>
  <tr>
    <th>Category</th>
    <td><?php echo $item['category_name']; ?></td>
  </tr>
  <tr>
    <th>Supplier</th>
    <td><?php echo $item['supplier_name']; ?></td>
  </tr>
  <tr>
    <th>Description</th>
    <td><?php echo $item['description']; ?></td>
  </tr>
  <tr>
    <th>Quantity</th>
    <td><?php echo $item['quantity']; ?></td>
  </tr>
  <tr>
    <th>Unit Price</th>
    <td>RM <?php echo number_format($item['unit_price'], 2); ?></td>
  </tr>
  <tr>
    <th>Total Value</th>
    <td>RM <?php echo number_format($total, 2); ?></td>
  </tr>
  <tr>
    <th>Status</th>
    <td><?php echo $item['status']; ?></td>
  </tr>
  <tr>
    <th>Date Added</th>
    <td><?php echo date("d F Y", strtotime($item['date_added'])); ?></td>
  </tr>
  <tr>
    <th>Fragile Item</th>
    <td><?php echo $item['fragile']; ?></td>
  </tr>
</table>

<br>
<form action="edit-item.php" method="post">
  <input type="hidden" name="id" value="<?php echo $item['item_id']; ?>">
  <input type="submit" value="Edit This Item">
</form>
<a href="inventory.php">Back to Inventory List</a>

</body>
</html>

<?php
include "auth.php";

$error = "";

// SUBMIT VALUE (INSERT)
if (isset($_POST['save'])) {
    $item_name   = mysqli_real_escape_string($conn, $_POST['itemName']);
    $sku         = mysqli_real_escape_string($conn, $_POST['sku']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $category_id = (int)$_POST['category'];
    $supplier_id = (int)$_POST['supplier'];
    $quantity    = (int)$_POST['quantity'];
    $unit_price  = (float)$_POST['price'];
    $date_added  = $_POST['dateAdded'];
    $status      = $_POST['status'];
    $fragile     = isset($_POST['fragile']) ? "Yes" : "No";

    $sql = "INSERT INTO items (sku, item_name, description, category_id, supplier_id, quantity, unit_price, status, fragile, date_added)
            VALUES ('$sku', '$item_name', '$description', $category_id, $supplier_id, $quantity, $unit_price, '$status', '$fragile', '$date_added')";

    if (mysqli_query($conn, $sql)) {
        log_activity($conn, "Add Item", "Added item " . $_POST['sku'] . " - " . $_POST['itemName']);
        $_SESSION['msg'] = "Item added successfully.";
        header("Location: inventory.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

$categories = mysqli_query($conn, "SELECT * FROM categories");
$suppliers  = mysqli_query($conn, "SELECT * FROM suppliers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Item - Stockroom Inventory System</title>
</head>
<body>

<?php include "nav.php"; ?>

<h2>Add New Inventory Item</h2>

<?php if ($error != "") { echo "<p><strong>$error</strong></p>"; } ?>

<form action="add-item.php" method="post">

  <p>
    <label for="itemName">Item Name:</label><br>
    <input type="text" id="itemName" name="itemName" placeholder="e.g. Wireless Barcode Scanner" required>
  </p>

  <p>
    <label for="sku">SKU Code:</label><br>
    <input type="text" id="sku" name="sku" placeholder="e.g. STK-1006" required>
  </p>

  <p>
    <label for="description">Description:</label><br>
    <textarea id="description" name="description" rows="4" cols="40" placeholder="Short description of the item"></textarea>
  </p>

  <p>
    <label for="category2">Category:</label><br>
    <select id="category2" name="category" required>
      <option value="">-- Select category --</option>
      <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
      <option value="<?php echo $c['category_id']; ?>"><?php echo $c['category_name']; ?></option>
      <?php } ?>
    </select>
  </p>

  <p>
    <label for="supplier">Supplier:</label><br>
    <select id="supplier" name="supplier" required>
      <option value="">-- Select supplier --</option>
      <?php while ($s = mysqli_fetch_assoc($suppliers)) { ?>
      <option value="<?php echo $s['supplier_id']; ?>"><?php echo $s['supplier_name']; ?></option>
      <?php } ?>
    </select>
  </p>

  <p>
    <label for="quantity">Quantity:</label><br>
    <input type="number" id="quantity" name="quantity" min="0" step="1" placeholder="0" required>
  </p>

  <p>
    <label for="price">Unit Price (RM):</label><br>
    <input type="number" id="price" name="price" min="0" step="0.01" placeholder="0.00" required>
  </p>

  <p>
    <label for="dateAdded">Date Added:</label><br>
    <input type="date" id="dateAdded" name="dateAdded" required>
  </p>

  <p>Stock Status:</p>
  <p>
    <input type="radio" id="statusIn" name="status" value="In Stock" checked>
    <label for="statusIn">In Stock</label>

    <input type="radio" id="statusLow" name="status" value="Low Stock">
    <label for="statusLow">Low Stock</label>

    <input type="radio" id="statusOut" name="status" value="Out of Stock">
    <label for="statusOut">Out of Stock</label>
  </p>

  <p>
    <input type="checkbox" id="fragile" name="fragile" value="yes">
    <label for="fragile">Fragile item</label>
  </p>

  <p>
    <input type="submit" name="save" value="Save Item">
    <input type="reset" value="Clear Form">
  </p>
</form>

<p><a href="inventory.php">Cancel and back to Inventory List</a></p>

</body>
</html>

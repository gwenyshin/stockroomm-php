<?php
include "auth.php";

// id datang dari POST (hidden field)
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$error = "";

// EDIT DATA (UPDATE)
if (isset($_POST['update'])) {
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

    $sql = "UPDATE items SET
              sku = '$sku',
              item_name = '$item_name',
              description = '$description',
              category_id = $category_id,
              supplier_id = $supplier_id,
              quantity = $quantity,
              unit_price = $unit_price,
              status = '$status',
              fragile = '$fragile',
              date_added = '$date_added'
            WHERE item_id = $id";

    if (mysqli_query($conn, $sql)) {
        log_activity($conn, "Edit Item", "Updated item " . $_POST['sku'] . " - " . $_POST['itemName']);
        $_SESSION['view_id'] = $id;
        header("Location: view-item.php");
        exit;
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}

// ambil data semasa untuk isi form
$item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM items WHERE item_id = $id"));
if (!$item) {
    $_SESSION['msg'] = "Item not found.";
    header("Location: inventory.php");
    exit;
}

$categories = mysqli_query($conn, "SELECT * FROM categories");
$suppliers  = mysqli_query($conn, "SELECT * FROM suppliers");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Item - Stockroom Inventory System</title>
</head>
<body>

<?php include "nav.php"; ?>

<h2>Edit Item: <?php echo $item['item_name']; ?></h2>

<?php if ($error != "") { echo "<p><strong>$error</strong></p>"; } ?>

<form action="edit-item.php" method="post">
  <input type="hidden" name="id" value="<?php echo $id; ?>">

  <p>
    <label for="eItemName">Item Name:</label><br>
    <input type="text" id="eItemName" name="itemName" value="<?php echo htmlspecialchars($item['item_name']); ?>" required>
  </p>

  <p>
    <label for="eSku">SKU Code:</label><br>
    <input type="text" id="eSku" name="sku" value="<?php echo htmlspecialchars($item['sku']); ?>" required>
  </p>

  <p>
    <label for="eDescription">Description:</label><br>
    <textarea id="eDescription" name="description" rows="4" cols="40"><?php echo htmlspecialchars($item['description']); ?></textarea>
  </p>

  <p>
    <label for="eCategory">Category:</label><br>
    <select id="eCategory" name="category" required>
      <?php while ($c = mysqli_fetch_assoc($categories)) { ?>
      <option value="<?php echo $c['category_id']; ?>" <?php if ($item['category_id'] == $c['category_id']) echo "selected"; ?>><?php echo $c['category_name']; ?></option>
      <?php } ?>
    </select>
  </p>

  <p>
    <label for="eSupplier">Supplier:</label><br>
    <select id="eSupplier" name="supplier" required>
      <?php while ($s = mysqli_fetch_assoc($suppliers)) { ?>
      <option value="<?php echo $s['supplier_id']; ?>" <?php if ($item['supplier_id'] == $s['supplier_id']) echo "selected"; ?>><?php echo $s['supplier_name']; ?></option>
      <?php } ?>
    </select>
  </p>

  <p>
    <label for="eQuantity">Quantity:</label><br>
    <input type="number" id="eQuantity" name="quantity" min="0" step="1" value="<?php echo $item['quantity']; ?>" required>
  </p>

  <p>
    <label for="ePrice">Unit Price (RM):</label><br>
    <input type="number" id="ePrice" name="price" min="0" step="0.01" value="<?php echo $item['unit_price']; ?>" required>
  </p>

  <p>
    <label for="eDateAdded">Date Added:</label><br>
    <input type="date" id="eDateAdded" name="dateAdded" value="<?php echo $item['date_added']; ?>" required>
  </p>

  <p>Stock Status:</p>
  <p>
    <input type="radio" id="eStatusIn" name="status" value="In Stock" <?php if ($item['status'] == "In Stock") echo "checked"; ?>>
    <label for="eStatusIn">In Stock</label>

    <input type="radio" id="eStatusLow" name="status" value="Low Stock" <?php if ($item['status'] == "Low Stock") echo "checked"; ?>>
    <label for="eStatusLow">Low Stock</label>

    <input type="radio" id="eStatusOut" name="status" value="Out of Stock" <?php if ($item['status'] == "Out of Stock") echo "checked"; ?>>
    <label for="eStatusOut">Out of Stock</label>
  </p>

  <p>
    <input type="checkbox" id="eFragile" name="fragile" value="yes" <?php if ($item['fragile'] == "Yes") echo "checked"; ?>>
    <label for="eFragile">Fragile item</label>
  </p>

  <p>
    <input type="submit" name="update" value="Update Item">
    <input type="reset" value="Reset Changes">
  </p>
</form>

<p><a href="inventory.php">Cancel and back to Inventory List</a></p>

</body>
</html>

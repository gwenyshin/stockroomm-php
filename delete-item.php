<?php
include "auth.php";

// RESTRICTION: hanya admin boleh delete
if ($_SESSION['role'] != 'admin') {
    $_SESSION['msg'] = "Access denied. Only Admin can delete items.";
    header("Location: inventory.php");
    exit;
}

// DELETE DATA (id datang dari POST)
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
$item = mysqli_fetch_assoc(mysqli_query($conn, "SELECT sku, item_name FROM items WHERE item_id = $id"));

if ($item && mysqli_query($conn, "DELETE FROM items WHERE item_id = $id")) {
    log_activity($conn, "Delete Item", "Deleted item " . $item['sku'] . " - " . $item['item_name']);
    $_SESSION['msg'] = "Item deleted successfully.";
} else {
    $_SESSION['msg'] = "Delete failed.";
}
header("Location: inventory.php");
exit;
?>

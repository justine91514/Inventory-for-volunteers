<?php
include 'includes/db.php';

$id = $_POST['id'];
$new_name = $_POST['name'];
$new_total = max(0, (int) $_POST['total']);

// get old item
$item = $conn->query("SELECT * FROM inventory WHERE id = $id")->fetch_assoc();

$old_name = $item['item_name'];
$borrowed = $item['total_qty'] - $item['available_qty'];

// ❌ safety check
if ($new_total < $borrowed) {
    echo "Cannot reduce below borrowed items ($borrowed)";
    exit;
}

$new_available = $new_total - $borrowed;

// ===== UPDATE INVENTORY =====
$conn->query("UPDATE inventory SET 
    item_name = '$new_name',
    total_qty = $new_total,
    available_qty = $new_available
    WHERE id = $id
");

// ===== UPDATE BORROW RECORDS =====
$conn->query("UPDATE borrow_records 
    SET item_name = '$new_name'
    WHERE item_name = '$old_name'
");

// OPTIONAL: update if you also use attendance logs with item reference
// (skip unless needed)

echo "success";
?>
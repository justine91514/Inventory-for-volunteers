<?php
include 'includes/db.php';

$id = $_POST['id'];
$newName = $_POST['name'];
$total = $_POST['total'];

// get old data
$item = $conn->query("SELECT * FROM inventory WHERE id = $id")->fetch_assoc();

$oldName = $item['item_name'];
$borrowed = $item['total_qty'] - $item['available_qty'];

// safety check
if ($total < $borrowed) {
    exit("Cannot set below borrowed items ($borrowed)");
}

$newAvailable = $total - $borrowed;

// 1. update inventory
$conn->query("
    UPDATE inventory SET
    item_name = '$newName',
    total_qty = $total,
    available_qty = $newAvailable
    WHERE id = $id
");

// 2. sync borrowed records (IMPORTANT)
$conn->query("
    UPDATE borrow_records 
    SET item_name = '$newName'
    WHERE item_name = '$oldName'
");

echo "success";
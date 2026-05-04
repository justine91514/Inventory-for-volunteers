<?php
include 'includes/db.php';

$id = $_POST['id'];
$new_total = $_POST['total'];

// get item
$item = $conn->query("SELECT * FROM inventory WHERE id = $id")->fetch_assoc();

$borrowed = $item['total_qty'] - $item['available_qty'];

// ❌ safety check
if ($new_total < $borrowed) {
    echo "Cannot reduce below borrowed items ($borrowed)";
    exit;
}

$new_available = $new_total - $borrowed;

$conn->query("UPDATE inventory SET 
    total_qty = $new_total,
    available_qty = $new_available
    WHERE id = $id");

echo "success";
?>
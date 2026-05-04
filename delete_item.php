<?php
include 'includes/db.php';

$id = $_GET['id'];

$item = $conn->query("SELECT * FROM inventory WHERE id = $id")->fetch_assoc();

$borrowed = $item['total_qty'] - $item['available_qty'];

if ($borrowed > 0) {
    echo "Cannot delete. Item is currently borrowed.";
    exit;
}

$conn->query("DELETE FROM inventory WHERE id = $id");

echo "success";
?>
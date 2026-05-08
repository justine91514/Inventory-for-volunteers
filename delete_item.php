<?php
include 'includes/db.php';
include 'log_helper.php';
session_start();

$id = $_GET['id'];

// GET ITEM DATA FIRST
$get = $conn->query("
    SELECT item_name, total_qty 
    FROM inventory 
    WHERE id = '$id'
");

$row = $get->fetch_assoc();

$item = $row['item_name'] ?? 'Unknown Item';
$qty = $row['total_qty'] ?? 0;

// DELETE QUERY
$sql = "DELETE FROM inventory WHERE id='$id'";

if ($conn->query($sql)) {

    addLog(
        $conn,
        "Delete",
        "Deleted $qty x $item"
    );

    echo "success";

} else {

    echo "error";

}
?>
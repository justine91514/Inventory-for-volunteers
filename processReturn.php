<?php
include 'includes/db.php';

$id = (int)$_POST['id'];
$qty = (int)$_POST['qty'];

// GET BORROW DATA
$data = $conn->query("SELECT * FROM borrow_records WHERE id = $id")->fetch_assoc();

if (!$data) {
    echo "Invalid record";
    exit;
}

if ($data['status'] === 'returned') {
    echo "Already returned";
    exit;
}

$item = $data['item_name'];
$borrowed_qty = (int)$data['quantity'];

// VALIDATION
if ($qty <= 0 || $qty > $borrowed_qty) {
    echo "Invalid quantity";
    exit;
}

// UPDATE INVENTORY (ONLY RETURN SELECTED QTY)
$conn->query("
    UPDATE inventory 
    SET available_qty = available_qty + $qty 
    WHERE item_name = '$item'
");

// IF FULL RETURN → MARK AS RETURNED
if ($qty == $borrowed_qty) {

    $conn->query("
        UPDATE borrow_records 
        SET status = 'returned'
        WHERE id = $id
    ");

} else {

    // PARTIAL RETURN → reduce remaining qty
    $conn->query("
        UPDATE borrow_records 
        SET quantity = quantity - $qty
        WHERE id = $id
    ");
}

echo "success";
?>
<?php
include 'includes/db.php';
include 'log_helper.php';
session_start();

$id = $_POST['id'];
$new_name = $_POST['name'];
$new_total = max(0, (int) $_POST['total']);

// get old item
$item = $conn->query("
    SELECT * 
    FROM inventory 
    WHERE id = $id
")->fetch_assoc();

$old_name = $item['item_name'];
$old_total = $item['total_qty'];

$borrowed = $item['total_qty'] - $item['available_qty'];

// ❌ safety check
if ($new_total < $borrowed) {
    echo "Cannot reduce below borrowed items ($borrowed)";
    exit;
}

$new_available = $new_total - $borrowed;

// ===== UPDATE INVENTORY =====
$sql = "
    UPDATE inventory SET 
    item_name = '$new_name',
    total_qty = $new_total,
    available_qty = $new_available
    WHERE id = $id
";

if ($conn->query($sql)) {

    // ===== UPDATE BORROW RECORDS =====
    $conn->query("
        UPDATE borrow_records 
        SET item_name = '$new_name'
        WHERE item_name = '$old_name'
    ");

    // ===== LOG =====
    addLog(
        $conn,
        "Inventory Updated",
        "Updated item $old_name ($old_total) to $new_name ($new_total)"
    );

    echo "success";

} else {

    echo "error";

}
?>
<!-- <?php
include 'includes/db.php';

if (!isset($_POST['old_name']) || !isset($_POST['new_name'])) {
    exit("invalid request");
}

$old = $conn->real_escape_string($_POST['old_name']);
$new = $conn->real_escape_string($_POST['new_name']);

$conn->query("
    UPDATE attendance 
    SET volunteer_name = '$new' 
    WHERE volunteer_name = '$old'
");

$conn->query("
    UPDATE borrow_records 
    SET borrower_name = '$new' 
    WHERE borrower_name = '$old'
");

echo "success";
?> -->

this file is not used
look for the "update_inventory.php" file
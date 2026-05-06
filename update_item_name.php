<?php
include 'includes/db.php';

$old = $_POST['old_name'];
$new = $_POST['new_name'];

// sanitize (important)
$old = $conn->real_escape_string($old);
$new = $conn->real_escape_string($new);

// update attendance
$conn->query("
    UPDATE attendance 
    SET volunteer_name = '$new' 
    WHERE volunteer_name = '$old'
");

// update borrow_records
$conn->query("
    UPDATE borrow_records 
    SET borrower_name = '$new' 
    WHERE borrower_name = '$old'
");

echo "success";
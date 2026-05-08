<?php
include 'includes/db.php';
include 'log_helper.php';

session_start();

$old = $_POST['old_name'];
$new = $_POST['new_name'];

// sanitize
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

// ===== LOG =====
$admin = $_SESSION['username'] ?? 'System';

addLog(
    $conn,
    "Rename",
    "$admin renamed $old to $new"
);

echo "success";
?>
<?php
include 'includes/db.php';

$id = $_POST['id'] ?? '';

if (!$id) {
    echo "Invalid user";
    exit;
}

$conn->query("
    DELETE FROM users
    WHERE id='$id'
");

echo "success";
?>
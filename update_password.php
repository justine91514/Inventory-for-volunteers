<?php
include 'includes/db.php';

$id = $_POST['id'];
$new = $_POST['new'];

$user = $conn->query("SELECT * FROM users WHERE id='$id'")->fetch_assoc();

if (!$user) exit("User not found");

// ❌ prevent same password
if ($user['password'] === $new) {
    exit("New password cannot be same as old password");
}

$conn->query("
    UPDATE users 
    SET password='$new',
        reset_token=NULL,
        reset_expiry=NULL
    WHERE id='$id'
");

echo "success";
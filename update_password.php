<?php
include 'includes/db.php';
include 'log_helper.php';

session_start();

$id = $_POST['id'];
$new = $_POST['new'];

// GET USER
$user = $conn->query("
    SELECT * 
    FROM users 
    WHERE id='$id'
")->fetch_assoc();

if (!$user) {
    exit("User not found");
}

$targetUser = $user['username'];

// ❌ prevent same password
if ($user['password'] === $new) {
    exit("New password cannot be same as old password");
}

// UPDATE PASSWORD
$conn->query("
    UPDATE users 
    SET password='$new',
        reset_token=NULL,
        reset_expiry=NULL
    WHERE id='$id'
");

// ===== LOG =====
$admin = $_SESSION['username'] ?? 'System';

addLog(
    $conn,
    "Password Changed",
    "$admin changed password of $targetUser"
);

echo "success";
?>
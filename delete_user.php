<?php
include 'includes/db.php';
include 'log_helper.php';
session_start();

$id = $_POST['id'];

// GET USER FIRST
$user = $conn->query("
    SELECT * FROM users WHERE id = $id
")->fetch_assoc();

$username = $user['username'];

$conn->query("
    DELETE FROM users WHERE id = $id
");

// ===== LOG =====
$admin = $_SESSION['username'];

addLog(
    $conn,
    "User Deleted",
    "$admin deleted user $username"
);

echo "success";
?>
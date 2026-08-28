<?php
include 'includes/db.php';
include 'log_helper.php';
session_start();

$id = $_POST['id'];
$username = trim($_POST['username']);

// GET OLD USER
$old = $conn->query("
    SELECT * FROM users WHERE id = $id
")->fetch_assoc();

$oldUsername = $old['username'];

// UPDATE
$conn->query("
    UPDATE users
    SET username = '$username',
    username_updated_at = NOW()
    WHERE id = $id
");

// UPDATE SESSION IF CURRENT USER CHANGED THEIR OWN USERNAME
if ($_SESSION['username'] === $oldUsername) {
    $_SESSION['username'] = $username;
}
// ===== LOG =====
$admin = $_SESSION['username'];

addLog(
    $conn,
    "User Updated",
    "$admin changed username from $oldUsername to $username"
);

echo "success";
?>

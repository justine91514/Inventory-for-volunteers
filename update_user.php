<?php
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');

$id = $_POST['id'] ?? '';
$username = trim($_POST['username'] ?? '');

if (!$id || !$username) {
    echo "Fill all fields";
    exit;
}

$date = date("Y-m-d H:i:s");

$conn->query("
    UPDATE users
    SET username='$username',
        username_updated_at='$date'
    WHERE id='$id'
");

echo "success";
?>
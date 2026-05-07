<?php
include 'includes/db.php';

if (!isset($_POST['id'], $_POST['new'])) {
    exit("invalid request");
}

$id = $_POST['id'];
$new = $_POST['new'];

$conn->query("
    UPDATE users 
    SET password = '$new'
    WHERE id = '$id'
");

echo "success";
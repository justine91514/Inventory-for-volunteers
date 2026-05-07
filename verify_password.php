<?php
include 'includes/db.php';

if (!isset($_POST['id'], $_POST['current'])) {
    exit("invalid request");
}

$id = $_POST['id'];
$current = $_POST['current'];

$result = $conn->query("SELECT password FROM users WHERE id = '$id'");
$user = $result->fetch_assoc();

if (!$user) {
    exit("User not found");
}

if ($user['password'] !== $current) {
    exit("Incorrect password");
}

echo "success";
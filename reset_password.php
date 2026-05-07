<?php
include 'includes/db.php';

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo "Fill all fields";
    exit;
}

// check existing email
$check = $conn->query("
    SELECT * FROM users
    WHERE email='$email'
");

if ($check->num_rows == 0) {

    echo "Email not found";

} else {

    $conn->query("
        UPDATE users
        SET password='$password'
        WHERE email='$email'
    ");

    echo "success";
}
?>
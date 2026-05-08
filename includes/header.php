<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory System</title>
    <link rel="stylesheet" href="assets/css/styles.css">
</head>

<?php
include 'db.php';

function addLog($conn, $type, $desc)
{
    $type = $conn->real_escape_string($type);
    $desc = $conn->real_escape_string($desc);

    $performedBy = $_SESSION['username'] ?? 'System';

    $performedBy = $conn->real_escape_string($performedBy);

    $conn->query("
        INSERT INTO reports 
        (action_type, description, performed_by)
        VALUES ('$type', '$desc', '$performedBy')
    ");
}
?>

<?php














// session_start();

// if (!isset($_SESSION['user'])) {
//     header("Location: login.php");
//     exit;
// }
?>

<body>

    <button id="toggleBtn">☰</button>
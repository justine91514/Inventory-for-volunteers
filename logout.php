<?php
session_start();

include 'includes/db.php';
include 'log_helper.php';

$username = $_SESSION['username'] ?? 'Unknown User';

// ===== LOG =====
addLog(
    $conn,
    "Logout",
    "$username logged out"
);

// DESTROY SESSION
session_destroy();

header("Location: login.php");
exit;
?>
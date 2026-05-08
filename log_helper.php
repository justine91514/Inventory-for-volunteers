<?php

function addLog($conn, $type, $desc, $transactionId = null)
{
    $type = $conn->real_escape_string($type);
    $desc = $conn->real_escape_string($desc);

    $performedBy = $_SESSION['username'] ?? 'System';
    $performedBy = $conn->real_escape_string($performedBy);

    $transactionId = $transactionId
        ? "'" . $conn->real_escape_string($transactionId) . "'"
        : "NULL";

    $conn->query("
        INSERT INTO reports 
        (action_type, description, performed_by, transaction_id)
        VALUES (
            '$type',
            '$desc',
            '$performedBy',
            $transactionId
        )
    ");
}
?>
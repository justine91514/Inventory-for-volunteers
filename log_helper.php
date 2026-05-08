<?php

function addLog($conn, $type, $desc)
{
    $type = $conn->real_escape_string($type);
    $desc = $conn->real_escape_string($desc);

    $conn->query("
        INSERT INTO reports (action_type, description)
        VALUES ('$type', '$desc')
    ");
}
?>
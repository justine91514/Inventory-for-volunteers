<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');

if (isset($_POST['time_out'])) {
    $name = $_POST['name'];
    $time = date("Y-m-d H:i:s");

    $sql = "UPDATE attendance 
        SET time_out = '$time' 
        WHERE volunteer_name = '$name' 
        AND time_out IS NULL
        ORDER BY id DESC 
        LIMIT 1";

    $conn->query($sql);
}
?>

<div class="main-content">
    <h1>Time Out</h1>

    <form method="POST">
        <input type="text" name="name" placeholder="Enter Name" required>
        <button type="submit" name="time_out">Time Out</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
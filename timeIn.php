<?php 
include 'includes/header.php'; 
include 'includes/sidebar.php'; 
include 'includes/db.php';

$error = "";
$success = "";

if (isset($_POST['time_in'])) {
    $name = trim($_POST['name']);
    $time = date("Y-m-d H:i:s");

    // ✅ CHECK if already timed in (no timeout yet)
    $check = $conn->query("SELECT * FROM attendance 
                           WHERE volunteer_name = '$name' 
                           AND time_out IS NULL");

    if ($check->num_rows > 0) {
        $error = "User already timed in. Please time out first.";
    } else {
        $sql = "INSERT INTO attendance (volunteer_name, time_in) 
                VALUES ('$name', '$time')";

        if ($conn->query($sql)) {
            $success = "Time In recorded!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<div class="main-content">
    <h1>Time In</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">
        <input type="text" name="name" placeholder="Enter Name" required>
        <button type="submit" name="time_in">Time In</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
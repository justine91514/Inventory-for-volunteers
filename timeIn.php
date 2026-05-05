<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');

$error = "";
$success = "";

$names = $conn->query("SELECT DISTINCT volunteer_name FROM attendance");

if (isset($_POST['time_in'])) {
    $name = trim($_POST['name']);
    $time = date("Y-m-d H:i:s");

    // ✅ CHECK if already timed in (no timeout yet)
    $today = date("Y-m-d");

    $check = $conn->query("SELECT * FROM attendance 
    WHERE volunteer_name = '$name' 
    AND attendance_date = '$today'");

    if ($check->num_rows > 0) {
        $error = "User already timed in. Please time out first.";
    } else {
        $sql = "INSERT INTO attendance (volunteer_name, time_in, attendance_date) 
                VALUES ('$name', '$time', '$today')";

        if ($conn->query($sql)) {
            $success = "Hi " . $name . " " . "your name has been recorded!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>

<div class="main-content">

    <div class="auth-card">

        <div class="auth-header">
            <h2>🟢 Time In</h2>
            <p>Record attendance entry</p>
        </div>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">

            <label>Volunteer Name</label>
            <input list="nameList" name="name" placeholder="Enter name..." required>

            <datalist id="nameList">
                <?php while ($row = $names->fetch_assoc()): ?>
                    <option value="<?= $row['volunteer_name'] ?>">
                    <?php endwhile; ?>
            </datalist>

            <button type="submit" name="time_in">
                ✔ Submit Time In
            </button>

        </form>

    </div>

</div>
<?php include 'includes/footer.php'; ?>
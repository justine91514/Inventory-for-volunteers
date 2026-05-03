<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');

$error = "";
$success = "";

$names = $conn->query("SELECT DISTINCT volunteer_name FROM attendance");
if (isset($_POST['time_out'])) {
    $name = $_POST['name'];
    $time = date("Y-m-d H:i:s");
    $today = date("Y-m-d");

    // ✅ check if may time-in today
    $check = $conn->query("SELECT * FROM attendance 
        WHERE volunteer_name = '$name' 
        AND DATE(time_in) = '$today'");

    if ($check->num_rows == 0) {
        $error = "this person has not timed in yet.";
    } else {

        // ✅ check if already timed out
        $check2 = $conn->query("SELECT * FROM attendance 
            WHERE volunteer_name = '$name' 
            AND DATE(time_in) = '$today'
            AND time_out IS NULL");

        if ($check2->num_rows == 0) {
            $error = $name . " Already timed out.";
        } else {

            // ✅ proceed update
            $sql = "UPDATE attendance 
                SET time_out = '$time' 
                WHERE volunteer_name = '$name' 
                AND DATE(time_in) = '$today'
                AND time_out IS NULL
                ORDER BY id DESC 
                LIMIT 1";

            if ($conn->query($sql)) {
                $success = "Time Out recorded!";
            } else {
                $error = "Error: " . $conn->error;
            }
        }
    }
}
?>

<div class="main-content">
    <h1>Time Out</h1>

    <?php if ($error): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?= $success ?></p>
    <?php endif; ?>

    <form method="POST">

        <!-- ✅ AUTOCOMPLETE INPUT -->
        <input list="nameList" name="name" placeholder="Enter Name" required>

        <datalist id="nameList">
            <?php while ($row = $names->fetch_assoc()): ?>
                <option value="<?= $row['volunteer_name'] ?>">
                <?php endwhile; ?>
        </datalist>

        <button type="submit" name="time_in">Time In</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
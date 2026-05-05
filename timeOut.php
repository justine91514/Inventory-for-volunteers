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

            // proceed update
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

    <div class="auth-card">

        <div class="auth-header">
            <h2>🔴 Time Out</h2>
            <p>Record exit time</p>
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

            <button type="button" onclick="openTimeOutModal()">
                ✔ Submit Time Out
            </button>

        </form>

    </div>

</div>

<div id="timeOutModal" class="modal">
    <div class="modal-box">

        <div class="modal-header" id="timeOutHeader">
            <h3>Time Out Confirmation</h3>
            <span onclick="closeTimeOutModal()">✖</span>
        </div>

        <div class="modal-body">
            <p>Confirm Time Out for:</p>
            <h4 id="timeOutName"></h4>
        </div>

        <div class="modal-footer">
            <button class="btn-confirm" onclick="submitTimeOut()">Confirm</button>
            <button class="btn-cancel" onclick="closeTimeOutModal()">Cancel</button>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>
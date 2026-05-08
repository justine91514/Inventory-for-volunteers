<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');

$error = "";
$success = "";

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

    addLog(
        $conn,
        "Time In",
        "$name timed in"
    );

    $success = "Hi " . $name . " your name has been recorded!";

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
            <input type="text" name="name" placeholder="Enter name..." required autocomplete="off">
            <button type="button" onclick="openTimeInModal()">
                ✔ Submit Time In
            </button>

        </form>

    </div>

</div>

<div id="timeInModal" class="modal">
    <div class="modal-box">

        <div class="modal-header" id="timeInHeader">
            <h3>Time In Confirmation</h3>
            <span onclick="closeTimeInModal()">✖</span>
        </div>

        <div class="modal-body">
            <p>Confirm Time In for:</p>
            <h4 id="timeInName"></h4>
        </div>

        <div class="modal-footer">
            <button class="btn-confirm" onclick="submitTimeIn()">Confirm</button>
            <button class="btn-cancel" onclick="closeTimeInModal()">Cancel</button>
        </div>

    </div>
</div>

<script>
document.querySelector("input[name='name']").addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault(); // stop form auto submit
        openTimeInModal();
    }
});
</script>

<script>
const input = document.querySelector("input[name='name']");

document.addEventListener("click", function (e) {
    if (!e.target.closest("input") && !e.target.closest(".dropdown-list")) {
        input.focus();
    }
});
</script>


<?php include 'includes/footer.php'; ?>
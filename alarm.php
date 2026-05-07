<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

$message = "";

// SAVE TIMER
if (isset($_POST['save_alarm'])) {

    $time = $_POST['timeout_time'];

    $check = $conn->query("SELECT * FROM alarm_settings LIMIT 1");

    if ($check->num_rows > 0) {

        $conn->query("
            UPDATE alarm_settings
            SET timeout_time = '$time'
            WHERE id = 1
        ");

    } else {

        $conn->query("
            INSERT INTO alarm_settings(timeout_time)
            VALUES('$time')
        ");
    }

    $message = "Alarm time updated!";
}

$get = $conn->query("SELECT * FROM alarm_settings LIMIT 1");
$row = $get->fetch_assoc();

$currentTime = $row['timeout_time'] ?? "18:00";
?>

<div class="main-content">

    <div class="card settings-card">

        <div class="auth-header">
            <h2>⏰ Time Out Alarm</h2>
            <p>Set automatic alert time</p>
        </div>

        <?php if ($message): ?>
            <div class="alert success"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">

            <label>Time Out Reminder</label>

            <input 
                type="time"
                name="timeout_time"
                value="<?= date('H:i', strtotime($currentTime)) ?>"
                required
            >

            <button type="submit" name="save_alarm" class="btn-primary">
                Save Timer
            </button>

        </form>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
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
            <input type="text" id="timeOutInput" name="name" placeholder="Select name..." readonly required
                onclick="toggleDropdown()">
            <div class="dropdown-wrapper">
                <div id="timeOutDropdown" class="dropdown-list">
                    <?php
                    $active = $conn->query("
            SELECT DISTINCT volunteer_name 
            FROM attendance 
            WHERE time_out IS NULL 
            AND attendance_date = CURDATE()
        ");

                    while ($row = $active->fetch_assoc()):
                        ?>
                        <div class="dropdown-item" onclick="selectName('<?= $row['volunteer_name'] ?>')">
                            <?= $row['volunteer_name'] ?>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

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


<script>
    function toggleDropdown() {
        let dd = document.getElementById("timeOutDropdown");
        dd.style.display = dd.style.display === "block" ? "none" : "block";
    }

    function selectName(name) {
        document.getElementById("timeOutInput").value = name;
        document.getElementById("timeOutDropdown").style.display = "none";

        document.getElementById("timeOutName").innerText = name;
    }

    document.addEventListener("click", function (e) {
        let input = document.getElementById("timeOutInput");
        let dd = document.getElementById("timeOutDropdown");

        if (!input.contains(e.target) && !dd.contains(e.target)) {
            dd.style.display = "none";
        }
    });
</script>
<?php include 'includes/footer.php'; ?>
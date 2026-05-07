<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');

$error = "";
$success = "";

$names = $conn->query("
    SELECT DISTINCT volunteer_name 
    FROM attendance
    WHERE attendance_date = CURDATE()
    AND time_out IS NULL
");

if (isset($_POST['time_out'])) {

    $name = $_POST['name'];
    $time = date("Y-m-d H:i:s");
    $today = date("Y-m-d");

    // 1. CHECK IF MAY ACTIVE BORROWED ITEMS
    $checkBorrow = $conn->query("
        SELECT * FROM borrow_records
        WHERE borrower_name = '$name'
        AND status = 'borrowed'
    ");

    if ($checkBorrow->num_rows > 0) {
        $error = "Cannot time out. You still have borrowed items.";
    }

    // 2. CHECK IF MAY TIME IN
    $check = $conn->query("
        SELECT * FROM attendance 
        WHERE volunteer_name = '$name' 
        AND DATE(time_in) = '$today'
    ");

    if (!$error && $check->num_rows == 0) {
        $error = "This person has not timed in yet.";
    }

    // 3. PROCESS TIME OUT
    if (!$error) {

        // check already timed out
        $check2 = $conn->query("
            SELECT * FROM attendance 
            WHERE volunteer_name = '$name'
            AND DATE(time_in) = '$today'
            AND time_out IS NULL
        ");

        if ($check2->num_rows == 0) {
            $error = $name . " already timed out.";
        } else {

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
            <div class="dropdown-wrapper">
                <input type="text" id="timeOutInput" name="name" placeholder="Enter name..." autocomplete="off"
                    required>

                <div id="dropdownList" class="dropdown-list"></div>
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
const input = document.getElementById("timeOutInput");
const dropdown = document.getElementById("dropdownList");

// PHP data into JS
const names = [
    <?php while ($row = $names->fetch_assoc()): ?>
        "<?= $row['volunteer_name'] ?>",
    <?php endwhile; ?>
];

// show dropdown
input.addEventListener("focus", showList);
input.addEventListener("input", showList);

function showList() {
    let val = input.value.toLowerCase();
    dropdown.innerHTML = "";

    let filtered = names.filter(n => n.toLowerCase().includes(val));

    filtered.forEach(name => {
        let div = document.createElement("div");
        div.textContent = name;

        div.onclick = function () {
            input.value = name;
            dropdown.innerHTML = "";
        };

        dropdown.appendChild(div);
    });

    dropdown.style.display = "block";
}

// close when clicking outside
document.addEventListener("click", function(e){
    if (!e.target.closest(".dropdown-wrapper")) {
        dropdown.style.display = "none";
    }
});
</script>

<script>
document.querySelector("input[name='name']").addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault(); // stop form auto submit
        openTimeInModal();
    }
});
</script>

<script>
document.getElementById("timeOutInput").addEventListener("keydown", function (e) {
    if (e.key === "Enter") {
        e.preventDefault(); // stop form auto submit
        openTimeOutModal();
    }
});
</script>
<script>
    document.addEventListener("click", function (e) {
    if (e.target !== input) {
        input.focus();
    }
});
</script>
<?php include 'includes/footer.php'; ?>
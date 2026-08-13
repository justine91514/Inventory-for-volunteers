```php
<?php

include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
include 'log_helper.php';

date_default_timezone_set('Asia/Manila');

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["time_in"])) {

    // Get and clean name
    $name = trim($_POST["name"] ?? "");

    // Validate name
    if ($name === "") {

        $error = "Please enter the volunteer name.";

    } else {

        $today = date("Y-m-d");
        $time = date("Y-m-d H:i:s");

        /*
        =====================================================
        CHECK IF USER IS CURRENTLY TIMED IN
        =====================================================
        Only consider an attendance record active when
        time_out is NULL.
        */

        $checkStmt = $conn->prepare("
            SELECT id
            FROM attendance
            WHERE volunteer_name = ?
            AND attendance_date = ?
            AND time_out IS NULL
            LIMIT 1
        ");

        if (!$checkStmt) {

            $error = "Database error: " . $conn->error;

        } else {

            $checkStmt->bind_param("ss", $name, $today);
            $checkStmt->execute();
            $checkStmt->store_result();

            if ($checkStmt->num_rows > 0) {

                // User is already timed in
                $error = "User already timed in. Please time out first.";

            } else {

                /*
                =====================================================
                INSERT NEW ATTENDANCE RECORD
                =====================================================
                */

                $insertStmt = $conn->prepare("
                    INSERT INTO attendance
                    (volunteer_name, time_in, attendance_date)
                    VALUES (?, ?, ?)
                ");

                if (!$insertStmt) {

                    $error = "Database error: " . $conn->error;

                } else {

                    $insertStmt->bind_param(
                        "sss",
                        $name,
                        $time,
                        $today
                    );

                    if ($insertStmt->execute()) {

                        /*
                        =====================================================
                        ADD LOG
                        =====================================================
                        */

                        addLog(
                            $conn,
                            "Time In",
                            "$name timed in"
                        );

                        $success = "Hi " . htmlspecialchars($name) .
                                   ", your time in has been recorded!";

                    } else {

                        $error = "Error recording Time In: " .
                                 $insertStmt->error;
                    }

                    $insertStmt->close();
                }
            }

            $checkStmt->close();
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

        <?php if ($error !== ""): ?>

            <div class="alert error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <?php if ($success !== ""): ?>

            <div class="alert success">
                <?= htmlspecialchars($success) ?>
            </div>

        <?php endif; ?>


        <form method="POST" id="timeInForm">

            <label for="name">
                Volunteer Name
            </label>

            <input
                type="text"
                id="name"
                name="name"
                placeholder="Enter name..."
                required
                autocomplete="off"
            >

            <!--
                Hidden input used to identify
                the Time In submission.
            -->
            <input
                type="hidden"
                name="time_in"
                value="1"
            >

            <button
                type="button"
                onclick="openTimeInModal()"
            >
                ✔ Submit Time In
            </button>

        </form>

    </div>

</div>


<!-- =====================================================
     TIME IN CONFIRMATION MODAL
     ===================================================== -->

<div
    id="timeInModal"
    class="modal"
    style="display: none;"
>

    <div class="modal-box">

        <div class="modal-header">

            <h3>
                Time In Confirmation
            </h3>

            <span
                onclick="closeTimeInModal()"
                style="cursor: pointer;"
            >
                ✖
            </span>

        </div>


        <div class="modal-body">

            <p>
                Confirm Time In for:
            </p>

            <h4 id="timeInName"></h4>

        </div>


        <div class="modal-footer">

            <button
                type="button"
                class="btn-confirm"
                onclick="submitTimeIn()"
            >
                Confirm
            </button>

            <button
                type="button"
                class="btn-cancel"
                onclick="closeTimeInModal()"
            >
                Cancel
            </button>

        </div>

    </div>

</div>


<script>

/*
=====================================================
OPEN TIME IN MODAL
=====================================================
*/

function openTimeInModal() {

    const input = document.getElementById("name");

    const name = input.value.trim();


    // Check if name is empty
    if (name === "") {

        alert("Please enter the volunteer name.");

        input.focus();

        return;
    }


    // Put name inside modal
    document.getElementById("timeInName").textContent = name;


    // Show modal
    document.getElementById("timeInModal").style.display = "flex";
}


/*
=====================================================
CLOSE TIME IN MODAL
=====================================================
*/

function closeTimeInModal() {

    document.getElementById("timeInModal").style.display = "none";
}


/*
=====================================================
SUBMIT TIME IN
=====================================================
*/

function submitTimeIn() {

    const form = document.getElementById("timeInForm");

    form.submit();
}


/*
=====================================================
ENTER KEY
=====================================================
*/

document
    .getElementById("name")
    .addEventListener("keydown", function (event) {

        if (event.key === "Enter") {

            event.preventDefault();

            openTimeInModal();
        }
    });


/*
=====================================================
CLOSE MODAL WHEN CLICKING OUTSIDE
=====================================================
*/

document
    .getElementById("timeInModal")
    .addEventListener("click", function (event) {

        if (event.target === this) {

            closeTimeInModal();
        }
    });


</script>


<?php

include 'includes/footer.php';

?>
```

<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

date_default_timezone_set('Asia/Manila');

/* ===== DATE ===== */

$date = $_GET['date'] ?? date("Y-m-d");

/* ===== GET ALARM TIME ===== */

$alarmQuery = $conn->query("
    SELECT timeout_time 
    FROM alarm_settings 
    LIMIT 1
");

$alarmData = $alarmQuery->fetch_assoc();

/* default 6PM kapag walang setting */
$timeoutTime = $alarmData['timeout_time'] ?? '18:00:00';

/* current time */
$now = date("H:i:s");

/* 30 mins before timeout */
$warningTime = date(
    "H:i:s",
    strtotime($timeoutTime . " -30 minutes")
);

/* ===== ATTENDANCE QUERY ===== */

$attendance = $conn->query("
    SELECT *,

    CASE

        /* 🔴 overdue */
        WHEN time_out IS NULL 
        AND '$now' >= '$timeoutTime'
        THEN 0

        /* 🟠 warning */
        WHEN time_out IS NULL 
        AND '$now' >= '$warningTime'
        THEN 1

        /* normal */
        ELSE 2

    END AS priority_sort

    FROM attendance

    WHERE attendance_date = '$date'

    ORDER BY priority_sort ASC, id DESC
");

/* ===== INVENTORY ===== */

$inventory = $conn->query("
    SELECT *,
    (total_qty - available_qty) AS borrowed_qty
    FROM inventory
");

/* ===== BORROWED ITEMS ===== */

$borrow = $conn->query("
    SELECT borrower_name, item_name,
           SUM(quantity) AS quantity,
           MAX(borrow_date) AS borrow_date
    FROM borrow_records
    WHERE status = 'borrowed'
    GROUP BY borrower_name, item_name
    ORDER BY borrow_date DESC
");
?>


<div class="main-content">

    <h1>Dashboard</h1>

    <!-- ===== ATTENDANCE ===== -->
    <div class="card">
        <div class="card">
            <input type="text" class="table-search" placeholder="Search attendance..." data-table="attendanceTable">
            <div class="table-header">
                <h3>Attendance Logs</h3>

                <div class="date-nav">
                    <a class="nav-btn" href="?date=<?= date('Y-m-d', strtotime($date . ' -1 day')) ?>">⬅</a>

                    <!-- CALENDAR BUTTON -->
                    <input type="date" id="calendarPicker" value="<?= $date ?>" style="display:none;">
                    <button type="button" class="date-btn" onclick="toggleCalendar()">
                        <?= date("F d, Y", strtotime($date)) ?>
                    </button>

                    <a class="nav-btn" href="?date=<?= date('Y-m-d', strtotime($date . ' +1 day')) ?>">➡</a>
                </div>
            </div>

            <div id="calendarPopup" class="calendar-popup">
                <div class="calendar-box">

                    <div class="calendar-header">
                        <button onclick="changeMonth(-1)">‹</button>
                        <span id="calMonth"></span>
                        <button onclick="changeMonth(1)">›</button>
                    </div>

                    <div class="calendar-grid" id="calendarGrid"></div>

                </div>
            </div>

            <div class="table-wrapper">
                <table class="table" id="attendanceTable">

                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($attendance && $attendance->num_rows > 0): ?>


                            <?php while ($row = $attendance->fetch_assoc()): ?>

                                <?php
                                $warningClass = "";

                                // only active users
                                if (empty($row['time_out']) && !empty($alarmData['timeout_time'])) {

                                    $now = strtotime(date("H:i:s"));
                                    $alarm = strtotime($alarmData['timeout_time']);

                                    // 30 mins before timeout
                                    $warning = strtotime("-30 minutes", $alarm);

                                    if ($now >= $alarm) {
                                        $warningClass = "danger-row";
                                    } elseif ($now >= $warning) {
                                        $warningClass = "warning-row";
                                    }
                                }
                                ?>

                                <tr class="<?= $warningClass ?>">

                                    <td><?= $row['volunteer_name'] ?></td>

                                    <td>
                                        <?= date("h:i A", strtotime($row['time_in'])) ?>
                                    </td>

                                    <td>
                                        <?= $row['time_out']
                                            ? '<span class="badge-timeout">' . date("h:i A", strtotime($row['time_out'])) . '</span>'
                                            : '<span class="badge-active">Active</span>' ?>
                                    </td>

                                    <td><?= $row['attendance_date'] ?></td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="4" class="empty">
                                    No records found
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>
        </div>

        <!-- ===== INVENTORY ===== -->
        <div class="card">
            <input type="text" class="table-search" placeholder="Search inventory..." data-table="inventoryTable">
            <h3>Inventory Status</h3>

            <div class="table-wrapper">
                <table class="table" id="inventoryTable">
                    <thead>
                        <tr>
                            <th>Item</th>
                            <th>Total</th>
                            <th>Borrowed</th>
                            <th>Available</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php while ($row = $inventory->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['item_name'] ?></td>
                                <td><span class="badge badge-blue"><?= $row['total_qty'] ?></span></td>
                                <td><span class="badge badge-red"><?= $row['borrowed_qty'] ?></span></td>
                                <td>
                                    <span class="badge <?= $row['available_qty'] == 0 ? 'badge-red' : 'badge-green' ?>">
                                        <?= $row['available_qty'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- ===== BORROWED ITEMS ===== -->
        <div class="card">
            <input type="text" class="table-search" placeholder="Search borrowed items..." data-table="borrowTable">
            <h3>Borrowed Items</h3>

            <div class="table-wrapper">
                <table class="table" id="borrowTable">
                    <thead>
                        <tr>
                            <th>Borrower</th>
                            <th>Item</th>
                            <th>Quantity</th>
                            <th>Date</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if ($borrow && $borrow->num_rows > 0): ?>
                            <?php while ($row = $borrow->fetch_assoc()): ?>
                                <tr>
                                    <td><?= $row['borrower_name'] ?></td>
                                    <td><?= $row['item_name'] ?></td>
                                    <td><span class="badge badge-blue"><?= $row['quantity'] ?></span></td>
                                    <td><?= date("Y-m-d h:i A", strtotime($row['borrow_date'])) ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="empty">No borrowed items</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <script>

            const timeoutTime = "<?= $alarmData['timeout_time'] ?? '' ?>";

            function updateWarningRows() {

                if (!timeoutTime) return;

                const rows = document.querySelectorAll("#attendanceTable tbody tr");

                let now = new Date();

                let [h, m, s] = timeoutTime.split(":");

                let alarm = new Date();
                alarm.setHours(h, m, s || 0);

                let warning = new Date(alarm.getTime() - (30 * 60 * 1000));

                rows.forEach(row => {

                    const statusCell = row.children[2];

                    if (!statusCell) return;

                    const isActive = statusCell.innerText.includes("Active");

                    // remove old classes first
                    row.classList.remove("warning-row");
                    row.classList.remove("danger-row");

                    if (isActive) {

                        if (now >= alarm) {

                            row.classList.add("danger-row");

                        } else if (now >= warning) {

                            row.classList.add("warning-row");
                        }
                    }
                });
            }

            // run immediately
            updateWarningRows();

            // auto update every second
            setInterval(updateWarningRows, 1000);

        </script>
        <?php include 'includes/footer.php'; ?>
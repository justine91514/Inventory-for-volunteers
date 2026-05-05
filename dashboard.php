<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

date_default_timezone_set('Asia/Manila');

/* ===== QUERIES ===== */

$date = $_GET['date'] ?? date("Y-m-d");

$attendance = $conn->query("
    SELECT * FROM attendance 
    WHERE attendance_date = '$date'
    ORDER BY id DESC
");

// Inventory
$inventory = $conn->query("
    SELECT *,
    (total_qty - available_qty) AS borrowed_qty
    FROM inventory
");

// Borrowed Items (grouped)
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

<link rel="stylesheet" href="assets/css/dashboard.css">

<div class="main-content">

    <h1>Dashboard</h1>

    <!-- ===== ATTENDANCE ===== -->
    <div class="card">
        <input type="text" class="table-search" placeholder="Search attendance..." data-table="attendanceTable">
        <div class="table-header">
            <h3>Attendance Logs</h3>

            <div class="date-nav">
                <a class="nav-btn" href="?date=<?= date('Y-m-d', strtotime($date . ' -1 day')) ?>">⬅</a>

                <button type="button" class="date-btn" onclick="toggleCalendar()">
                    <?= date("F d, Y", strtotime($date)) ?>
                </button>

                <a class="nav-btn" href="?date=<?= date('Y-m-d', strtotime($date . ' +1 day')) ?>">➡</a>
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
                            <tr>
                                <td><?= $row['volunteer_name'] ?></td>
                                <td><?= date("h:i A", strtotime($row['time_in'])) ?></td>
                                <td>
                                    <?= $row['time_out']
                                        ? date("h:i A", strtotime($row['time_out']))
                                        : '<span class="badge badge-red">Active</span>' ?>
                                </td>
                                <td><?= $row['attendance_date'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty">No records found</td>
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
    <?php include 'includes/footer.php'; ?>
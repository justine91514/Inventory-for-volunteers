<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php'; // ✅ IMPORTANT
date_default_timezone_set('Asia/Manila');
?>

<div class="main-content">
    <h1>Dashboard</h1>

    <?php
    $result = $conn->query("SELECT * FROM attendance ORDER BY id DESC");
    ?>

    <table border="1">
        <tr>
            <th>Name</th>
            <th>Time In</th>
            <th>Time Out</th>
            <th>Date</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['volunteer_name'] ?></td>
                    <td>
                        <?= date("h:i A", strtotime($row['time_in'])) ?>
                    </td>

                    <td>
                        <?= $row['time_out']
                            ? date("h:i A", strtotime($row['time_out']))
                            : '---' ?>
                    </td>
                    <td><?= $row['attendance_date'] ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No records found</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<?php
$borrow = $conn->query("
    SELECT * FROM borrow_records
    WHERE status = 'borrowed'
    ORDER BY id DESC
");
?>

<div class="main-content">
    <h2>Borrowed Items</h2>

    <table class="inventory-table">
        <tr>
            <th>Borrower</th>
            <th>Item</th>
            <th>Quantity</th>
            <th>Date</th>
        </tr>

        <?php if ($borrow && $borrow->num_rows > 0): ?>
            <?php while ($row = $borrow->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['borrower_name'] ?></td>
                    <td><?= $row['item_name'] ?></td>
                    <td><?= $row['quantity'] ?></td>
                    <td><?= date("Y-m-d h:i A", strtotime($row['borrow_date'])) ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4">No active borrows</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<?php
$inv = $conn->query("
    SELECT *,
    (total_qty - available_qty) AS borrowed_qty
    FROM inventory
");
?>



<div class="table-container">

    <table class="inventory-table">

        <tr>
            <th>Item</th>
            <th>Total</th>
            <th>Borrowed</th>
            <th>Available</th>
        </tr>

        <?php while ($row = $inv->fetch_assoc()): ?>
            <tr>
                <td><strong><?= $row['item_name'] ?></strong></td>

                <td>
                    <span class="badge badge-blue">
                        <?= $row['total_qty'] ?>
                    </span>
                </td>

                <td>
                    <span class="badge badge-red">
                        <?= $row['borrowed_qty'] ?>
                    </span>
                </td>

                <td>
                    <span class="badge badge-green">
                        <?= $row['available_qty'] ?>
                    </span>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>

</div>


<script>
setInterval(() => {
    location.reload();
}, 5000);
</script>
<?php include 'includes/footer.php'; ?>
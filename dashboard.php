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

<?php include 'includes/footer.php'; ?>
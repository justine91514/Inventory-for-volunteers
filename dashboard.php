<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php'; // ✅ IMPORTANT
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
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="3">No records found</td>
            </tr>
        <?php endif; ?>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
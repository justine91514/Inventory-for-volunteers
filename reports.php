<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

$reports = $conn->query("
    SELECT *
    FROM reports
    ORDER BY created_at DESC
");
?>

<div class="main-content">

    <div class="card">

        <div class="table-header">
            <h2>System Reports</h2>
        </div>

        <input type="text" class="table-search" placeholder="Search reports..." data-table="reportsTable">

        <div class="table-wrapper">

            <table class="table" id="reportsTable">

                <thead>
                    <tr>
                        <th>User</th>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>

                    <?php
                    $currentTransaction = "";

                    while ($row = $reports->fetch_assoc()):

                        $isGrouped = !empty($row['transaction_id']);

                        // NEW GROUP
                        if ($isGrouped && $currentTransaction != $row['transaction_id']):

                            $currentTransaction = $row['transaction_id'];
                            ?>

                            <tr class="report-parent" onclick="toggleReport('<?= $currentTransaction ?>')">

                                <td><?= $row['performed_by'] ?></td>

                                <?php
                                preg_match('/borrowed by (.*?) -/', $row['description'], $matches);
                                $borrowerName = $matches[1] ?? 'Unknown';
                                ?>

                                <td>
                                    <?= $borrowerName ?> Borrowed Items
                                </td>

                                <td>
                                    Click to expand ▼
                                </td>

                                <td>
                                    <?= date("Y-m-d h:i A", strtotime($row['created_at'])) ?>
                                </td>

                            </tr>

                        <?php endif; ?>

                        <?php if ($isGrouped): ?>

                            <tr class="report-child child-<?= $currentTransaction ?>" style="display:none;">

                                <td colspan="4">
                                    • <?= $row['description'] ?>
                                </td>

                            </tr>

                        <?php else: ?>

                            <tr>

                                <td><?= $row['performed_by'] ?></td>

                                <td><?= $row['action_type'] ?></td>

                                <td><?= $row['description'] ?></td>

                                <td>
                                    <?= date("Y-m-d h:i A", strtotime($row['created_at'])) ?>
                                </td>

                            </tr>

                        <?php endif; ?>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<script>
    function toggleReport(id) {

        let rows = document.querySelectorAll(".child-" + id);

        rows.forEach(row => {

            row.style.display =
                row.style.display === "table-row"
                    ? "none"
                    : "table-row";

        });
    }
</script>

<?php include 'includes/footer.php'; ?>
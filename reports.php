<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

$logs = $conn->query("
    SELECT * FROM reports
    ORDER BY id DESC
");
?>

<div class="main-content">

    <div class="card">

        <div class="table-header">
            <h2>System Reports</h2>
        </div>

        <input type="text"
               class="table-search"
               placeholder="Search reports..."
               data-table="reportsTable">

        <div class="table-wrapper">

            <table class="table" id="reportsTable">

                <thead>
                    <tr>
                        <th>Action</th>
                        <th>Description</th>
                        <th>Date</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while($row = $logs->fetch_assoc()): ?>

                        <tr>

                            <td>
                                <span class="badge badge-blue">
                                    <?= $row['action_type'] ?>
                                </span>
                            </td>

                            <td><?= $row['description'] ?></td>

                            <td>
                                <?= date(
                                    "Y-m-d h:i A",
                                    strtotime($row['created_at'])
                                ) ?>
                            </td>

                        </tr>

                    <?php endwhile; ?>

                </tbody>

            </table>

        </div>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
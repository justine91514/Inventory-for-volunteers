<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
session_start();

// GET USERS
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<div class="main-content">

    <div class="auth-card settings-card">

        <h3>Existing Users</h3>

        <table class="table">
            <thead>
                <tr>
                    <th>Gmail</th>
                    <th>Username</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['email'] ?></td>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
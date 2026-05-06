<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
session_start();

$message = "";

// ADD USER
if (isset($_POST['add_user'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    //$password = password_hash($_POST['password'],
    //this is for hashing passwords, if you return this edit, check the login.php

    
    // ✅ Gmail format validation
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $username)) {
        $message = "Username must be a valid Gmail address";
    } else {

        // check duplicate
        $check = $conn->query("SELECT * FROM users WHERE username = '$username'");

        if ($check->num_rows > 0) {
            $message = "User already exists";
        } else {

            // ❌ NO HASH (as requested)
            $conn->query("
                INSERT INTO users (username, password)
                VALUES ('$username', '$password')
            ");

            $message = "User added successfully";
        }
    }
}

// GET USERS
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");
?>
<div class="main-content">

    <div class="auth-card settings-card">

        <div class="auth-header">
            <h2>👤 User Management</h2>
            <p>Add login users</p>
        </div>

        <?php if ($message): ?>
            <div class="alert success"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST">

            <label>Username</label>
            <input type="text" name="username" required autocomplete="off">

            <label>Password</label>
            <input type="password" name="password" required>

            <button type="submit" name="add_user" class="btn-primary">
                Add User
            </button>

        </form>

        <hr>

        <h3>Existing Users</h3>

        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Created</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['username'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</div>

<?php include 'includes/footer.php'; ?>
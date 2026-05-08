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
                    <th>Username Updated</th>
                    <th>Action</th>
                </tr>
            </thead>
            </thead>

            <tbody>
                <?php while ($row = $users->fetch_assoc()): ?>
                    <tr>

                        <td><?= $row['email'] ?></td>

                        <td><?= $row['username'] ?></td>

                        <td>
                            <?= date("Y-m-d h:i A", strtotime($row['created_at'])) ?>
                        </td>

                        <td>
                            <?php
                            if ($row['username_updated_at']) {
                                echo date(
                                    "Y-m-d h:i A",
                                    strtotime($row['username_updated_at'])
                                );
                            } else {
                                echo "<span style='color:gray'>Never</span>";
                            }
                            ?>
                        </td>

                        <td class="action-buttons">

                            <button class="btn-edit" onclick="openEditUser(
                        '<?= $row['id'] ?>',
                        '<?= $row['username'] ?>'
                    )">
                                Edit
                            </button>
                            <button class="btn-password" onclick="openPasswordModal('<?= $row['id'] ?>')">
                                Change Password
                            </button>
                            <button class="btn-delete" onclick="deleteUser('<?= $row['id'] ?>')">
                                Delete
                            </button>

                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

    </div>

</div>


<div id="editUserModal" class="modal">

    <div class="modal-box">

        <div class="modal-header">
            <h3>Edit User</h3>
            <span onclick="closeEditUser()">✖</span>
        </div>

        <div class="modal-body">

            <input type="hidden" id="edit_id">

            <label>Username</label>
            <input type="text" id="edit_username" autocomplete="off">

        </div>

        <div class="modal-footer">

            <button class="btn-confirm" onclick="saveUserEdit()">
                Save
            </button>

            <button class="btn-cancel" onclick="closeEditUser()">
                Cancel
            </button>

        </div>

    </div>

</div>

<div id="passwordModal" class="modal">

    <div class="modal-box">

        <div class="modal-header">
            <h3>Change Password</h3>
            <span onclick="closePasswordModal()">✖</span>
        </div>

        <div class="modal-body">

            <input type="hidden" id="pwd_id">

            <!-- STEP 1 -->
            <div id="step1">
                <label>Current Password</label>
                <input type="password" id="current_password" placeholder="Enter current password">

                <!-- <a href="forgot_password.php" class="forgot-link">
                    Forgot Password?
                </a> -->

                <button class="btn-confirm" onclick="verifyCurrentPassword()">
                    Continue
                </button>
            </div>

            <!-- STEP 2 -->
            <div id="step2" style="display:none;">
                <label>New Password</label>
                <input type="password" id="new_password" placeholder="Enter new password">

                <button class="btn-confirm" onclick="savePassword()">
                    Update Password
                </button>
            </div>

        </div>

        <div class="modal-footer">
            <button class="btn-cancel" onclick="closePasswordModal()">Cancel</button>
        </div>

    </div>

</div>
<script>
    
function openPasswordModal(id) {
    document.getElementById("pwd_id").value = id;

    // reset
    document.getElementById("current_password").value = "";
    document.getElementById("new_password").value = "";
    document.getElementById("step1").style.display = "block";
    document.getElementById("step2").style.display = "none";

    document.getElementById("passwordModal").style.display = "flex";
}

function closePasswordModal() {
    document.getElementById("passwordModal").style.display = "none";
}

// STEP 1: verify current password
function verifyCurrentPassword() {

    let id = document.getElementById("pwd_id").value;
    let current = document.getElementById("current_password").value;

    if (!current) {
        alert("Enter current password");
        return;
    }

    fetch("verify_password.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "id=" + encodeURIComponent(id) +
            "&current=" + encodeURIComponent(current)
    })

    .then(res => res.text())
    .then(data => {

        if (data === "success") {

            document.getElementById("step1").style.display = "none";
            document.getElementById("step2").style.display = "block";

        } else {
            alert(data);
        }
    });
}

// STEP 2: update password
function savePassword() {

    let id = document.getElementById("pwd_id").value;
    let current = document.getElementById("current_password").value;
    let newPass = document.getElementById("new_password").value;

    if (!newPass) {
        alert("Enter new password");
        return;
    }

    // ❌ BLOCK SAME PASSWORD
    if (current === newPass) {
        alert("New password cannot be the same as current password");
        return;
    }

    fetch("update_password.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body:
            "id=" + encodeURIComponent(id) +
            "&new=" + encodeURIComponent(newPass)
    })

    .then(res => res.text())
    .then(data => {

        if (data === "success") {
            location.reload();
        } else {
            alert(data);
        }
    });
}
</script>
<script>

    function openEditUser(id, username) {

        document.getElementById("edit_id").value = id;
        document.getElementById("edit_username").value = username;

        document.getElementById("editUserModal").style.display = "flex";
    }

    function closeEditUser() {

        document.getElementById("editUserModal").style.display = "none";
    }

    function saveUserEdit() {

        let id = document.getElementById("edit_id").value;
        let username = document.getElementById("edit_username").value;

        fetch("update_user.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },

            body:
                "id=" + encodeURIComponent(id)
                + "&username=" + encodeURIComponent(username)

        })

            .then(res => res.text())

            .then(data => {

                if (data === "success") {

                    location.reload();

                } else {

                    alert(data);

                }

            });

    }

    function deleteUser(id) {

        if (!confirm("Delete this user?")) return;

        fetch("delete_user.php", {

            method: "POST",

            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },

            body: "id=" + encodeURIComponent(id)

        })

            .then(res => res.text())

            .then(data => {

                if (data === "success") {

                    location.reload();

                } else {

                    alert(data);

                }

            });

    }

</script>


<?php include 'includes/footer.php'; ?>
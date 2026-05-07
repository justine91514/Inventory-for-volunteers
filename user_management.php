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
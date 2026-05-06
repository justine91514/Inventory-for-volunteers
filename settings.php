<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

date_default_timezone_set('Asia/Manila');
?>

<div class="card">
    <h3>Edit Volunteer Name</h3>

    <input type="text" id="old_name" placeholder="Old Name">
    <input type="text" id="new_name" placeholder="New Name">

    <button onclick="updateName()">Update Name</button>

    <p id="msg"></p>
</div>

<script>
    function updateName() {
        let oldName = document.getElementById("old_name").value;
        let newName = document.getElementById("new_name").value;

        if (!oldName || !newName) {
            alert("Fill both fields");
            return;
        }

        fetch("update_name.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "old_name=" + oldName + "&new_name=" + newName
        })
            .then(res => res.text())
            .then(data => {
                if (data === "success") {
                    document.getElementById("msg").innerText = "Updated successfully!";
                    location.reload();
                } else {
                    document.getElementById("msg").innerText = data;
                }
            });
    }
</script>

<?php include 'includes/footer.php'; ?>
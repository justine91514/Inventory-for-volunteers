<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

date_default_timezone_set('Asia/Manila');
?>

<?php
// kuha lahat ng unique names (both tables)
$names = $conn->query("
    SELECT volunteer_name AS name FROM attendance
    UNION
    SELECT borrower_name AS name FROM borrow_records
");
?>

<div class="main-content">

    <div class="card settings-card">
        <h2>⚙️ Manage Names</h2>

        <label>Select Name</label>
        <select id="old_name">
            <option value="">-- Select Name --</option>
            <?php while ($row = $names->fetch_assoc()): ?>
                <option value="<?= $row['name'] ?>">
                    <?= $row['name'] ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label>New Name</label>
        <input type="text" id="new_name" placeholder="Enter new name">

        <button onclick="openRenameModal()" class="btn-primary">
            Update Name
        </button>

        <p id="msg"></p>
    </div>

</div>

<div id="renameModal" class="modal">
    <div class="modal-box">

        <div class="modal-header">
            <h3>Confirm Rename</h3>
            <span onclick="closeRenameModal()">✖</span>
        </div>

        <div class="modal-body">
            <p>Rename:</p>
            <h4 id="oldNameText"></h4>
            <p>to</p>
            <h4 id="newNameText"></h4>
        </div>

        <div class="modal-footer">
            <button class="btn-confirm" onclick="submitRename()">Confirm</button>
            <button class="btn-cancel" onclick="closeRenameModal()">Cancel</button>
        </div>

    </div>
</div>
<script>
    function openRenameModal() {
        let oldName = document.getElementById("old_name").value;
        let newName = document.getElementById("new_name").value;

        if (!oldName || !newName) {
            alert("Fill all fields");
            return;
        }

        document.getElementById("oldNameText").innerText = oldName;
        document.getElementById("newNameText").innerText = newName;

        document.getElementById("renameModal").style.display = "block";
    }

    function closeRenameModal() {
        document.getElementById("renameModal").style.display = "none";
    }

    function submitRename() {
        let oldName = document.getElementById("old_name").value;
        let newName = document.getElementById("new_name").value;

        fetch("update_name.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "old_name=" + oldName + "&new_name=" + newName
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
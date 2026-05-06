<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

date_default_timezone_set('Asia/Manila');
?>

<?php
// kuha lahat ng unique names (both tables)
$names = $conn->query("
    SELECT DISTINCT name FROM (
        SELECT volunteer_name AS name FROM attendance
        UNION ALL
        SELECT borrower_name AS name FROM borrow_records
    ) AS combined
    ORDER BY name ASC
");
?>

<div class="main-content">

    <div class="auth-card settings-card">

        <div class="auth-header">
            <h2>⚙️ Manage Names</h2>
            <p>Rename volunteers & borrowers</p>
        </div>

        <label>Select Name</label>
        <div class="dropdown-wrapper">
            <input type="text" id="old_name" placeholder="Select name..." autocomplete="off">
            <div id="nameDropdown" class="dropdown-list"></div>
        </div>

        <label>New Name</label>
        <input type="text" id="new_name" placeholder="Enter new name..." autocomplete="off">

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
const input = document.getElementById("old_name");
const dropdown = document.getElementById("nameDropdown");

const names = [
<?php while ($row = $names->fetch_assoc()): ?>
    "<?= $row['name'] ?>",
<?php endwhile; ?>
];

// show dropdown
input.addEventListener("focus", showList);
input.addEventListener("input", showList);

function showList() {
    let val = input.value.toLowerCase();
    dropdown.innerHTML = "";

    let filtered = names.filter(n => n.toLowerCase().includes(val));

    filtered.forEach(name => {
        let div = document.createElement("div");
        div.textContent = name;

        div.onclick = function () {
            input.value = name;
            dropdown.style.display = "none";
        };

        dropdown.appendChild(div);
    });

    dropdown.style.display = "block";
}

// close outside
document.addEventListener("click", function(e){
    if (!e.target.closest(".dropdown-wrapper")) {
        dropdown.style.display = "none";
    }
});
</script>

<script>
function openRenameModal() {
    let oldName = document.getElementById("old_name").value.trim();
    let newName = document.getElementById("new_name").value.trim();

    if (!oldName || !newName) {
        alert("Fill all fields");
        return;
    }

    document.getElementById("oldNameText").innerText = oldName;
    document.getElementById("newNameText").innerText = newName;

    document.getElementById("renameModal").style.display = "flex";
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
        body: "old_name=" + encodeURIComponent(oldName) + 
              "&new_name=" + encodeURIComponent(newName)
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
<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');
?>

<?php
$itemList = $conn->query("SELECT DISTINCT item_name FROM inventory");
if (isset($_POST['add_item'])) {
    $item = $_POST['item'];
    $qty = $_POST['qty'];

    // check if item already exists
    $check = $conn->query("SELECT * FROM inventory WHERE item_name = '$item'");

    if ($check->num_rows > 0) {

        // ✅ UPDATE existing item
        $sql = "UPDATE inventory 
                SET total_qty = total_qty + $qty,
                    available_qty = available_qty + $qty
                WHERE item_name = '$item'";

        if ($conn->query($sql)) {
            $success = "Stock updated! Added $qty to $item.";
        } else {
            $error = "Error: " . $conn->error;
        }

    } else {

        // insert item
        $sql = "INSERT INTO inventory (item_name, total_qty, available_qty)
            VALUES ('$item', '$qty', '$qty')";

        if ($conn->query($sql)) {
            $success = "Item added!";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}

// fetch items
$items = $conn->query("SELECT * FROM inventory");
?>

<div class="main-content">
    <h2>Inventory</h2>

    <?php if (!empty($success))
        echo "<p style='color:green'>$success</p>"; ?>
    <?php if (!empty($error))
        echo "<p style='color:red'>$error</p>"; ?>

    <form method="POST">
        <div class="dropdown-wrapper">
            <input type="text" id="itemInput" name="item" placeholder="Item Name" autocomplete="off" required>
            <div id="itemDropdown" class="dropdown-list"></div>
        </div>
        <input type="number" name="qty" placeholder="Quantity" required>
        <button type="submit" name="add_item">Add Item</button>
    </form>

    <table border="1">
        <tr>
            <th>Item</th>
            <th>Total</th>
            <th>Available</th>
            <th>Borrowed</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $items->fetch_assoc()): ?>
            <tr>
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['total_qty'] ?></td>
                <td><?= $row['available_qty'] ?></td>
                <td><?= $row['total_qty'] - $row['available_qty'] ?></td>

                <td>
                    <button onclick="openEdit(
        '<?= $row['id'] ?>',
        '<?= $row['item_name'] ?>',
        '<?= $row['total_qty'] ?>'
    )">Edit</button>

                    <button onclick="deleteItem(<?= $row['id'] ?>)">Delete</button>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <div id="editModal" class="edit-modal">

        <div class="edit-modal-box">

            <div class="edit-modal-header">
                <h3>Edit Inventory Item</h3>
                <span onclick="closeModal()">✖</span>
            </div>

            <div class="edit-modal-body">

                <input type="hidden" id="edit_id">

                <div class="form-group">
                    <label>Item Name</label>
                    <input type="text" id="edit_name" required>
                </div>

                <div class="form-group">
                    <label>Total Quantity</label>
                    <input type="number" id="edit_total" required>
                </div>

                <p id="edit_error" class="edit-error"></p>

            </div>

            <div class="edit-modal-footer">
                <button class="btn-save" onclick="submitEdit()">Save Changes</button>
                <button class="btn-close" onclick="closeModal()">Cancel</button>
            </div>

        </div>

    </div>
</div>

<script>
const itemInput = document.getElementById("itemInput");
const dropdown = document.getElementById("itemDropdown");

const items = [
<?php while ($row = $itemList->fetch_assoc()): ?>
    "<?= $row['item_name'] ?>",
<?php endwhile; ?>
];

itemInput.addEventListener("focus", showItems);
itemInput.addEventListener("input", showItems);

function showItems() {
    let val = itemInput.value.toLowerCase().trim();
    dropdown.innerHTML = "";

    let filtered = items.filter(i => i.toLowerCase().includes(val));

    // 👉 show existing items
    filtered.forEach(item => {
        let div = document.createElement("div");
        div.classList.add("dropdown-item");
        div.textContent = item;

        div.onclick = function () {
            itemInput.value = item;
            dropdown.style.display = "none";
        };

        dropdown.appendChild(div);
    });

    // 👉 show "Add new item" if not exact match
    if (val && !items.some(i => i.toLowerCase() === val)) {
        let addBtn = document.createElement("div");
        addBtn.classList.add("dropdown-add");
        addBtn.textContent = `+ Add "${itemInput.value}"`;

        addBtn.onclick = function () {
            // just keep value (your PHP will handle insert/update)
            dropdown.style.display = "none";
        };

        dropdown.appendChild(addBtn);
    }

    dropdown.style.display = "block";
}

// close outside
document.addEventListener("click", function(e){
    if (!e.target.closest(".dropdown-wrapper")) {
        dropdown.style.display = "none";
    }
});
</script>
<?php include 'includes/footer.php'; ?>
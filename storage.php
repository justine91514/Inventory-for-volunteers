<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');
?>

<?php

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

<h2>Inventory</h2>

<?php if (!empty($success))
    echo "<p style='color:green'>$success</p>"; ?>
<?php if (!empty($error))
    echo "<p style='color:red'>$error</p>"; ?>

<form method="POST">
    <input type="text" name="item" placeholder="Item Name" required>
    <input type="number" name="qty" placeholder="Quantity" required>
    <button type="submit" name="add_item">Add Item</button>
</form>

<hr>

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

<div id="editModal" style="display:none; position:fixed; top:20%; left:35%; background:white; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.3);">
    
    <h3>Edit Item</h3>

    <form id="editForm">
        <input type="hidden" id="edit_id">

        <input type="text" id="edit_name" disabled><br><br>

        <input type="number" id="edit_total" required><br><br>

        <button type="button" onclick="submitEdit()">Save</button>
        <button type="button" onclick="closeModal()">Close</button>
    </form>

    <p id="edit_error" style="color:red;"></p>
</div>

<script>
function openEdit(id, name, total) {
    document.getElementById("editModal").style.display = "block";
    document.getElementById("edit_id").value = id;
    document.getElementById("edit_name").value = name;
    document.getElementById("edit_total").value = total;
}

function closeModal() {
    document.getElementById("editModal").style.display = "none";
}

function submitEdit() {
    let id = document.getElementById("edit_id").value;
    let total = document.getElementById("edit_total").value;

    fetch("update_item.php", {
        method: "POST",
        headers: {"Content-Type": "application/x-www-form-urlencoded"},
        body: "id=" + id + "&total=" + total
    })
    .then(res => res.text())
    .then(data => {
        if (data === "success") {
            location.reload();
        } else {
            document.getElementById("edit_error").innerText = data;
        }
    });
}

function deleteItem(id) {
    if (!confirm("Delete this item?")) return;

    fetch("delete_item.php?id=" + id)
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
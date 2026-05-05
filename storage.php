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

<div class="main-content">
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
                    <input type="text" id="edit_name" disabled>
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



<?php include 'includes/footer.php'; ?>
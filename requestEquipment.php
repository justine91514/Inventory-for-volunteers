<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');

$error = "";
$success = "";

// GET ITEMS
$items = $conn->query("SELECT * FROM inventory");

// BORROW ACTION
if (isset($_POST['borrow'])) {

    $name = trim($_POST['name']);
    $selected = $_POST['selected_items'] ?? [];
    $qtys = $_POST['qty'] ?? [];
    $today = date("Y-m-d");

    // ===== CHECK ATTENDANCE =====

    // check if may time in today
    $checkIn = $conn->query("
    SELECT * FROM attendance 
    WHERE volunteer_name = '$name' 
    AND attendance_date = '$today'
");

    if ($checkIn->num_rows == 0) {

        $error = "You must time in first.";

    } else {

        // check if active (no timeout yet)
        $active = $conn->query("
        SELECT * FROM attendance 
        WHERE volunteer_name = '$name' 
        AND attendance_date = '$today'
        AND time_out IS NULL
    ");

        if ($active->num_rows == 0) {
            $error = "You already timed out. Borrowing not allowed.";
        }
    }

    // ===== CONTINUE ONLY IF NO ERROR =====
    if (!$error && empty($selected)) {
        $error = "No items selected.";
    } elseif (!$error) {
        $error = "No items selected.";
    } else {

        foreach ($selected as $item) {

            $qty = (int) ($qtys[$item] ?? 0);

            if ($qty <= 0)
                continue;

            // GET INVENTORY
            $inv = $conn->query("
                SELECT * FROM inventory 
                WHERE item_name = '$item'
            ");
            $row = $inv->fetch_assoc();

            if (!$row)
                continue;

            // CHECK STOCK
            if ($row['available_qty'] < $qty) {
                $error .= "$item not enough stock. ";
                continue;
            }

            // UPDATE STOCK
            $conn->query("
                UPDATE inventory 
                SET available_qty = available_qty - $qty 
                WHERE item_name = '$item'
            ");

            // CHECK IF SAME ITEM ALREADY BORROWED BY USER
            $existing = $conn->query("
    SELECT * FROM borrow_records 
    WHERE borrower_name = '$name'
    AND item_name = '$item'
    AND status = 'borrowed'
");

            if ($existing->num_rows > 0) {

                // UPDATE EXISTING (ADD QUANTITY)
                $conn->query("
        UPDATE borrow_records
        SET quantity = quantity + $qty
        WHERE borrower_name = '$name'
        AND item_name = '$item'
        AND status = 'borrowed'
    ");

            } else {

                // INSERT NEW
                $conn->query("
        INSERT INTO borrow_records 
        (borrower_name, item_name, quantity, borrow_date, status)
        VALUES ('$name', '$item', $qty, NOW(), 'borrowed')
    ");
            }
        }

        if (!$error) {
            $success = "$name successfully borrowed selected items.";

            header("Location: requestEquipment.php?success=1");
            exit;
        }
    }
}
?>

<div class="main-content">

    <h2>Borrow Equipment</h2>

    <!-- MESSAGES -->
    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if (isset($_GET['success'])): ?>
        <p class="success">Borrow successful!</p>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST">

        <input type="text" id="borrowerName" name="name" placeholder="Borrower Name" required>

        <table class="inventory-table">

            <thead>
                <tr>
                    <th>Select</th>
                    <th>Item</th>
                    <th>Available</th>
                    <th>Qty</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($i = $items->fetch_assoc()):
                    $isOut = $i['available_qty'] <= 0;
                    ?>
                    <tr class="<?= $isOut ? 'out-of-stock' : '' ?>">

                        <td>
                            <input type="checkbox" name="selected_items[]" value="<?= $i['item_name'] ?>" class="item-check"
                                <?= $isOut ? 'disabled' : 'disabled' ?>>
                        </td>

                        <td><?= $i['item_name'] ?></td>

                        <td>
                            <?php if ($isOut): ?>
                                <span class="stock-zero">0 (Out of stock)</span>
                            <?php else: ?>
                                <?= $i['available_qty'] ?>
                            <?php endif; ?>
                        </td>

                        <td>
                            <input type="number" name="qty[<?= $i['item_name'] ?>]" min="1" max="<?= $i['available_qty'] ?>"
                                value="1" class="qty-input" disabled>
                        </td>

                    </tr>
                <?php endwhile; ?>
            </tbody>

        </table>

        <br>

        <button type="submit" name="borrow">Equipment Selected</button>

    </form>

</div>


<script>
    const nameInput = document.getElementById("borrowerName");
    const checkboxes = document.querySelectorAll(".item-check");

    nameInput.addEventListener("input", function () {

        let enabled = this.value.trim().length > 0;

        checkboxes.forEach(cb => {

            // ❌ DO NOT ENABLE if out-of-stock
            if (cb.closest("tr").classList.contains("out-of-stock")) {
                cb.disabled = true;
                return;
            }

            cb.disabled = !enabled;

            if (!enabled) {
                cb.checked = false;
                toggleQty(cb, false);
            }
        });
    });

    checkboxes.forEach(cb => {
        cb.addEventListener("change", function () {
            toggleQty(this, this.checked);
        });
    });

    function toggleQty(checkbox, isChecked) {
        let row = checkbox.closest("tr");
        let qty = row.querySelector(".qty-input");

        qty.disabled = !isChecked;

        if (!isChecked) {
            qty.value = 1;
        }
    }
</script>

<?php include 'includes/footer.php'; ?>
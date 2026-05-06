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

    // reset error
    $error = "";

    // ===== ATTENDANCE CHECK =====

    $checkIn = $conn->query("
        SELECT * FROM attendance 
        WHERE volunteer_name = '$name' 
        AND attendance_date = '$today'
    ");

    if ($checkIn->num_rows == 0) {
        $error = "You must time in first.";
    }

    $active = $conn->query("
        SELECT * FROM attendance 
        WHERE volunteer_name = '$name' 
        AND attendance_date = '$today'
        AND time_out IS NULL
    ");

    if (!$error && $active->num_rows == 0) {
        $error = "You already timed out. Borrowing not allowed.";
    }

    // ===== BORROW PROCESS =====
    if (!$error) {

        if (empty($selected)) {
            $error = "No items selected.";
        } else {

            foreach ($selected as $item) {

                $qty = (int) ($qtys[$item] ?? 0);
                if ($qty <= 0)
                    continue;

                $inv = $conn->query("
                    SELECT * FROM inventory 
                    WHERE item_name = '$item'
                ");
                $row = $inv->fetch_assoc();

                if (!$row)
                    continue;

                if ($row['available_qty'] < $qty) {
                    $error .= "$item not enough stock. ";
                    continue;
                }

                // deduct stock
                $conn->query("
                    UPDATE inventory 
                    SET available_qty = available_qty - $qty 
                    WHERE item_name = '$item'
                ");

                // check existing borrow
                $existing = $conn->query("
                    SELECT * FROM borrow_records 
                    WHERE borrower_name = '$name'
                    AND item_name = '$item'
                    AND status = 'borrowed'
                ");

                if ($existing->num_rows > 0) {

                    $conn->query("
                        UPDATE borrow_records
                        SET quantity = quantity + $qty
                        WHERE borrower_name = '$name'
                        AND item_name = '$item'
                        AND status = 'borrowed'
                    ");

                } else {

                    $conn->query("
                        INSERT INTO borrow_records 
                        (borrower_name, item_name, quantity, borrow_date, status)
                        VALUES ('$name', '$item', $qty, NOW(), 'borrowed')
                    ");
                }
            }

            if (!$error) {
                header("Location: requestEquipment.php?success=1");
                exit;
            }
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

        <div class="dropdown-wrapper">

            <input type="text" id="borrowerName" name="name" placeholder="Search borrower..."
                onkeyup="filterBorrowers()" onclick="toggleBorrowerDropdown()" autocomplete="off" required>

            <div id="borrowerDropdown" class="dropdown-list">
                <?php
                $activeUsers = $conn->query("
            SELECT DISTINCT volunteer_name 
            FROM attendance 
            WHERE attendance_date = CURDATE()
            AND time_out IS NULL
        ");

                while ($row = $activeUsers->fetch_assoc()):
                    ?>
                    <div class="dropdown-item" onclick="selectBorrower('<?= $row['volunteer_name'] ?>')">
                        <?= $row['volunteer_name'] ?>
                    </div>
                <?php endwhile; ?>
            </div>

        </div>

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
                                <?= $isOut ? 'disabled' : '' ?>>
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
    //script for checkbox in requestEquipment
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


<script>
    //script for dropdaown in requestEquipment
    function toggleBorrowerDropdown() {
        let dd = document.getElementById("borrowerDropdown");
        dd.style.display = dd.style.display === "block" ? "none" : "block";
    }

    function selectBorrower(name) {
        document.getElementById("borrowerName").value = name;
        document.getElementById("borrowerDropdown").style.display = "none";
    }

    // close when clicking outside
    document.addEventListener("click", function (e) {
        let input = document.getElementById("borrowerName");
        let dd = document.getElementById("borrowerDropdown");

        if (!input.contains(e.target) && !dd.contains(e.target)) {
            dd.style.display = "none";
        }
    });
</script>

<script>
function toggleBorrowerDropdown() {
    document.getElementById("borrowerDropdown").style.display = "block";
}

function selectBorrower(name) {
    document.getElementById("borrowerName").value = name;
    document.getElementById("borrowerDropdown").style.display = "none";
}

// 🔥 LIVE SEARCH FILTER
function filterBorrowers() {
    let input = document.getElementById("borrowerName").value.toLowerCase();
    let items = document.querySelectorAll("#borrowerDropdown .dropdown-item");

    let hasMatch = false;

    items.forEach(item => {
        let text = item.textContent.toLowerCase();

        if (text.includes(input)) {
            item.style.display = "block";
            hasMatch = true;
        } else {
            item.style.display = "none";
        }
    });

    document.getElementById("borrowerDropdown").style.display = hasMatch ? "block" : "none";
}

// close when clicking outside
document.addEventListener("click", function(e){
    let input = document.getElementById("borrowerName");
    let dd = document.getElementById("borrowerDropdown");

    if (!input.contains(e.target) && !dd.contains(e.target)) {
        dd.style.display = "none";
    }
});
</script>

<?php include 'includes/footer.php'; ?>
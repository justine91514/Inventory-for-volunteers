<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';

$error = "";
$success = "";

// GET ACTIVE BORROWS ONLY
$borrows = $conn->query("
    SELECT * FROM borrow_records
    WHERE status = 'borrowed'
    ORDER BY id DESC
");

// RETURN ACTION
if (isset($_POST['return_item'])) {

    $id = (int) $_POST['borrow_id'];

    // 1. GET BORROW DATA
    $borrow = $conn->query("SELECT * FROM borrow_records WHERE id = $id");
    $data = $borrow->fetch_assoc();

    if (!$data) {
        $error = "Invalid record.";
        exit;
    }

    $item = $data['item_name'];
    $qty = (int) $data['quantity'];

    // 2. START SAFE LOGIC (PREVENT DOUBLE RETURN)
    if ($data['status'] === 'returned') {
        $error = "Already returned.";
        exit;
    }

    // 3. RUN BOTH UPDATES SAFELY
    $conn->query("UPDATE inventory 
        SET available_qty = available_qty + $qty 
        WHERE item_name = '$item'");

    $conn->query("UPDATE borrow_records 
        SET status = 'returned' 
        WHERE id = $id");

    $success = "Item returned successfully!";
}
?>

<div class="main-content">
    <h1>Return Equipment</h1>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p class="success"><?= $success ?></p>
    <?php endif; ?>

    <table class="inventory-table">
        <tr>
            <th>Borrower</th>
            <th>Item</th>
            <th>Qty</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $borrows->fetch_assoc()): ?>
            <tr>
                <td><?= $row['borrower_name'] ?></td>
                <td><?= $row['item_name'] ?></td>
                <td><?= $row['quantity'] ?></td>

                <td>
                    <button type="button" onclick="openReturnModal(
        <?= $row['id'] ?>,
        '<?= $row['item_name'] ?>',
        <?= $row['quantity'] ?>
    )">
                        Return
                    </button>
                </td>
            </tr>
        <?php endwhile; ?>

    </table>
</div>


<div id="returnModal"
    style="display:none; position:fixed; top:20%; left:35%; background:white; padding:20px; box-shadow:0 0 10px rgba(0,0,0,0.3);">

    <h3>Return Equipment</h3>

    <p id="returnText"></p>

    <input type="hidden" id="borrow_id">
    <input type="hidden" id="max_qty">

    <input type="number" id="return_qty" min="1" style="display:none;">

    <br><br>

    <button onclick="submitReturn()">Confirm</button>
    <button onclick="closeReturn()">Cancel</button>

</div>



<script>

    function openReturnModal(id, item, qty) {

        document.getElementById("returnModal").style.display = "block";
        document.getElementById("borrow_id").value = id;
        document.getElementById("max_qty").value = qty;

        if (qty > 1) {
            document.getElementById("return_qty").style.display = "block";
            document.getElementById("return_qty").max = qty;
            document.getElementById("return_qty").value = qty;

            document.getElementById("returnText").innerText =
                "How many " + item + " do you want to return?";
        } else {
            document.getElementById("return_qty").style.display = "none";

            document.getElementById("returnText").innerText =
                "Do you really want to return the equipment?";
        }
    }

    function closeReturn() {
        document.getElementById("returnModal").style.display = "none";
    }

    function submitReturn() {

        let id = document.getElementById("borrow_id").value;
        let max = document.getElementById("max_qty").value;
        let qtyInput = document.getElementById("return_qty");

        let qty = (qtyInput.style.display === "none") ? 1 : parseInt(qtyInput.value);

        if (qty > max) {
            alert("Cannot return more than borrowed quantity.");
            return;
        }

        if (!confirm("Do you really want to return the equipment?")) {
            return;
        }

        fetch("processReturn.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + id + "&qty=" + qty
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
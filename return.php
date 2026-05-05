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
    '<?= $row['borrower_name'] ?>',
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


<div id="returnModal">

    <div id="modalHeader">
        <h3>Return Equipment</h3>
    </div>

    <p><strong>Borrower:</strong> <span id="m_borrower"></span></p>
    <p><strong>Item:</strong> <span id="m_item"></span></p>
    <p><strong>Borrowed Qty:</strong> <span id="m_qty"></span></p>

    <p id="returnText"></p>

    <input type="hidden" id="borrow_id">
    <input type="hidden" id="max_qty">

    <input type="number" id="return_qty" min="1" style="display:none;">

    <br><br>

    <button onclick="submitReturn()">Confirm</button>
    <button onclick="closeReturn()">Cancel</button>
</div>
<?php include 'includes/footer.php'; ?>
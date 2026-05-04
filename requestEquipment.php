<?php
include 'includes/header.php';
include 'includes/sidebar.php';
include 'includes/db.php';
date_default_timezone_set('Asia/Manila');
?>

<?php
$error = "";
$success = "";

// get items
$items = $conn->query("SELECT * FROM inventory");


// borrow action
if (isset($_POST['borrow'])) {

    $name = trim($_POST['name']);
    $item = $_POST['item'];
    $qty = (int) $_POST['qty'];
    $today = date("Y-m-d");

    // 1. CHECK TIME-IN
    $check = $conn->query("SELECT * FROM attendance 
        WHERE volunteer_name = '$name' 
        AND attendance_date = '$today'");

    if ($check->num_rows == 0) {
        $error = "You must time in first before borrowing.";
    } else {

        // 2. GET INVENTORY
        $inv = $conn->query("SELECT * FROM inventory WHERE item_name = '$item'");
        $row = $inv->fetch_assoc();

        if (!$row) {
            $error = "Item not found.";

            // 3. STRONG SAFETY CHECK (IMPORTANT FIX)
        } elseif ($qty <= 0) {
            $error = "Invalid quantity.";

        } elseif ($row['available_qty'] < $qty) {
            $error = "Not enough stock.";

        } else {

            // 4. UPDATE INVENTORY (SAFE)
            $conn->query("
                UPDATE inventory 
                SET available_qty = available_qty - $qty 
                WHERE item_name = '$item'
            ");

            // 5. INSERT BORROW RECORD
            $conn->query("
                INSERT INTO borrow_records 
                (borrower_name, item_name, quantity, borrow_date, status)
                VALUES ('$name', '$item', $qty, NOW(), 'borrowed')
            ");

            $success = "$name borrowed $qty $item";
        }
    }
}
?>

<h2>Borrow Item</h2>

<?php if ($error)
    echo "<p style='color:red'>$error</p>"; ?>
<?php if ($success)
    echo "<p style='color:green'>$success</p>"; ?>

<form method="POST">

    <input type="text" name="name" placeholder="Borrower Name" required>

    <select name="item" required>
        <option value="">Select Item</option>
        <?php while ($i = $items->fetch_assoc()): ?>
            <option value="<?= $i['item_name'] ?>">
                <?= $i['item_name'] ?> (Available: <?= $i['available_qty'] ?>)
            </option>
        <?php endwhile; ?>
    </select>

    <input type="number" name="qty" placeholder="Quantity" required>

    <button type="submit" name="borrow">Borrow</button>
</form>













<?php include 'includes/footer.php'; ?>
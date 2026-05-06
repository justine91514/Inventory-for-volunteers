<?php
session_start();
include 'includes/db.php';
$users = $conn->query("SELECT username FROM users ORDER BY username ASC");


$error = "";

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // return this if you returned the hasshing in user_mangement
        //if (password_verify($password, $user['password']))
            if ($password === $user['password']) {
            $_SESSION['user'] = $user['username'];
            header("Location: dashboard.php");
            exit;
        } else {
            $error = "Wrong password";
        }
    } else {
        $error = "User not found";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>

<div class="login-box">

    <h2>Login</h2>

    <?php if ($error): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <form method="POST">
        <div class="dropdown-wrapper">

    <input type="text" id="usernameInput" name="username"
        placeholder="Select username..." autocomplete="off" required>

    <div id="userDropdown" class="dropdown-list"></div>

</div>
        <input type="password" name="password" placeholder="Password" required>

        <button type="submit" name="login">Login</button>
    </form>

</div>


<script>
const input = document.getElementById("usernameInput");
const dropdown = document.getElementById("userDropdown");

const users = [
<?php while ($row = $users->fetch_assoc()): ?>
    "<?= $row['username'] ?>",
<?php endwhile; ?>
];

// show list
input.addEventListener("focus", showList);
input.addEventListener("input", showList);

function showList() {
    let val = input.value.toLowerCase();
    dropdown.innerHTML = "";

    let filtered = users.filter(u =>
        u.toLowerCase().includes(val)
    );

    filtered.forEach(user => {
        let div = document.createElement("div");
        div.textContent = user;

        div.onclick = function () {
            input.value = user;
            dropdown.style.display = "none";
        };

        dropdown.appendChild(div);
    });

    dropdown.style.display = "block";
}

// close when clicking outside
document.addEventListener("click", function(e){
    if (!e.target.closest(".dropdown-wrapper")) {
        dropdown.style.display = "none";
    }
});
</script>
</body>
</html>
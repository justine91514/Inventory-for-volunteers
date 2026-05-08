<?php
session_start();
include 'includes/db.php';
include 'log_helper.php';

$error = "";
$success = "";

/* ================= LOGIN ================= */
if (isset($_POST['login'])) {

    $login = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $conn->prepare("
        SELECT * FROM users 
        WHERE username = ?
        OR email = ?
    ");

    $stmt->bind_param("ss", $login, $login);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        if ($password === $user['password']) {

            $_SESSION['username'] = $user['username'];

            addLog(
                $conn,
                "Login",
                $user['username'] . " logged in"
            );

            header("Location: dashboard.php");
            exit;


        } else {
            $error = "Wrong password";
        }

    } else {
        $error = "User not found";
    }
}
/* ================= SIGNUP ================= */
/* ================= SIGNUP ================= */
if (isset($_POST['signup'])) {

    $email = trim($_POST['signup_email'] ?? '');
    $username = trim($_POST['signup_username'] ?? '');
    $password = $_POST['signup_password'] ?? '';

    // gmail validation
    if (!preg_match("/^[a-zA-Z0-9._%+-]+@gmail\.com$/", $email)) {

        $error = "Email must be a Gmail address";

    } else {

        // check duplicate email
        $checkEmail = $conn->query("
            SELECT * FROM users 
            WHERE email='$email'
        ");

        // check duplicate username
        $checkUser = $conn->query("
            SELECT * FROM users 
            WHERE username='$username'
        ");

        if ($checkEmail->num_rows > 0) {

            $error = "Email already exists";

        } elseif ($checkUser->num_rows > 0) {

            $error = "Username already taken";

        } else {

            $conn->query("
                INSERT INTO users(email, username, password)
                VALUES('$email','$username','$password')
            ");

            $success = "Account created successfully!";
        }
    }
}

/* ================= USERNAMES ================= */
$users = $conn->query("
    SELECT username FROM users
    ORDER BY username ASC
");
?>

<!DOCTYPE html>
<html>
<div id="forgotModal" class="modal">

    <div class="modal-box">

        <div class="modal-header">
            <h3>Reset Password</h3>
            <span onclick="closeForgotModal()">✖</span>
        </div>

        <div class="modal-body">

            <input type="email" id="forgotEmail" placeholder="Enter Gmail address" autocomplete="off">

            <input type="password" id="newPassword" placeholder="New Password">

        </div>

        <div class="modal-footer">

            <button class="btn-confirm" onclick="resetPassword()">
                Reset
            </button>

            <button class="btn-cancel" onclick="closeForgotModal()">
                Cancel
            </button>

        </div>

    </div>

</div>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="assets/css/login.css">
</head>

<body>

    <div class="login-box">

        <h2 id="formTitle">Login</h2>

        <?php if ($error): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success"><?= $success ?></p>
        <?php endif; ?>

        <!-- LOGIN FORM -->
        <form method="POST" id="loginForm">

            <div class="dropdown-wrapper">

                <input type="text" id="usernameInput" name="username" placeholder="Username or Gmail..."
                    autocomplete="off" required>

                <div id="userDropdown" class="dropdown-list"></div>

            </div>

            <input type="password" name="password" placeholder="Password" required>

            <button type="submit" name="login">
                Login
            </button>
            <hr>
            <div class="forgot-wrapper">
                <button type="button" id="forgotBtn">
                    Forgot Password?
                </button>
            </div>

        </form>

        <!-- SIGNUP FORM -->
        <form method="POST" id="signupForm" style="display:none;">

            <input type="email" name="signup_email" placeholder="Gmail Address" autocomplete="off" required>

            <input type="text" name="signup_username" placeholder="Username" autocomplete="off" required>

            <input type="password" name="signup_password" placeholder="Password" required>

            <button type="submit" name="signup">
                Create Account
            </button>

        </form>

        <div class="switch-form">

            <span id="toggleText">
                Don't have an account?
            </span>

            <button type="button" id="toggleBtn">
                Sign Up
            </button>

        </div>

    </div>

    <script>
        /* ========= LOGIN USER DROPDOWN ========= */

        const input = document.getElementById("usernameInput");
        const dropdown = document.getElementById("userDropdown");

        const users = [
            <?php while ($row = $users->fetch_assoc()): ?>
                                                                                                                                            "<?= $row['username'] ?>",
            <?php endwhile; ?>
        ];

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

        document.addEventListener("click", function (e) {

            if (!e.target.closest(".dropdown-wrapper")) {
                dropdown.style.display = "none";
            }

        });

        /* ========= TOGGLE LOGIN / SIGNUP ========= */

        const loginForm = document.getElementById("loginForm");
        const signupForm = document.getElementById("signupForm");

        const toggleBtn = document.getElementById("toggleBtn");
        const toggleText = document.getElementById("toggleText");
        const formTitle = document.getElementById("formTitle");

        let signupMode = false;

        toggleBtn.addEventListener("click", function () {

            signupMode = !signupMode;

            if (signupMode) {

                loginForm.style.display = "none";
                signupForm.style.display = "block";

                formTitle.innerText = "Create Account";

                toggleText.innerText = "Already have an account?";
                toggleBtn.innerText = "Login";

            } else {

                loginForm.style.display = "block";
                signupForm.style.display = "none";

                formTitle.innerText = "Login";

                toggleText.innerText = "Don't have an account?";
                toggleBtn.innerText = "Sign Up";
            }

        });
    </script>
    <script>

        const forgotModal = document.getElementById("forgotModal");

        document.getElementById("forgotBtn")
            .addEventListener("click", function () {

                forgotModal.style.display = "flex";

            });

        function closeForgotModal() {

            forgotModal.style.display = "none";

        }

        function resetPassword() {

            let email = document.getElementById("forgotEmail").value;
            let password = document.getElementById("newPassword").value;

            fetch("reset_password_in_login.php", {

                method: "POST",

                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },

                body:
                    "email=" + encodeURIComponent(email)
                    + "&password=" + encodeURIComponent(password)

            })

                .then(res => res.text())

                .then(data => {

                    if (data === "success") {

                        alert("Password updated successfully");

                        closeForgotModal();

                    } else {

                        alert(data);

                    }

                });

        }
    </script>
</body>

</html>
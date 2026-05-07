<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<div class="sidebar" id="sidebar">

    <div class="sidebar-header">
        <h2>Inventory</h2>
        <button id="toggleBtn">
            <button id="toggleBtn">☰</button>
        </button>
    </div>

    <ul class="menu">
        <!-- OTHER MENU -->
        <li>
            <a href="dashboard.php">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </li>

        <li>
            <a href="timeIn.php">
                <i class="fas fa-sign-in-alt"></i>
                <span>Time In</span>
            </a>
        </li>

        <li>
            <a href="timeOut.php">
                <i class="fas fa-sign-out-alt"></i>
                <span>Time Out</span>
            </a>
        </li>

        <li>
            <a href="requestEquipment.php">
                <i class="fas fa-hand-holding"></i>
                <span>Request Equipment</span>
            </a>
        </li>

        <li>
            <a href="return.php">
                <i class="fas fa-undo"></i>
                <span>Return</span>
            </a>
        </li>

        <li>
            <a href="storage.php">
                <i class="fas fa-boxes"></i>
                <span>Storage</span>
            </a>
        </li>

        <!-- SETTINGS DROPDOWN -->
        <li class="dropdown">
            <a href="javascript:void(0)" onclick="toggleSettings()">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
                <i class="fas fa-chevron-down arrow"></i>
            </a>

            <ul class="dropdown-menu" id="settingsDropdown">
                <li><a href="user_management.php">User Management</a></li>
                <li><a href="rename.php">Rename</a></li>
                <li><a href="alarm.php">Alarm</a></li>
            </ul>
        </li>

        <!-- PUSH LOGOUT TO BOTTOM -->
        <li style="margin-top:auto;">
            <a href="logout.php">
                <i class="fas fa-right-from-bracket"></i>
                <span>Logout</span>
            </a>
        </li>
    </ul>
    
    <script>
        function toggleSettings() {
            const dropdown = document.getElementById("settingsDropdown");
            dropdown.parentElement.classList.toggle("open");

            if (dropdown.style.display === "block") {
                dropdown.style.display = "none";
            } else {
                dropdown.style.display = "block";
            }
        }
    </script>
    
</div>
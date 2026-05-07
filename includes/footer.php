<script>
    /* ===== SIDEBAR TOGGLE ===== */
    document.getElementById("toggleBtn").onclick = function () {
        document.getElementById("sidebar").classList.toggle("collapsed");
    };

    /* ===== RETURN MODAL ===== */
    function openReturnModal(id, borrower, item, qty) {
        document.getElementById("returnModal").style.display = "block";

        document.getElementById("borrow_id").value = id;
        document.getElementById("max_qty").value = qty;

        document.getElementById("m_borrower").innerText = borrower;
        document.getElementById("m_item").innerText = item;
        document.getElementById("m_qty").innerText = qty;

        if (qty > 1) {
            document.getElementById("return_qty").style.display = "block";
            document.getElementById("return_qty").value = qty;
            document.getElementById("return_qty").max = qty;
            document.getElementById("returnText").innerText = "Select how many to return";
        } else {
            document.getElementById("return_qty").style.display = "none";
            document.getElementById("returnText").innerText = "Confirm return?";
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









    /* ===== TIME IN ===== */
    function openTimeInModal() {
        let name = document.querySelector("input[name='name']").value;
        if (!name) return alert("Enter name first");

        document.getElementById("timeInName").innerText = name;
        document.getElementById("timeInModal").style.display = "block";
    }

    function closeTimeInModal() {
        document.getElementById("timeInModal").style.display = "none";
    }

    function submitTimeIn() {
        let form = document.querySelector("form");

        let hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "time_in";
        hidden.value = "1";

        form.appendChild(hidden);
        form.submit();
    }

    /* ===== TIME OUT ===== */
    function openTimeOutModal() {
        let name = document.querySelector("input[name='name']").value;
        if (!name) return alert("Enter name first");

        document.getElementById("timeOutName").innerText = name;
        document.getElementById("timeOutModal").style.display = "block";
    }

    function closeTimeOutModal() {
        document.getElementById("timeOutModal").style.display = "none";
    }

    function submitTimeOut() {
        let form = document.querySelector("form");

        let hidden = document.createElement("input");
        hidden.type = "hidden";
        hidden.name = "time_out";
        hidden.value = "1";

        form.appendChild(hidden);
        form.submit();
    }
</script>


















<script>
    //SCRIPT FOR EDIT MODAL IN STORAGE
    function openEdit(id, name, total) {
        document.getElementById("editModal").style.display = "block";
        document.getElementById("edit_id").value = id;
        document.getElementById("edit_name").value = name;
        document.getElementById("edit_total").value = total;
    }

    function closeModal() {
        document.getElementById("editModal").style.display = "none";
    }
</script>

<script>
    function submitEdit() {
        let id = document.getElementById("edit_id").value;
        let name = document.getElementById("edit_name").value;
        let total = document.getElementById("edit_total").value;

        fetch("update_inventory.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "id=" + id + "&name=" + encodeURIComponent(name) + "&total=" + total
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
</script>

<script>
    //SCRIPT FOR DELETING ITEM IN STORAGE
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




<script>
    function openCalendar() {
        document.getElementById("calendarPicker").showPicker();
    }

    document.getElementById("calendarPicker").addEventListener("change", function () {
        window.location.href = "?date=" + this.value;
    });
</script>

<script>
    let currentDate = new Date("<?= $date ?>");

    function toggleCalendar() {
        document.getElementById("calendarPopup").style.display = "flex";
        renderCalendar();
    }

    function closeCalendar() {
        document.getElementById("calendarPopup").style.display = "none";
    }

    function changeMonth(step) {
        currentDate.setMonth(currentDate.getMonth() + step);
        renderCalendar();
    }

    function renderCalendar() {
        const grid = document.getElementById("calendarGrid");
        const monthLabel = document.getElementById("calMonth");

        grid.innerHTML = "";

        const year = currentDate.getFullYear();
        const month = currentDate.getMonth();

        const firstDay = new Date(year, month, 1).getDay();
        const daysInMonth = new Date(year, month + 1, 0).getDate();

        monthLabel.innerText = currentDate.toLocaleString('default', {
            month: 'long',
            year: 'numeric'
        });

        const today = new Date();
        today.setHours(0, 0, 0, 0);

        // blank spaces
        for (let i = 0; i < firstDay; i++) {
            grid.innerHTML += `<div></div>`;
        }

        for (let day = 1; day <= daysInMonth; day++) {

            let date = new Date(year, month, day);

            let disabled = date > today ? "disabled-day" : "";

            grid.innerHTML += `
            <div class="calendar-day ${disabled}"
                 onclick="selectDate('${year}-${String(month + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}')">
                ${day}
            </div>
        `;
        }
    }

    function selectDate(date) {
        window.location.href = "?date=" + date;
    }

    // close on outside click
    document.getElementById("calendarPopup").addEventListener("click", function (e) {
        if (e.target === this) closeCalendar();
    });
</script>

<script>
    document.querySelectorAll(".table-search").forEach(input => {
        input.addEventListener("keyup", function () {

            let tableId = this.dataset.table;
            let table = document.getElementById(tableId);
            let filter = this.value.toLowerCase();

            let rows = table.getElementsByTagName("tr");

            for (let i = 1; i < rows.length; i++) {
                let rowText = rows[i].innerText.toLowerCase();

                if (rowText.includes(filter)) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        });
    });
</script>
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
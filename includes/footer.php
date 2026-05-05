<script>
    document.getElementById("toggleBtn").onclick = function () {
        document.getElementById("sidebar").classList.toggle("collapsed");
    };
</script>

<script>
    //MODAL
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

            document.getElementById("returnText").innerText =
                "Select how many to return";
        } else {
            document.getElementById("return_qty").style.display = "none";
            document.getElementById("returnText").innerText =
                "Confirm return?";
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

<script>
    //FOR THE DRAG OF MODAL
    let modal = document.getElementById("returnModal");
    let header = document.getElementById("modalHeader");

    let isDragging = false;
    let offsetX, offsetY;

    header.addEventListener("mousedown", function (e) {
        isDragging = true;

        offsetX = e.clientX - modal.offsetLeft;
        offsetY = e.clientY - modal.offsetTop;
    });

    document.addEventListener("mousemove", function (e) {
        if (isDragging) {
            modal.style.left = (e.clientX - offsetX) + "px";
            modal.style.top = (e.clientY - offsetY) + "px";
            modal.style.transform = "none";
        }
    });

    document.addEventListener("mouseup", function () {
        isDragging = false;
    });
</script>

</body>

</html>
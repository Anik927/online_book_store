'use strict';

console.log('admin_book_index.js loaded');

let selectedBookId = null;

document.addEventListener("DOMContentLoaded", function () {

    // =========================
    // DELETE MODAL
    // =========================

    const modal = document.getElementById("deleteModal");
    const confirmBtn = document.getElementById("confirmDeleteBtn");
    const checkBox = document.getElementById("confirmCheck");

    document.querySelectorAll(".btn-delete").forEach(btn => {

        btn.addEventListener("click", function () {

            selectedBookId = this.dataset.id;

            document.getElementById("bookTitle").innerText = this.dataset.title;

            checkBox.checked = false;
            confirmBtn.disabled = true;

            modal.style.display = "flex";
        });

    });

    checkBox.addEventListener("change", function () {
        confirmBtn.disabled = !this.checked;
    });

    confirmBtn.addEventListener("click", function () {

        const xhr = new XMLHttpRequest();

        xhr.open("POST", "/online_book_store/control/admin_control.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

        xhr.onreadystatechange = function () {

            if (xhr.readyState !== 4) return;

            let res;

            try {
                res = JSON.parse(xhr.responseText);
            } catch (e) {
                alert("Server error");
                return;
            }

            if (res.success) {
                location.reload();
            } else {
                alert(res.message || "Delete failed");
            }
        };

        xhr.send("action=delete_book&book_id=" + selectedBookId);
    });

    // =========================
    // LOGOUT (FIXED LOCATION)
    // =========================

    const logoutBtn = document.getElementById('logoutBtn');

    if (logoutBtn) {

        logoutBtn.addEventListener('click', function (e) {

            e.preventDefault();

            const xhr = new XMLHttpRequest();

            xhr.open('POST', '/online_book_store/control/login_control.php', true);

            xhr.setRequestHeader(
                'Content-Type',
                'application/x-www-form-urlencoded'
            );

            xhr.onreadystatechange = function () {

                if (xhr.readyState !== 4) return;

                let res;

                try {
                    res = JSON.parse(xhr.responseText);
                } catch (error) {
                    alert('Logout failed.');
                    return;
                }

                if (res.success) {
                    window.location.href = res.redirect;
                } else {
                    alert(res.message || 'Logout failed.');
                }
            };

            xhr.send('action=logout');
        });
    }

});

// =========================
// CLOSE MODAL
// =========================

function closeModal() {
    document.getElementById("deleteModal").style.display = "none";
}
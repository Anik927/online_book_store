'use strict';

let selectedUserId = null;

document.addEventListener('DOMContentLoaded', function () {

    const modal      = document.getElementById('deleteModal');
    const confirmBtn = document.getElementById('confirmDeleteBtn');
    const checkBox   = document.getElementById('confirmCheck');
    const alertBanner = document.getElementById('alertBanner');

    // Open modal on delete click
    document.querySelectorAll('.btn-delete').forEach(function (btn) {
        btn.addEventListener('click', function () {
            selectedUserId = this.dataset.id;
            document.getElementById('userName').innerText = this.dataset.name;
            checkBox.checked = false;
            confirmBtn.disabled = true;
            modal.style.display = 'flex';
        });
    });

    // Enable confirm button only when checkbox checked
    checkBox.addEventListener('change', function () {
        confirmBtn.disabled = !this.checked;
    });

    // Confirm delete
    confirmBtn.addEventListener('click', function () {

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/online_book_store/control/admin_control.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;

            var res;
            try {
                res = JSON.parse(xhr.responseText);
            } catch (e) {
                showAlert('Server error. Please try again.', 'error');
                closeModal();
                return;
            }

            closeModal();

            if (res.success) {
                // Remove row from table without reload
                var row = document.getElementById('row-' + selectedUserId);
                if (row) row.remove();
                showAlert('Customer deleted successfully.', 'success');
            } else {
                showAlert(res.message || 'Delete failed.', 'error');
            }
        };

        xhr.send('action=delete_user&user_id=' + selectedUserId);
    });

    // Logout
    var logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function (e) {
            e.preventDefault();

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/online_book_store/control/login_control.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (xhr.readyState !== 4) return;
                var res;
                try { res = JSON.parse(xhr.responseText); } catch (e) { return; }
                if (res.success) window.location.href = res.redirect;
            };

            xhr.send('action=logout');
        });
    }

    function showAlert(message, type) {
        alertBanner.textContent = message;
        alertBanner.className = 'alert-banner ' + type;
        alertBanner.style.display = 'block';
        setTimeout(function () { alertBanner.style.display = 'none'; }, 4000);
    }

});

function closeModal() {
    document.getElementById('deleteModal').style.display = 'none';
}
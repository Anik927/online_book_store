'use strict';

document.addEventListener('DOMContentLoaded', function () {

    var alertBanner = document.getElementById('alertBanner');

    // ── Status dropdown change ────────────────────────────
    document.querySelectorAll('.status-dropdown').forEach(function (dropdown) {

        dropdown.addEventListener('change', function () {

            var orderId   = this.dataset.id;
            var newStatus = this.value;
            var select    = this;

            select.disabled = true;

            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/online_book_store/control/admin_control.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

            xhr.onreadystatechange = function () {
                if (xhr.readyState !== 4) return;

                select.disabled = false;

                var res;
                try {
                    res = JSON.parse(xhr.responseText);
                } catch (e) {
                    showAlert('Server error.', 'error');
                    return;
                }

                if (res.success) {
                    // Update dropdown color to match new status
                    select.className = 'status-dropdown ' + newStatus;
                    showAlert('Order status updated.', 'success');
                } else {
                    showAlert(res.message || 'Update failed.', 'error');
                }
            };

            xhr.send('action=update_order_status&order_id=' + orderId + '&status=' + newStatus);
        });

        // Set initial color on page load
        dropdown.className = 'status-dropdown ' + dropdown.value;
    });

    // ── Logout ────────────────────────────────────────────
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

    // ── Helper ────────────────────────────────────────────
    function showAlert(message, type) {
        alertBanner.textContent  = message;
        alertBanner.className    = 'alert-banner ' + type;
        alertBanner.style.display = 'block';
        setTimeout(function () { alertBanner.style.display = 'none'; }, 3000);
    }

});
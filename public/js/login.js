'use strict';

document.addEventListener('DOMContentLoaded', function () {

    var form       = document.getElementById('loginForm');
    var emailInput = document.getElementById('email');
    var passInput  = document.getElementById('password');
    var submitBtn  = document.getElementById('submitBtn');
    var alertError = document.getElementById('alertError');
    var togglePass = document.getElementById('togglePass');

    // ── Password show/hide ────────────────────────────────
    togglePass.addEventListener('click', function () {
        passInput.type = passInput.type === 'password' ? 'text' : 'password';
    });

    // ── Form submit ───────────────────────────────────────
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        
        var email    = emailInput.value.trim();
        var password = passInput.value.trim();

        alertError.style.display = 'none';

        // Client-side validation
        if (email === '' || password === '') {
            showAlert('Please fill in all fields.');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            showAlert('Enter a valid email address.');
            return;
        }

        if (password.length < 8) {
            showAlert('Password must be at least 8 characters.');
            return;
        }

        // AJAX
        submitBtn.disabled = true;
        submitBtn.textContent = 'Signing in...';

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/online_book_store/control/login_control.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;

            submitBtn.disabled = false;
            submitBtn.textContent = 'Sign in';

            var res;
            try {
                res = JSON.parse(xhr.responseText);
            } catch (e) {
                showAlert('Unexpected server response.');
                return;
            }

            if (res.success) {
                window.location.href = res.redirect;
            } else {
                showAlert(res.message || 'Login failed. Please try again.');
            }
        };

        xhr.send(
            'action=login'
            + '&email='       + encodeURIComponent(email)
            + '&password='    + encodeURIComponent(password)
            + '&remember_me=' + (document.getElementById('rememberMe').checked ? '1' : '0')
        );
    });

    function showAlert(msg) {
        alertError.textContent    = msg;
        alertError.style.display  = 'block';
    }

});
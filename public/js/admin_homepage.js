'use strict';

document.addEventListener('DOMContentLoaded', function () {

    document.getElementById('logoutBtn').addEventListener('click', function () {

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/online_book_store/control/login_control.php', true);        
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;

            var res;
            try {
                res = JSON.parse(xhr.responseText);
            } catch (e) {
                alert('Logout failed. Please try again.');
                return;
            }

            if (res.success) {
                window.location.href = res.redirect;
            }
        };

        xhr.send('action=logout');
    });

});
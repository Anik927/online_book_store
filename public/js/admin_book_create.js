'use strict';

document.addEventListener('DOMContentLoaded', function () {

    // ── Image preview ─────────────────────────────────────
    var imageInput   = document.getElementById('imageInput');
    var imagePreview = document.getElementById('imagePreview');
    var previewImg   = document.getElementById('previewImg');

    if (imageInput) {
        imageInput.addEventListener('change', function () {
            var file = this.files[0];

            if (!file) {
                imagePreview.style.display = 'none';
                return;
            }

            // Client-side type check
            if (file.type !== 'image/jpeg' && file.type !== 'image/png') {
                alert('Only JPEG or PNG images are allowed.');
                this.value = '';
                imagePreview.style.display = 'none';
                return;
            }

            // Client-side size check (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('Image must be under 2MB.');
                this.value = '';
                imagePreview.style.display = 'none';
                return;
            }

            var reader = new FileReader();
            reader.onload = function (e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        });
    }

    // ── Form validation ───────────────────────────────────
    var form = document.getElementById('bookForm');

    if (form) {
        form.addEventListener('submit', function (e) {

            var title    = form.querySelector('[name="title"]').value.trim();
            var author   = form.querySelector('[name="author"]').value.trim();
            var price    = parseFloat(form.querySelector('[name="price"]').value);
            var stock    = parseInt(form.querySelector('[name="stock"]').value);
            var category = form.querySelector('[name="category_id"]').value;

            if (title === '') {
                alert('Title is required.');
                e.preventDefault(); return;
            }

            if (author === '') {
                alert('Author is required.');
                e.preventDefault(); return;
            }

            if (isNaN(price) || price <= 0) {
                alert('Price must be a positive number.');
                e.preventDefault(); return;
            }

            if (isNaN(stock) || stock < 0) {
                alert('Stock cannot be negative.');
                e.preventDefault(); return;
            }

            if (category === '') {
                alert('Please select a category.');
                e.preventDefault(); return;
            }
        });
    }

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

});
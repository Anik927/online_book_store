// public/js/home.js
// Handles: category filter, add-to-cart, logout

document.addEventListener('DOMContentLoaded', function () {

    // ── Category filter ───────────────────────────────────────────────
    var catBtns   = document.querySelectorAll('.cat-btn');
    var grid      = document.getElementById('books-grid');
    var label     = document.getElementById('result-label');
    var activeId  = 0;

    catBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            var id = parseInt(btn.dataset.id, 10);
            if (id === activeId) return;

            activeId = id;

            catBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            filterBooks(id);
        });
    });

    function filterBooks(categoryId) {
        grid.classList.add('loading');
        label.textContent = 'Loading…';

        var xhr = new XMLHttpRequest();
        xhr.open('GET',
            '/online_book_store/control/home_control.php?action=filter_books&category_id=' + categoryId,
            true);

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4) return;
            grid.classList.remove('loading');

            if (xhr.status !== 200) {
                label.textContent = 'Failed to load books.';
                return;
            }

            var books = JSON.parse(xhr.responseText);
            grid.innerHTML = '';

            if (books.length === 0) {
                grid.innerHTML = '<div class="empty-state"><p>No books found in this category.</p></div>';
                label.textContent = 'No results';
                return;
            }

            books.forEach(function (book, i) {
                var card = buildCard(book);
                card.style.animationDelay = (i * 0.05) + 's';
                grid.appendChild(card);
            });

            label.textContent = 'Showing ' + books.length + ' book' + (books.length !== 1 ? 's' : '');
        };

        xhr.send();
    }


    // ── Build a book card from JSON (mirrors PHP renderBookCard) ──────
    function buildCard(book) {
        var stock  = parseInt(book.stock, 10);
        var price  = parseFloat(book.price).toFixed(2);
        var title  = esc(book.title);
        var author = esc(book.author);
        var cat    = esc(book.category_name || '');
        var img    = book.image_url || '/online_book_store/public/uploads/books/sample.jpg';
        var id     = parseInt(book.id, 10);

        var stockBadge, cartBtn;

        if (stock <= 0) {
            stockBadge = '<span class="stock-badge out">Out of Stock</span>';
            cartBtn    = '<button class="btn-cart" disabled>Out of Stock</button>';
        } else if (stock <= 5) {
            stockBadge = '<span class="stock-badge low">Only ' + stock + ' left</span>';
            cartBtn    = '<button class="btn-cart" data-id="' + id + '">Add to Cart</button>';
        } else {
            stockBadge = '<span class="stock-badge">In Stock</span>';
            cartBtn    = '<button class="btn-cart" data-id="' + id + '">Add to Cart</button>';
        }

        var div = document.createElement('div');
        div.className = 'book-card';
        div.innerHTML =
            '<div class="book-cover-wrap">'
            + '<img src="' + img + '" alt="' + title + '" class="book-cover" loading="lazy">'
            + '<span class="book-cat-tag">' + cat + '</span>'
            + '</div>'
            + '<div class="book-body">'
            + '<div class="book-title">' + title + '</div>'
            + '<div class="book-author">by ' + author + '</div>'
            + '</div>'
            + '<div class="book-footer">'
            + '<span class="book-price">৳' + price + '</span>'
            + stockBadge
            + '</div>'
            + cartBtn;

        // Attach cart listener to the button inside the new card
        var btn = div.querySelector('.btn-cart:not([disabled])');
        if (btn) {
            btn.addEventListener('click', function () {
                addToCart(id);
            });
        }

        return div;
    }


    // ── Add to cart (AJAX) ────────────────────────────────────────────
    window.addToCart = function (bookId) {
        if (!IS_LOGGED_IN) {
            window.location.href = '/online_book_store/view/login_page/login.php';
            return;
        }

        var xhr = new XMLHttpRequest();
        xhr.open('POST', '/online_book_store/control/customer_homepage_control.php?chk_add_cart=true', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function () {
            if (xhr.readyState !== 4 || xhr.status !== 200) return;
            var res = JSON.parse(xhr.responseText);
            if (res.success) {
                showToast('Added to cart!');
                updateCartBadge(1);
            } else {
                showToast(res.message || 'Could not add to cart.', true);
            }
        };

        xhr.send('book_id=' + bookId);
    };


    // ── Cart badge (increment without full reload) ────────────────────
    function updateCartBadge(delta) {
        var badge = document.getElementById('nav-cart-count');
        if (!badge) return;
        badge.textContent = Math.max(0, parseInt(badge.textContent, 10) + delta);
    }


    // ── Logout ────────────────────────────────────────────────────────
    var logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function () {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '/online_book_store/control/login_control.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState !== 4) return;
                var res = JSON.parse(xhr.responseText);
                if (res.redirect) window.location.href = res.redirect;
            };
            xhr.send('action=logout');
        });
    }


    // ── Toast helper ──────────────────────────────────────────────────
    function showToast(message, isError) {
        var wrap  = document.getElementById('toast-wrap');
        var toast = document.createElement('div');
        toast.className = 'toast' + (isError ? ' error' : '');
        toast.textContent = message;
        wrap.appendChild(toast);
        setTimeout(function () { toast.remove(); }, 3000);
    }


    // ── Tiny XSS escape ───────────────────────────────────────────────
    function esc(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }
});
<?php
if (!isset($categories) || !isset($featuredBooks)) {
    header('Location: /online_book_store/control/home_control.php');
    exit;
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin    = $isLoggedIn && $_SESSION['role'] === 'admin';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BookStore — Discover Your Next Read</title>
    <link rel="stylesheet" href="/online_book_store/public/css/home.css">
</head>
<body>

<!-- ══ NAVBAR ══════════════════════════════════════════════════════════ -->
<nav class="navbar">
    <div class="nav-brand">Book<span>Store</span></div>

    <div class="nav-links">
        <a href="/online_book_store/control/home_control.php" class="nav-link active">Home</a>

        <?php if ($isLoggedIn): ?>
            <a href="/online_book_store/view/home_page/customer_homepage.html" class="nav-link">Browse All</a>
            <a href="/online_book_store/view/checkout_page/checkout.html" class="nav-link">Orders</a>

            <?php if ($isAdmin): ?>
                <a href="/online_book_store/control/admin_control.php?action=dashboard"
                   class="nav-link admin-link">⚙ Admin Panel</a>
            <?php endif; ?>
        <?php else: ?>
            <a href="/online_book_store/view/login.php" class="nav-link">Sign In</a>
            <a href="/online_book_store/view/register.php" class="nav-link">Register</a>
        <?php endif; ?>
    </div>

    <?php if ($isLoggedIn): ?>
        <button class="nav-cart" onclick="window.location.href='/online_book_store/view/home_page/customer_homepage.html#cart'">
            🛒 Cart
            <span class="cart-badge" id="nav-cart-count"><?= $cartCount ?></span>
        </button>

        <span class="nav-user">
            👤 <strong><?= htmlspecialchars($_SESSION['name']) ?></strong>
        </span>

        <button class="btn-logout" id="logoutBtn">Logout</button>
    <?php endif; ?>
</nav>


<!-- ══ HERO ════════════════════════════════════════════════════════════ -->
<section class="hero">
    <div class="hero-inner">
        <p class="hero-eyebrow">Welcome to BookStore</p>
        <h1 class="hero-title">
            Every great story<br>begins with <em>a page.</em>
        </h1>
        <p class="hero-sub">
            Explore our curated collection — from timeless classics to the
            latest releases. Find your next obsession today.
        </p>
        <div class="hero-actions">
            <?php if ($isLoggedIn): ?>
                <button class="btn-primary" onclick="document.getElementById('featured').scrollIntoView({behavior:'smooth'})">
                    Browse Books
                </button>
                <button class="btn-ghost" onclick="window.location.href='/online_book_store/view/home_page/customer_homepage.html'" hidden >
                    Go to Shop →
                </button>
            <?php else: ?>
                <a href="/online_book_store/view/register/register.php" class="btn-primary">Get Started</a>
                <a href="/online_book_store/view/login_page/login.php" class="btn-ghost">Sign In →</a>
            <?php endif; ?>
        </div>
    </div>
</section>


<!-- ══ MAIN BODY ════════════════════════════════════════════════════════ -->
<div class="page-body">

    <!-- ── Category Sidebar ──────────────────────────────────────────── -->
    <aside class="sidebar">
        <p class="sidebar-title">Browse by genre</p>
        <div class="category-list">

            <button class="cat-btn active" data-id="0">All Books</button>

            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                <button class="cat-btn" data-id="<?= (int)$cat['id'] ?>">
                    <?= htmlspecialchars($cat['name']) ?>
                </button>
            <?php endwhile; ?>

        </div>
    </aside>


    <!-- ── Featured Books ────────────────────────────────────────────── -->
    <main id="featured">
        <div class="section-header">
            <h2 class="section-title">Featured Books</h2>
            <span class="section-label" id="result-label">
                Showing latest arrivals
            </span>
        </div>

        <div class="books-grid" id="books-grid">

            <?php if (mysqli_num_rows($featuredBooks) > 0): ?>
                <?php while ($book = mysqli_fetch_assoc($featuredBooks)): ?>
                    <?= renderBookCard($book) ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="empty-state">
                    <p>No books available right now. Check back soon!</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

</div><!-- /page-body -->


<!-- ── Toast container ────────────────────────────────────────────────── -->
<div class="toast-wrap" id="toast-wrap"></div>


<?php
// ── Helper: render a single book card (used for both PHP and JS template)
function renderBookCard($book) {
    $imgUrl  = !empty($book['image_path'])
               ? '/online_book_store/public/uploads/books/' . htmlspecialchars($book['image_path'])
               : '/online_book_store/public/uploads/books/sample.jpg';
    $price   = number_format((float)$book['price'], 2);
    $stock   = (int)$book['stock'];
    $catName = htmlspecialchars($book['category_name'] ?? '');
    $title   = htmlspecialchars($book['title']);
    $author  = htmlspecialchars($book['author']);
    $id      = (int)$book['id'];

    if ($stock <= 0) {
        $stockBadge  = '<span class="stock-badge out">Out of Stock</span>';
        $cartBtn     = '<button class="btn-cart" disabled>Out of Stock</button>';
    } elseif ($stock <= 5) {
        $stockBadge  = '<span class="stock-badge low">Only ' . $stock . ' left</span>';
        $cartBtn     = '<button class="btn-cart" onclick="addToCart(' . $id . ')">Add to Cart</button>';
    } else {
        $stockBadge  = '<span class="stock-badge">In Stock</span>';
        $cartBtn     = '<button class="btn-cart" onclick="addToCart(' . $id . ')">Add to Cart</button>';
    }

    return '
    <div class="book-card">
        <div class="book-cover-wrap">
            <img src="' . $imgUrl . '" alt="' . $title . '" class="book-cover" loading="lazy">
            <span class="book-cat-tag">' . $catName . '</span>
        </div>
        <div class="book-body">
            <div class="book-title">' . $title . '</div>
            <div class="book-author">by ' . $author . '</div>
        </div>
        <div class="book-footer">
            <span class="book-price">৳' . $price . '</span>
            ' . $stockBadge . '
        </div>
        ' . $cartBtn . '
    </div>';
}
?>

<script>
    // Pass session state to JS
    var IS_LOGGED_IN = <?= $isLoggedIn ? 'true' : 'false' ?>;
</script>
<script src="/online_book_store/public/js/home.js"></script>
</body>
</html>
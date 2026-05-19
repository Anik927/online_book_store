<?php


if (session_status() === PHP_SESSION_NONE) session_start();

require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/../model/home_model.php';

$action = $_GET['action'] ?? '';

// ── AJAX: filter books by category ─────────────────────────────────────────
if ($action === 'filter_books') {
    header('Content-Type: application/json');

    $categoryId = (int)($_GET['category_id'] ?? 0);
    $result     = getFeaturedBooks($conn, $categoryId, 8);

    $books = [];
    while ($row = mysqli_fetch_assoc($result)) {
        // Build the image URL the view already uses
        $row['image_url'] = !empty($row['image_path'])
            ? '/online_book_store/public/uploads/books/' . $row['image_path']
            : '/online_book_store/public/uploads/books/sample.jpg';
        $books[] = $row;
    }

    echo json_encode($books);
    exit;
}

// ── Normal page load ────────────────────────────────────────────────────────
$categories   = getHomeCategories($conn);       // for the left panel
$featuredBooks = getFeaturedBooks($conn, 0, 8); // initial load — all, latest 8
$cartCount    = 0;

if (isset($_SESSION['user_id'])) {
    $cartCount = getCartCount($conn, $_SESSION['user_id']);
}

// Pass data to the view
require __DIR__ . '/../view/home.php';
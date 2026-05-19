<?php



// Get all categories (for the filter panel)
function getHomeCategories($conn) {
    $sql = "SELECT id, name FROM categories ORDER BY name ASC";
    return mysqli_query($conn, $sql);
}


// Get featured books — latest 8, or filtered by category
// $categoryId = 0 means "all categories"
function getFeaturedBooks($conn, $categoryId = 0, $limit = 8) {
    $limit      = (int)$limit;
    $categoryId = (int)$categoryId;

    if ($categoryId > 0) {
        $stmt = mysqli_prepare($conn, "
            SELECT books.*, categories.name AS category_name
            FROM books
            LEFT JOIN categories ON books.category_id = categories.id
            WHERE books.category_id = ? AND books.stock > 0
            ORDER BY books.created_at DESC
            LIMIT ?
        ");
        mysqli_stmt_bind_param($stmt, 'ii', $categoryId, $limit);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        return $result;
    }

    // No filter — return latest books
    $sql = "
        SELECT books.*, categories.name AS category_name
        FROM books
        LEFT JOIN categories ON books.category_id = categories.id
        WHERE books.stock > 0
        ORDER BY books.created_at DESC
        LIMIT $limit
    ";
    return mysqli_query($conn, $sql);
}


// Get cart item count for the nav badge
function getCartCount($conn, $userId) {
    $userId = (int)$userId;
    $row = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT COALESCE(SUM(quantity), 0) AS total FROM cart WHERE user_id = $userId")
    );
    return (int)($row['total'] ?? 0);
}
<?php



// Get all books with category name
function getAllBooks($conn) {
    $sql = "
        SELECT books.*, categories.name AS category_name
        FROM books
        LEFT JOIN categories ON books.category_id = categories.id
        ORDER BY books.created_at DESC
    ";
    return mysqli_query($conn, $sql);
}


// Get single book by ID
function getBookById($conn, $id) {
    $stmt = mysqli_prepare($conn, "SELECT * FROM books WHERE id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, 'i', $id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $book   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return $book;
}


// Get all categories for dropdown
function getAllCategories($conn) {
    return mysqli_query($conn, "SELECT * FROM categories ORDER BY name ASC");
}


// Create a new book
function createBook($conn, $data) {
    $stmt = mysqli_prepare($conn, "
        INSERT INTO books (title, author, description, price, category_id, image_path, stock, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
    ");
        mysqli_stmt_bind_param($stmt, 'sssdisi',
        $data['title'],
        $data['author'],
        $data['description'],
        $data['price'],
        $data['category_id'],
        $data['image_path'],
        $data['stock']
    );
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}


// Update an existing book
function updateBook($conn, $data) {
    $stmt = mysqli_prepare($conn, "
        UPDATE books
        SET title = ?, author = ?, description = ?, price = ?, category_id = ?, image_path = ?, stock = ?
        WHERE id = ?
    ");
        mysqli_stmt_bind_param($stmt, 'sssdisii',
        $data['title'],
        $data['author'],
        $data['description'],
        $data['price'],
        $data['category_id'],
        $data['image_path'],
        $data['stock'],
        $data['id']        // WHERE id = ?
    );
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $ok;
}


// Check if book has any pending orders — block delete if true
function bookHasPendingOrders($conn, $bookId) {
    $stmt = mysqli_prepare($conn, "
        SELECT COUNT(*) AS total
        FROM order_items oi
        INNER JOIN orders o ON oi.order_id = o.id
        WHERE oi.book_id = ? AND o.status = 'pending'
    ");
    mysqli_stmt_bind_param($stmt, 'i', $bookId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row    = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    return (int)$row['total'] > 0;
}


// Delete a book — blocked if pending orders exist
function deleteBook($conn, $bookId) {

    // Block if pending orders
    if (bookHasPendingOrders($conn, $bookId)) {
        echo json_encode([
            'success' => false,
            'message' => 'Cannot delete — this book has pending orders. Process or cancel them first.'
        ]);
        exit;
    }

    // Get image path before deleting
    $book = getBookById($conn, $bookId);

    $stmt = mysqli_prepare($conn, "DELETE FROM books WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $bookId);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    // Delete image file if exists
    if ($affected > 0 && !empty($book['image_path'])) {
        $imagePath = __DIR__ . '/../public/uploads/books/' . $book['image_path'];
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }
    }

    echo json_encode(['success' => $affected > 0]);
    exit;
}
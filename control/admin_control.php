<?php
session_start();
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/../model/book_model.php';
require_once __DIR__ . '/../model/user_model.php';
require_once __DIR__ . '/../model/order_model.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // create_book and edit_book are multipart (file upload), not JSON
    if ($action === 'create_book' || $action === 'edit_book') {
        handleBookForm($conn, $action);
        exit;
    }

    header('Content-Type: application/json');

    if ($action === 'delete_book') {
        $bookId = (int)($_POST['book_id'] ?? 0);
        if ($bookId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid book.']);
            exit;
        }
        deleteBook($conn, $bookId);

    } elseif ($action === 'delete_user') {
        $userId = (int)($_POST['user_id'] ?? 0);
        if ($userId <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid user.']);
            exit;
        }
        $deleted = deleteCustomer($conn, $userId);
        echo json_encode(['success' => $deleted, 'message' => $deleted ? '' : 'Delete failed.']);
        exit;

    } elseif ($action === 'update_order_status') {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status  = $_POST['status'] ?? '';
        if ($orderId <= 0 || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'Invalid data.']);
            exit;
        }
        $updated = updateOrderStatus($conn, $orderId, $status);
        echo json_encode(['success' => $updated, 'message' => $updated ? '' : 'Update failed.']);
        exit;

    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid action.']);
        exit;
    }

} else {

    if ($action === 'books') {
        showBooks();
    } elseif ($action === 'create_book') {
        showCreateBook();
    } elseif ($action === 'edit_book') {
        showEditBook();
    } elseif ($action === 'users') {
        showUsers();
    } elseif ($action === 'orders') {
        showOrders();
    } else {
        showDashboard();
    }
}


// ============================================
// BOOK FORM HANDLER (create + edit)
// ============================================
function handleBookForm($conn, $action) {

    $errors = [];

    $title       = trim($_POST['title']       ?? '');
    $author      = trim($_POST['author']      ?? '');
    $description = trim($_POST['description'] ?? '');
    $price       = $_POST['price']            ?? '';
    $category_id = (int)($_POST['category_id'] ?? 0);
    $stock       = (int)($_POST['stock']       ?? 0);
    $bookId      = (int)($_POST['book_id']     ?? 0);

    // Validation
    if ($title === '')       $errors[] = 'Title is required.';
    if ($author === '')      $errors[] = 'Author is required.';
    if ($description === '') $errors[] = 'Description is required.';
    if (!is_numeric($price) || (float)$price <= 0) $errors[] = 'Price must be a positive number.';
    if ($category_id <= 0)  $errors[] = 'Please select a category.';
    if ($stock < 0)         $errors[] = 'Stock cannot be negative.';

    // Image upload
    $imagePath = $_POST['existing_image'] ?? '';

    if (!empty($_FILES['image']['name'])) {
        $file    = $_FILES['image'];
        $allowed = ['image/jpeg', 'image/png'];
        $maxSize = 2 * 1024 * 1024;

        if (!in_array($file['type'], $allowed)) {
            $errors[] = 'Image must be JPEG or PNG.';
        } elseif ($file['size'] > $maxSize) {
            $errors[] = 'Image must be under 2MB.';
        } else {
            $ext       = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename  = uniqid('book_') . '.' . $ext;
            $uploadDir = __DIR__ . '/../public/uploads/books/';

            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
                $errors[] = 'Image upload failed.';
            } else {
                if ($action === 'edit_book' && !empty($imagePath)) {
                    $oldPath = $uploadDir . $imagePath;
                    if (file_exists($oldPath)) unlink($oldPath);
                }
                $imagePath = $filename;
            }
        }
    }

    if (!empty($errors)) {
        $query = '?action=' . ($action === 'create_book' ? 'create_book' : 'edit_book&id=' . $bookId);
        $query .= '&error=' . urlencode(implode(' ', $errors));
        header('Location: /online_book_store/control/admin_control.php' . $query);
        exit;
    }

    $data = [
        'title'       => $title,
        'author'      => $author,
        'description' => $description,
        'price'       => (float)$price,
        'category_id' => $category_id,
        'image_path'  => $imagePath,
        'stock'       => $stock,
    ];

    if ($action === 'create_book') {
        createBook($conn, $data);
        header('Location: /online_book_store/control/admin_control.php?action=books&success=' . urlencode('Book added successfully.'));
    } else {
        $data['id'] = $bookId;
        updateBook($conn, $data);
        header('Location: /online_book_store/control/admin_control.php?action=books&success=' . urlencode('Book updated successfully.'));
    }
    exit;
}


// ============================================
// SHOW FUNCTIONS
// ============================================
function showDashboard() {
    global $conn;

    $stats = [];
    $stats['books']     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM books"))['total'];
    $stats['customers'] = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'"))['total'];
    $stats['orders']    = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM orders"))['total'];
    $stats['revenue']   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COALESCE(SUM(total_amount), 0) AS total FROM orders WHERE status != 'pending'"))['total'];

    require __DIR__ . '/../view/admin_homepage.php';
}

function showBooks() {
    global $conn;
    $result = getAllBooks($conn);
    require __DIR__ . '/../view/admin_book_index.php';
}

function showCreateBook() {
    global $conn;
    $categories = getAllCategories($conn);
    require __DIR__ . '/../view/admin_book_create.php';
}

function showEditBook() {
    global $conn;
    $bookId = (int)($_GET['id'] ?? 0);
    if ($bookId <= 0) {
        header('Location: /online_book_store/control/admin_control.php?action=books');
        exit;
    }
    $book       = getBookById($conn, $bookId);
    $categories = getAllCategories($conn);
    if (!$book) {
        header('Location: /online_book_store/control/admin_control.php?action=books&error=' . urlencode('Book not found.'));
        exit;
    }
    require __DIR__ . '/../view/admin_book_edit.php';
}

function showUsers() {
    global $conn;
    $result = getAllUsers($conn);
    require __DIR__ . '/../view/admin_users.php';
}

function showOrders() {
    global $conn;
    $status = $_GET['status'] ?? '';
    $date   = $_GET['date']   ?? '';
    $result = getAllOrders($conn, $status, $date);
    require __DIR__ . '/../view/admin_orders.php';
}
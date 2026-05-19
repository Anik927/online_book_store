<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/db_config.php';
require_once __DIR__ . '/../model/customer_homepage_model.php';


if (isset($_GET['chk_fetch'])) {
    $result = allBooks($conn);
    $arr = [];
    
    
    if ($result) {
        foreach ($result as $rows) {
            array_push($arr, $rows);
        }
    }
    
    
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit; 
}


if (isset($_GET['chk_search'])) {
    
    $query = isset($_GET['query']) ? $_GET['query'] : '';
    $filter = isset($_GET['filter']) ? $_GET['filter'] : 'title'; // default title

    
    $result = searchBook($conn, $query, $filter);
    $arr = [];

    if ($result) {
        foreach ($result as $rows) {
            array_push($arr, $rows);
        }
    }

   
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}


if (isset($_GET['chk_fetch_cart'])) {
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;

    $result = getCartItems($conn, $user_id);
    $arr = [];

    if ($result) {
        while ($rows = mysqli_fetch_assoc($result)) {
            array_push($arr, $rows);
        }
    }

    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}


if (isset($_GET['chk_add_cart'])) {
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
    $book_id = isset($_POST['book_id']) ? (int)$_POST['book_id'] : 0;

    if ($book_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Invalid book.']);
        exit;
    }

    // Stock check
    $stockResult = mysqli_query($conn, "SELECT stock FROM books WHERE id = $book_id");
    $book = mysqli_fetch_assoc($stockResult);

    if (!$book || $book['stock'] <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Out of stock.']);
        exit;
    }

    
    $checkResult = mysqli_query($conn, "SELECT id, quantity FROM cart WHERE user_id = $user_id AND book_id = $book_id");
    $existing = mysqli_fetch_assoc($checkResult);

    if ($existing) {
        
        $newQty = $existing['quantity'] + 1;
        mysqli_query($conn, "UPDATE cart SET quantity = $newQty WHERE id = " . $existing['id']);
    } else {
        // না থাকলে নতুন insert
        mysqli_query($conn, "INSERT INTO cart (user_id, book_id, quantity) VALUES ($user_id, $book_id, 1)");
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}


if (isset($_GET['chk_update_cart'])) {
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
    $action  = isset($_POST['action'])  ? $_POST['action'] : '';

    if ($cart_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
        exit;
    }

    $cartRow = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM cart WHERE id = $cart_id AND user_id = $user_id"));

    if (!$cartRow) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
        exit;
    }

    if ($action === 'plus') {
        $newQty = $cartRow['quantity'] + 1;
        mysqli_query($conn, "UPDATE cart SET quantity = $newQty WHERE id = $cart_id");
    } else if ($action === 'minus') {
        if ($cartRow['quantity'] <= 1) {
            
            mysqli_query($conn, "DELETE FROM cart WHERE id = $cart_id");
        } else {
            $newQty = $cartRow['quantity'] - 1;
            mysqli_query($conn, "UPDATE cart SET quantity = $newQty WHERE id = $cart_id");
        }
    }

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}


if (isset($_GET['chk_remove_cart'])) {
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;
    $cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;

    if ($cart_id <= 0) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false]);
        exit;
    }

    mysqli_query($conn, "DELETE FROM cart WHERE id = $cart_id AND user_id = $user_id");

    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
    exit;
}




if (isset($_GET['chk_fetch_orders'])) {
    $user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 1;

    $result = getOrderHistory($conn, $user_id);
    $arr = [];

    if ($result) {
        while ($rows = mysqli_fetch_assoc($result)) {
            array_push($arr, $rows);
        }
    }

    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}



?>
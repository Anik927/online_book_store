<?php

require_once __DIR__ . '/../model/checkout_model.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/db_config.php';

// Login check
if (!isset($_SESSION['user_id'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Please login first.']);
    exit;
}


if (isset($_GET['chk_place_order'])) {
    $user_id        = (int)$_SESSION['user_id'];
    $address        = isset($_POST['address'])        ? trim($_POST['address'])        : '';
    $payment_method = isset($_POST['payment_method']) ? trim($_POST['payment_method']) : '';

    // PHP Validation
    if (empty($address)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Address is required.']);
        exit;
    }

    if (empty($payment_method)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Payment method is required.']);
        exit;
    }

    
    $cartResult = mysqli_query($conn, 
        "SELECT cart.*, books.price 
         FROM cart 
         INNER JOIN books ON cart.book_id = books.id 
         WHERE cart.user_id = $user_id"
    );

    $cartItems = [];
    while ($row = mysqli_fetch_assoc($cartResult)) {
        $cartItems[] = $row;
    }

    
    if (empty($cartItems)) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Your cart is empty.']);
        exit;
    }

    
    $total_amount = 0;
    foreach ($cartItems as $item) {
        $total_amount += $item['price'] * $item['quantity'];
    }

    
    $payment_method_escaped = mysqli_real_escape_string($conn, $payment_method);
    $address_escaped        = mysqli_real_escape_string($conn, $address);

    mysqli_query($conn, 
        "INSERT INTO orders (user_id, total_amount, status, payment_method) 
         VALUES ($user_id, $total_amount, 'pending', '$payment_method_escaped')"
    );

    $order_id = mysqli_insert_id($conn);

    
    foreach ($cartItems as $item) {
        $book_id    = (int)$item['book_id'];
        $quantity   = (int)$item['quantity'];
        $unit_price = (float)$item['price'];

        mysqli_query($conn, 
            "INSERT INTO order_items (order_id, book_id, quantity, unit_price) 
             VALUES ($order_id, $book_id, $quantity, $unit_price)"
        );
    }

    $transaction_id = 'TXN' . time() . $user_id;

    mysqli_query($conn, 
        "INSERT INTO payments (order_id, amount, payment_method, transaction_id) 
         VALUES ($order_id, $total_amount, '$payment_method_escaped', '$transaction_id')"
    );

    
    mysqli_query($conn, "DELETE FROM cart WHERE user_id = $user_id");

    
    mysqli_query($conn, 
        "UPDATE users SET address = '$address_escaped' WHERE id = $user_id"
    );

    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'order_id' => $order_id]);
    exit;
}
?>
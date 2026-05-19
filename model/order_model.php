<?php



// Get all orders with customer name, total, status, payment, date
function getAllOrders($conn, $status = '', $date = '') {

    $sql = "
        SELECT 
            o.id,
            o.total_amount,
            o.status,
            o.payment_method,
            o.order_date,
            u.name AS customer_name,
            u.email AS customer_email,
            GROUP_CONCAT(b.title ORDER BY b.title SEPARATOR ', ') AS book_titles
        FROM orders o
        LEFT JOIN users u ON o.user_id = u.id
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN books b ON oi.book_id = b.id
    ";

    $conditions = [];
    $params     = [];
    $types      = '';

    if (!empty($status)) {
        $conditions[] = "o.status = ?";
        $params[]     = $status;
        $types       .= 's';
    }

    if (!empty($date)) {
        $conditions[] = "DATE(o.order_date) = ?";
        $params[]     = $date;
        $types       .= 's';
    }

    if (!empty($conditions)) {
        $sql .= " WHERE " . implode(' AND ', $conditions);
    }

    $sql .= " GROUP BY o.id ORDER BY o.order_date DESC";

    if (!empty($params)) {
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, $types, ...$params);
        mysqli_stmt_execute($stmt);
        return mysqli_stmt_get_result($stmt);
    }

    return mysqli_query($conn, $sql);
}


// Update order status
function updateOrderStatus($conn, $orderId, $status) {
    $allowed = ['pending', 'confirmed', 'shipped', 'delivered'];

    if (!in_array($status, $allowed)) {
        return false;
    }

    $stmt = mysqli_prepare($conn, "UPDATE orders SET status = ? WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'si', $status, $orderId);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    return $affected >= 0;
}
<?php

// ============================================
// model/user_model.php
// All database queries related to users
// ============================================


// Get all users (admin + customer)
function getAllUsers($conn) {
    $sql = "SELECT id, name, email, role, created_at FROM users ORDER BY created_at DESC";
    return mysqli_query($conn, $sql);
}


// Delete a customer and cascade their cart and orders
function deleteCustomer($conn, $userId) {

    // Delete cart items
    $stmt = mysqli_prepare($conn, "DELETE FROM cart WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete order items linked to user's orders
    $stmt = mysqli_prepare($conn, "DELETE oi FROM order_items oi 
                                   INNER JOIN orders o ON oi.order_id = o.id 
                                   WHERE o.user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete orders
    $stmt = mysqli_prepare($conn, "DELETE FROM orders WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Delete user
    $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ? AND role = 'customer'");
    mysqli_stmt_bind_param($stmt, 'i', $userId);
    mysqli_stmt_execute($stmt);
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    return $affected > 0;
}
<?php


function allBooks($conn) {
    // categories টেবিলের সাথে JOIN করা হয়েছে যাতে ক্যাটাগরির নামও একসাথে আনা যায়
    $qry = "SELECT books.*, categories.name AS category_name 
            FROM books 
            LEFT JOIN categories ON books.category_id = categories.id
            ORDER BY books.id DESC";
            
    return mysqli_query($conn, $qry);
}


function searchBook($conn, $query, $filter) {
    
    $query = mysqli_real_escape_string($conn, $query);
    $filter = mysqli_real_escape_string($conn, $filter);
    
    
    if ($filter === 'category') {
       
        $qry = "SELECT books.*, categories.name AS category_name 
                FROM books 
                LEFT JOIN categories ON books.category_id = categories.id 
                WHERE categories.name LIKE '%$query%'";
    } else if ($filter === 'author') {
        
        $qry = "SELECT books.*, categories.name AS category_name 
                FROM books 
                LEFT JOIN categories ON books.category_id = categories.id 
                WHERE books.author LIKE '%$query%'";
    } else {
        
        $qry = "SELECT books.*, categories.name AS category_name 
                FROM books 
                LEFT JOIN categories ON books.category_id = categories.id 
                WHERE books.title LIKE '%$query%'";
    }
    
    return mysqli_query($conn, $qry);
}



function getCartItems($conn, $user_id) {
    $user_id = (int)$user_id;

    $qry = "SELECT cart.id, cart.quantity, books.title, books.price 
            FROM cart 
            INNER JOIN books ON cart.book_id = books.id 
            WHERE cart.user_id = $user_id";

    return mysqli_query($conn, $qry);
}


function getOrderHistory($conn, $user_id) {
    $user_id = (int)$user_id;

    $qry = "SELECT 
                orders.id,
                orders.total_amount,
                orders.status,
                orders.payment_method,
                orders.order_date,
                GROUP_CONCAT(books.title SEPARATOR ', ') AS book_titles
            FROM orders
            INNER JOIN order_items ON orders.id = order_items.order_id
            INNER JOIN books ON order_items.book_id = books.id
            WHERE orders.user_id = $user_id
            GROUP BY orders.id
            ORDER BY orders.order_date DESC";

    return mysqli_query($conn, $qry);
}

?>
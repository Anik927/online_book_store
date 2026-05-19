<?php
// session should already be started in controller or bootstrap
if (!isset($_SESSION)) {
    session_start();
}

// Admin access control (OK to keep in view if no middleware exists)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login_page/login.php");
    exit;
}

// safety: ensure $result exists
if (!isset($result)) {
    $result = null;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Management | BookStore Admin</title>    
    <link rel="stylesheet" href="/online_book_store/public/css/admin_book_index.css?v=2">
</head>
<body>

<div class="wrapper">

    <!-- Topbar -->
    <div class="topbar">
    <h1>BookStore <span>Admin</span></h1>

        <div class="user-info">
            👤 <?= htmlspecialchars($_SESSION['name']) ?>
            &nbsp;|&nbsp;

            <a href="#" id="logoutBtn" class="logout-btn">
            Logout
            </a>
        </div>
    </div>

    <div class="content">

        <!-- Header row -->
        <div class="page-header">
            <h2>Book Management</h2>
            <a href="/online_book_store/control/admin_control.php?action=create_book" class="btn-add">+ Add New Book</a>
            <a href="/online_book_store/control/admin_control.php?action=dashboard" class="btn-back">← Back To Dashboard</a>


        </div>

        <!-- Success/Error messages -->
        <?php if (isset($_GET['success'])): ?>
        <div class="alert success"><?= htmlspecialchars($_GET['success']) ?></div>
        <?php endif; ?>
        <?php if (isset($_GET['error'])): ?>
        <div class="alert error"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>

        <!-- Books Table -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Actions</th>
                    </tr>
                </thead>
               <tbody>

            <?php if ($result && mysqli_num_rows($result) > 0): ?>

                <?php $i = 1; while ($book = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $i++ ?></td>

                        <td>
                            <?php if (!empty($book['image_path'])): ?>
                                <img src="/online_book_store/public/uploads/books/<?= htmlspecialchars($book['image_path']) ?>" 
                                alt="cover" class="book-thumb">
                            <?php else: ?>
                                <div class="no-img">No Image</div>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($book['title']) ?></td>
                        <td><?= htmlspecialchars($book['author']) ?></td>
                        <td><?= htmlspecialchars($book['category_name']) ?></td>
                        <td>৳<?= number_format((float)$book['price'], 2) ?></td>
                        <td><?= (int)$book['stock'] ?></td>

                        <td>
                            <div class="actions">
                                <a href="/online_book_store/control/admin_control.php?action=edit_book&id=<?= (int)$book['id'] ?>" class="btn-edit">Edit</a>
                                <button class="btn-delete"
                                    data-id="<?= (int)$book['id'] ?>"
                                    data-title="<?= htmlspecialchars($book['title']) ?>">
                                    Delete
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="8" class="empty">
                        No books found. <a href="create.php">Add one?</a>
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">

        <h3>Delete Book</h3>

        <p>
            Are you sure you want to delete 
            <strong id="bookTitle"></strong>?
        </p>

        <p class="modal-warning">
            This action cannot be undone.
        </p>

        <!-- CONFIRM SAFETY CHECK -->
        <label style="font-size:13px; display:block; margin-top:10px;">
            <input type="checkbox" id="confirmCheck">
            I understand this cannot be undone
        </label>

        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>

            <button class="btn-confirm-delete" id="confirmDeleteBtn" disabled>
                Yes, Delete
            </button>
        </div>

    </div>
</div>

<script src="/online_book_store/public/js/admin_book_index.js"></script>

</body>
</html>
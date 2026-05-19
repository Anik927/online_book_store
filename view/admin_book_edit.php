<?php
if (session_status() === PHP_SESSION_NONE) session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login_page/login.php'); exit;
}
$error = isset($_GET['error']) ? htmlspecialchars(urldecode($_GET['error'])) : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Book | BookStore Admin</title>
    <link rel="stylesheet" href="/online_book_store/public/css/admin_book_create.css">
</head>
<body>

<div class="wrapper">

    <div class="topbar">
        <h1>BookStore <span>Admin</span></h1>
        <div class="user-info">
            👤 <?= htmlspecialchars($_SESSION['name']) ?>
            &nbsp;|&nbsp;
            <a href="#" id="logoutBtn" class="logout-btn">Logout</a>
        </div>
    </div>

    <div class="content">

        <div class="page-header">
            <h2>Edit Book</h2>
            <a href="/online_book_store/control/admin_control.php?action=books" class="btn-back">← Back to Books</a>
        </div>

        <?php if ($error): ?>
            <div class="alert error"><?= $error ?></div>
        <?php endif; ?>

        <div class="form-box">
            <form action="/online_book_store/control/admin_control.php" method="POST" enctype="multipart/form-data" id="bookForm">
                <input type="hidden" name="action"         value="edit_book">
                <input type="hidden" name="book_id"        value="<?= (int)$book['id'] ?>">
                <input type="hidden" name="existing_image" value="<?= htmlspecialchars($book['image_path'] ?? '') ?>">

                <div class="field">
                    <label>Title</label>
                    <input type="text" name="title" value="<?= htmlspecialchars($book['title']) ?>" required>
                </div>

                <div class="field">
                    <label>Author</label>
                    <input type="text" name="author" value="<?= htmlspecialchars($book['author']) ?>" required>
                </div>

                <div class="field">
                    <label>Description</label>
                    <textarea name="description" rows="4" required><?= htmlspecialchars($book['description']) ?></textarea>
                </div>

                <div class="field-row">
                    <div class="field">
                        <label>Price (৳)</label>
                        <input type="number" name="price" min="0.01" step="0.01"
                               value="<?= htmlspecialchars($book['price']) ?>" required>
                    </div>
                    <div class="field">
                        <label>Stock</label>
                        <input type="number" name="stock" min="0"
                               value="<?= (int)$book['stock'] ?>" required>
                    </div>
                </div>

                <div class="field">
                    <label>Category</label>
                    <select name="category_id" required>
                        <option value="">Select category</option>
                        <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                            <option value="<?= $cat['id'] ?>"
                                <?= $cat['id'] == $book['category_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="field">
                    <label>Cover Image <span class="hint">(Leave empty to keep current)</span></label>

                    <?php if (!empty($book['image_path'])): ?>
                        <div class="current-image">
                            <img src="/online_book_store/public/uploads/books/<?= htmlspecialchars($book['image_path']) ?>"
                                 alt="Current cover">
                            <span>Current image</span>
                        </div>
                    <?php endif; ?>

                    <input type="file" name="image" accept="image/jpeg,image/png" id="imageInput">
                    <div class="image-preview" id="imagePreview" style="display:none;">
                        <img id="previewImg" src="" alt="New preview">
                    </div>
                </div>

                <div class="form-actions">
                    <a href="/online_book_store/control/admin_control.php?action=books" class="btn-cancel">Cancel</a>
                    <button type="submit" class="btn-submit">Save Changes</button>
                </div>

            </form>
        </div>
    </div>
</div>

<script src="/online_book_store/public/js/admin_book_edit.js"></script>
</body>
</html>
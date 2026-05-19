<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login_page/login.php');
    exit;
}

if (!isset($result)) $result = null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Users | BookStore Admin</title>
    <link rel="stylesheet" href="/online_book_store/public/css/admin_users.css">
</head>
<body>

<div class="wrapper">

    <!-- Topbar -->
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
            <h2>All Users</h2>
            <a href="/online_book_store/control/admin_control.php?action=dashboard" class="btn-back">← Back to Dashboard</a>
        </div>

        <!-- Alert banner for AJAX messages -->
        <div class="alert-banner" id="alertBanner" style="display:none;"></div>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Registered</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>

                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php $i = 1; while ($user = mysqli_fetch_assoc($result)): ?>
                    <tr id="row-<?= $user['id'] ?>">
                        <td><?= $i++ ?></td>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge <?= $user['role'] ?>">
                                <?= ucfirst($user['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d M Y', strtotime($user['created_at'])) ?></td>
                        <td>
                            <?php if ($user['role'] === 'customer'): ?>
                                <button class="btn-delete"
                                    data-id="<?= (int)$user['id'] ?>"
                                    data-name="<?= htmlspecialchars($user['name']) ?>">
                                    Delete
                                </button>
                            <?php else: ?>
                                <span class="no-action">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="empty">No users found.</td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- Delete Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal">
        <h3>Delete Customer</h3>
        <p>Are you sure you want to delete <strong id="userName"></strong>?</p>
        <p class="modal-warning">This will also delete their cart and orders. This cannot be undone.</p>
        <label style="font-size:13px; display:block; margin-top:10px;">
            <input type="checkbox" id="confirmCheck">
            I understand this cannot be undone
        </label>
        <div class="modal-actions">
            <button class="btn-cancel" onclick="closeModal()">Cancel</button>
            <button class="btn-confirm-delete" id="confirmDeleteBtn" disabled>Yes, Delete</button>
        </div>
    </div>
</div>

<script src="/online_book_store/public/js/admin_users.js"></script>
</body>
</html>
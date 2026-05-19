<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login_page/login.php');
    exit;
}

if (!isset($result)) $result = null;

$filterStatus = $_GET['status'] ?? '';
$filterDate   = $_GET['date']   ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase History | BookStore Admin</title>
    <link rel="stylesheet" href="/online_book_store/public/css/admin_orders.css">
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
            <h2>Purchase History</h2>
            <a href="/online_book_store/control/admin_control.php?action=dashboard" class="btn-back">← Back to Dashboard</a>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET" action="/online_book_store/control/admin_control.php">
                <input type="hidden" name="action" value="orders">

                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All</option>
                        <option value="pending"   <?= $filterStatus === 'pending'   ? 'selected' : '' ?>>Pending</option>
                        <option value="confirmed" <?= $filterStatus === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                        <option value="shipped"   <?= $filterStatus === 'shipped'   ? 'selected' : '' ?>>Shipped</option>
                        <option value="delivered" <?= $filterStatus === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label>Date</label>
                    <input type="date" name="date" value="<?= htmlspecialchars($filterDate) ?>">
                </div>

                <button type="submit" class="btn-filter">Apply</button>

                <?php if ($filterStatus || $filterDate): ?>
                    <a href="/online_book_store/control/admin_control.php?action=orders" class="btn-clear">Clear</a>
                <?php endif; ?>

            </form>
        </div>

        <!-- Table -->
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Customer</th>
                        <th>Books</th>
                        <th>Total</th>
                        <th>Payment</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>

                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                    <?php $i = 1; while ($order = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $i++ ?></td>
                        <td>
                            <div class="customer-name"><?= htmlspecialchars($order['customer_name']) ?></div>
                            <div class="customer-email"><?= htmlspecialchars($order['customer_email']) ?></div>
                        </td>
                        <td class="book-titles"><?= htmlspecialchars($order['book_titles'] ?? '—') ?></td>
                        <td>৳<?= number_format((float)$order['total_amount'], 2) ?></td>
                        <td><?= htmlspecialchars($order['payment_method']) ?></td>
                        <td>
                            <select class="status-dropdown" data-id="<?= (int)$order['id'] ?>">
                                <option value="pending"   <?= $order['status'] === 'pending'   ? 'selected' : '' ?>>Pending</option>
                                <option value="confirmed" <?= $order['status'] === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                <option value="shipped"   <?= $order['status'] === 'shipped'   ? 'selected' : '' ?>>Shipped</option>
                                <option value="delivered" <?= $order['status'] === 'delivered' ? 'selected' : '' ?>>Delivered</option>
                            </select>
                        </td>
                        <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="empty">No orders found.</td>
                    </tr>
                <?php endif; ?>

                </tbody>
            </table>
        </div>

    </div>
</div>

<script src="/online_book_store/public/js/admin_orders.js"></script>
</body>
</html>
<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login_page/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | BookStore</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/online_book_store/public/css/admin_homepage.css">
</head>
<body>

<div class="layout">

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="brand-icon">📚</span>
            <span class="brand-name">BookStore</span>
        </div>

        <nav class="sidebar-nav">
            <p class="nav-label">Admin Pages</p>
            <a href="/online_book_store/control/admin_control.php?action=books" class="nav-link">                📚 Book Management
            </a>
            <a href="/online_book_store/control/admin_control.php?action=users" class="nav-link">
                👥 All Users
            </a>
            <a href="/online_book_store/control/admin_control.php?action=orders" class="nav-link">
                🧾 Purchase History
            </a>
        </nav>

        <div class="sidebar-footer">
            <button class="logout-btn" id="logoutBtn">Logout</button>
        </div>
    </aside>

    <!-- Main -->
    <div class="main">

        <!-- Topbar -->
        <div class="topbar">
            <h1>Dashboard</h1>
            <div class="user-info">
                👤 <?= htmlspecialchars($_SESSION['name']) ?>
                <span class="role-badge">Admin</span>
            </div>
        </div>

        <!-- Content -->
        <div class="content">

            <h2>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?>!</h2>
            <p class="sub">Session is active.</p>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-icon">📚</div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['books'] ?? 0 ?></div>
                        <div class="stat-label">Total Books</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">👥</div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['customers'] ?? 0 ?></div>
                        <div class="stat-label">Total Customers</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">🧾</div>
                    <div class="stat-info">
                        <div class="stat-value"><?= $stats['orders'] ?? 0 ?></div>
                        <div class="stat-label">Total Orders</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon">💰</div>
                    <div class="stat-info">
                        <div class="stat-value">৳<?= number_format((float)($stats['revenue'] ?? 0), 2) ?></div>
                        <div class="stat-label">Total Revenue</div>
                    </div>
                </div>
            </div>

            <!-- Session Info -->
            <div class="session-box">
                <h3>Session Info</h3>
                <table>
                    <tr>
                        <td>User ID</td>
                        <td><?= $_SESSION['user_id'] ?></td>
                    </tr>
                    <tr>
                        <td>Name</td>
                        <td><?= htmlspecialchars($_SESSION['name']) ?></td>
                    </tr>
                    <tr>
                        <td>Role</td>
                        <td><?= htmlspecialchars($_SESSION['role']) ?></td>
                    </tr>
                    <tr>
                        <td>Logged in at</td>
                        <td><?= $_SESSION['logged_at'] ?? 'N/A' ?></td>
                    </tr>
                    <tr>
                        <td>Session ID</td>
                        <td><?= session_id() ?></td>
                    </tr>
                    <tr>
                        <td>Remember Me</td>
                        <td><?= isset($_COOKIE['remember_token']) ? '✅ Active (7 days)' : '❌ Not set' ?></td>
                    </tr>
                </table>
            </div>

        </div>
    </div>
</div>

<script src="/online_book_store/public/js/admin_homepage.js?v=2"></script>
</body>
</html>
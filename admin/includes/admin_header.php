<?php
requireAdmin();

// Get current page for sidebar active state
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$adminName = $_SESSION['user_name'] ?? 'Admin';
$adminEmail = $_SESSION['user_email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel — <?php echo APP_NAME; ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/admin.css?v=<?= time(); ?>">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <img src="../assets/images/logo.png?v=3" alt="BharatBuy" class="sidebar-logo">
            <span class="sidebar-brand-text">Admin</span>
        </div>

        <nav class="sidebar-nav">
            <a href="index.php" class="sidebar-link <?= $currentPage === 'index' ? 'active' : '' ?>" title="Dashboard">
                <i class="fa-solid fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
            <a href="users.php" class="sidebar-link <?= $currentPage === 'users' ? 'active' : '' ?>" title="Users">
                <i class="fa-solid fa-users"></i>
                <span>Users</span>
            </a>
            <a href="products.php" class="sidebar-link <?= $currentPage === 'products' ? 'active' : '' ?>" title="Products">
                <i class="fa-solid fa-box-open"></i>
                <span>Products</span>
            </a>
            <a href="chatbot.php" class="sidebar-link <?= $currentPage === 'chatbot' ? 'active' : '' ?>" title="Chatbot FAQ">
                <i class="fa-solid fa-robot"></i>
                <span>Chatbot FAQ</span>
            </a>

            <div class="sidebar-divider"></div>
            <div class="sidebar-section-label">Monitoring</div>

            <a href="trackers.php" class="sidebar-link <?= $currentPage === 'trackers' ? 'active' : '' ?>" title="Price Trackers">
                <i class="fa-solid fa-bell"></i>
                <span>Price Trackers</span>
            </a>
            <a href="notifications.php" class="sidebar-link <?= $currentPage === 'notifications' ? 'active' : '' ?>" title="Notifications">
                <i class="fa-solid fa-envelope"></i>
                <span>Notifications</span>
            </a>
            <a href="provider_logs.php" class="sidebar-link <?= $currentPage === 'provider_logs' ? 'active' : '' ?>" title="Provider Health">
                <i class="fa-solid fa-server"></i>
                <span>Provider Health</span>
            </a>
            <a href="chatbot_logs.php" class="sidebar-link <?= $currentPage === 'chatbot_logs' ? 'active' : '' ?>" title="Chatbot Logs">
                <i class="fa-solid fa-comments"></i>
                <span>Chatbot Logs</span>
            </a>
            <a href="activity_log.php" class="sidebar-link <?= $currentPage === 'activity_log' ? 'active' : '' ?>" title="Activity Log">
                <i class="fa-solid fa-clipboard-list"></i>
                <span>Activity Log</span>
            </a>
        </nav>

        <div class="sidebar-footer">
            <a href="../index.php" class="sidebar-link" title="Back to Store">
                <i class="fa-solid fa-store"></i>
                <span>Back to Store</span>
            </a>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Top Bar -->
        <header class="admin-topbar">
            <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle Sidebar">
                <i class="fa-solid fa-bars"></i>
            </button>

            <div class="topbar-right">
                <div class="admin-profile">
                    <div class="admin-avatar"><?= strtoupper(substr($adminName, 0, 1)); ?></div>
                    <div class="admin-info">
                        <span class="admin-name"><?= htmlspecialchars($adminName); ?></span>
                        <span class="admin-role">Administrator</span>
                    </div>
                </div>
                <a href="logout.php" class="topbar-btn" title="Logout">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </a>
            </div>
        </header>

        <!-- Page Content -->
        <div class="admin-content">

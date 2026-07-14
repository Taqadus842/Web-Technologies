<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<header class="main-header">

    <div class="logo">
        📚 Book Request System
    </div>

    <nav class="nav-links">

        <?php if (isset($_SESSION['user'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="request_book.php">Request Book</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['admin'])): ?>
            <a href="admin/dashboard.php">Admin Panel</a>
        <?php endif; ?>

        <?php if (isset($_SESSION['superadmin'])): ?>
            <a href="superadmin/dashboard.php">Super Admin</a>
        <?php endif; ?>

    </nav>

    <div class="user-section">

        <?php if (isset($_SESSION['user'])): ?>
            <span class="user">
                 <?php echo htmlspecialchars($_SESSION['user']['username']); ?>
            </span>
        <?php elseif (isset($_SESSION['admin'])): ?>
            <span class="user">
                 <?php echo htmlspecialchars($_SESSION['admin']['username']); ?>
            </span>
        <?php elseif (isset($_SESSION['superadmin'])): ?>
            <span class="user">
                 <?php echo htmlspecialchars($_SESSION['superadmin']['username']); ?>
            </span>
        <?php endif; ?>

        <?php if (isset($_SESSION['user']) || isset($_SESSION['admin']) || isset($_SESSION['superadmin'])): ?>
            <a class="logout" href="logout.php">Logout</a>
        <?php else: ?>
            <a class="login" href="login.php">Login</a>
        <?php endif; ?>

    </div>

</header>
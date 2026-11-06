<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get current page for active nav highlight
$current = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));

function isActive($pages) {
    global $current, $current_dir;
    foreach ((array)$pages as $page) {
        if ($current === $page) return 'active';
    }
    return '';
}
?>

<nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #0d6efd;">
    <div class="container">

        <a class="navbar-brand fw-bold" href="/globetrek/index.php">
            GlobeTrek Adventures
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('index.php'); ?>" href="/globetrek/index.php">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('packages.php'); ?>" href="/globetrek/packages.php">Packages</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('about.php'); ?>" href="/globetrek/about.php">About</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo isActive('contact.php'); ?>" href="/globetrek/contact.php">Contact</a>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_dir == 'admin' ? 'active' : ''; ?>" href="/globetrek/admin/dashboard.php">
                                <i class="bi bi-speedometer2"></i> Admin Panel
                            </a>
                        </li>
                    <?php elseif ($_SESSION['role'] == 'staff'): ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_dir == 'staff' ? 'active' : ''; ?>" href="/globetrek/staff/dashboard.php">
                                <i class="bi bi-briefcase"></i> Staff Panel
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link <?php echo $current_dir == 'customer' ? 'active' : ''; ?>" href="/globetrek/customer/dashboard.php">
                                <i class="bi bi-person-circle"></i> My Account
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <a class="nav-link text-warning" href="/globetrek/logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>

                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo isActive('login.php'); ?>" href="/globetrek/login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning btn-sm ms-2 <?php echo isActive('register.php'); ?>" href="/globetrek/register.php">Register</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>

<style>
.navbar .nav-link.active {
    font-weight: 700;
    border-bottom: 2px solid #fff;
    color: #fff !important;
}
</style>

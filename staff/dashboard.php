<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('staff');
 
// Quick stats for staff
$pending   = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE status='pending'")->fetch_assoc()['cnt'];
$inquiries = $conn->query("SELECT COUNT(*) as cnt FROM inquiries WHERE status='open'")->fetch_assoc()['cnt'];
$packages  = $conn->query("SELECT COUNT(*) as cnt FROM packages WHERE status='active'")->fetch_assoc()['cnt'];
?>
 
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="/globetrek/assets/css/dashboard.css">
<?php include '../includes/navbar.php'; ?>
 
<div class="container-fluid">
    <div class="row">
 
        <!-- Sidebar -->
        <div class="col-md-2 p-0 sidebar d-none d-md-block">
            <div class="text-white text-center py-3 fw-bold border-bottom border-secondary">
                <i class="bi bi-briefcase"></i> Staff Panel
            </div>
            <a href="dashboard.php" class="active"><i class="bi bi-house"></i> Dashboard</a>
            <a href="inquiries.php"><i class="bi bi-envelope"></i> Inquiries</a>
            <a href="manage_bookings.php"><i class="bi bi-calendar-check"></i> Bookings</a>
            <a href="update_packages.php"><i class="bi bi-suitcase-lg"></i> Packages</a>
            <a href="/globetrek/logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
 
        <!-- Main Content -->
        <div class="col-md-10 dash-content">
            <div class="welcome-box mb-4">
                <h4><i class="bi bi-briefcase"></i> Staff Dashboard</h4>
                <p class="mb-0">Welcome, <?php echo clean($_SESSION['full_name']); ?>!</p>
            </div>
 
            <!-- Stats -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <div class="stat-box" style="border-top-color: #ffc107;">
                        <h3 style="color:#ffc107;"><?php echo $pending; ?></h3>
                        <p>Pending Bookings</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-box" style="border-top-color: #dc3545;">
                        <h3 style="color:#dc3545;"><?php echo $inquiries; ?></h3>
                        <p>Open Inquiries</p>
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stat-box" style="border-top-color: #198754;">
                        <h3 style="color:#198754;"><?php echo $packages; ?></h3>
                        <p>Active Packages</p>
                    </div>
                </div>
            </div>
 
            <!-- Quick Links -->
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="manage_bookings.php" class="btn btn-outline-primary w-100 py-3">
                        <i class="bi bi-calendar-check fs-3 d-block mb-1"></i>
                        Manage Bookings
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="inquiries.php" class="btn btn-outline-danger w-100 py-3">
                        <i class="bi bi-envelope fs-3 d-block mb-1"></i>
                        Customer Inquiries
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="update_packages.php" class="btn btn-outline-success w-100 py-3">
                        <i class="bi bi-suitcase-lg fs-3 d-block mb-1"></i>
                        Update Packages
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
 
<?php include '../includes/footer.php'; ?>
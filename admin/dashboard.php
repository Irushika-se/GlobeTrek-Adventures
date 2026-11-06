<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('admin');
 
// Stats for dashboard
$total_users    = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role = 'customer'")->fetch_assoc()['cnt'];
$total_packages = $conn->query("SELECT COUNT(*) as cnt FROM packages")->fetch_assoc()['cnt'];
$total_bookings = $conn->query("SELECT COUNT(*) as cnt FROM bookings")->fetch_assoc()['cnt'];
$total_inquiries = $conn->query("SELECT COUNT(*) as cnt FROM inquiries WHERE status = 'open'")->fetch_assoc()['cnt'];
?>
 
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="/globetrek/assets/css/dashboard.css">
<?php include '../includes/navbar.php'; ?>
 
<div class="container-fluid">
    <div class="row">
 
        <!-- Sidebar -->
        <div class="col-md-2 p-0 sidebar d-none d-md-block">
            <div class="text-white text-center py-3 fw-bold border-bottom border-secondary">
                <i class="bi bi-speedometer2"></i> Admin Panel
            </div>
            <a href="dashboard.php" class="active"><i class="bi bi-house"></i> Dashboard</a>
            <a href="manage_users.php"><i class="bi bi-people"></i> Users</a>
            <a href="manage_packages.php"><i class="bi bi-suitcase-lg"></i> Packages</a>
            <a href="bookings.php"><i class="bi bi-calendar-check"></i> Bookings</a>
            <a href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a>
            <a href="/globetrek/logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
 
        <!-- Main Content -->
        <div class="col-md-10 dash-content">
            <div class="welcome-box mb-4">
                <h4><i class="bi bi-speedometer2"></i> Admin Dashboard</h4>
                <p class="mb-0">Welcome back, <?php echo clean($_SESSION['full_name']); ?>!</p>
            </div>
 
            <!-- Stats Row -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <h3><?php echo $total_users; ?></h3>
                        <p><i class="bi bi-people"></i> Total Customers</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box" style="border-top-color: #198754;">
                        <h3 style="color:#198754;"><?php echo $total_packages; ?></h3>
                        <p><i class="bi bi-suitcase-lg"></i> Packages</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box" style="border-top-color: #ffc107;">
                        <h3 style="color:#ffc107;"><?php echo $total_bookings; ?></h3>
                        <p><i class="bi bi-calendar-check"></i> Bookings</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box" style="border-top-color: #dc3545;">
                        <h3 style="color:#dc3545;"><?php echo $total_inquiries; ?></h3>
                        <p><i class="bi bi-envelope"></i> Open Inquiries</p>
                    </div>
                </div>
            </div>
 
            <!-- Recent Bookings -->
            <div class="card shadow-sm mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Recent Bookings</h6>
                    <a href="bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Date</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT b.*, u.full_name, p.title
                                        FROM bookings b
                                        JOIN users u ON b.customer_id = u.id
                                        JOIN packages p ON b.package_id = p.id
                                        ORDER BY b.booked_at DESC LIMIT 5";
                                $res = $conn->query($sql);
 
                                while ($b = $res->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>#<?php echo $b['id']; ?></td>
                                    <td><?php echo clean($b['full_name']); ?></td>
                                    <td><?php echo clean($b['title']); ?></td>
                                    <td><?php echo $b['travel_date']; ?></td>
                                    <td><?php echo formatPrice($b['total_price']); ?></td>
                                    <td>
                                        <span class="badge status-<?php echo $b['status']; ?>">
                                            <?php echo ucfirst($b['status']); ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
 
        </div>
    </div>
</div>
 
<?php include '../includes/footer.php'; ?>
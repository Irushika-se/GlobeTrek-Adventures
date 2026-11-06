<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('admin');
 
// Summary stats
$total_revenue  = $conn->query("SELECT SUM(total_price) as total FROM bookings WHERE status='confirmed'")->fetch_assoc()['total'];
$total_bookings = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE status='confirmed'")->fetch_assoc()['cnt'];
$total_users    = $conn->query("SELECT COUNT(*) as cnt FROM users WHERE role='customer'")->fetch_assoc()['cnt'];
$total_packages = $conn->query("SELECT COUNT(*) as cnt FROM packages WHERE status='active'")->fetch_assoc()['cnt'];
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
            <a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
            <a href="manage_users.php"><i class="bi bi-people"></i> Users</a>
            <a href="manage_packages.php"><i class="bi bi-suitcase-lg"></i> Packages</a>
            <a href="bookings.php"><i class="bi bi-calendar-check"></i> Bookings</a>
            <a href="reports.php" class="active"><i class="bi bi-file-earmark-text"></i> Reports</a>
            <a href="/globetrek/logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
 
        <!-- Main Content -->
        <div class="col-md-10 dash-content">
            <h4 class="mb-4"><i class="bi bi-file-earmark-bar-graph"></i> Reports & Summary</h4>
 
            <!-- Summary Cards -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <h3 class="text-success">LKR <?php echo number_format($total_revenue ?? 0, 0); ?></h3>
                        <p>Total Revenue</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <h3><?php echo $total_bookings; ?></h3>
                        <p>Total Bookings</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <h3><?php echo $total_users; ?></h3>
                        <p>Registered Customers</p>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="stat-box">
                        <h3><?php echo $total_packages; ?></h3>
                        <p>Active Packages</p>
                    </div>
                </div>
            </div>
 
            <!-- Top Packages Report -->
            <div class="card shadow-sm mb-4">
                <div class="card-header"><h6 class="mb-0">Most Booked Packages</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>Package</th>
                                    <th>Destination</th>
                                    <th>Total Bookings</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT p.title, p.destination,
                                               COUNT(b.id) as bookings,
                                               SUM(b.total_price) as revenue
                                        FROM packages p
                                        LEFT JOIN bookings b ON p.id = b.package_id AND b.status = 'confirmed'
                                        GROUP BY p.id
                                        ORDER BY bookings DESC";
                                $res = $conn->query($sql);
 
                                while ($row = $res->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo clean($row['title']); ?></td>
                                    <td><?php echo clean($row['destination']); ?></td>
                                    <td><?php echo $row['bookings'] ?? 0; ?></td>
                                    <td><?php echo formatPrice($row['revenue'] ?? 0); ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
 
            <!-- Open Inquiries Report -->
            <div class="card shadow-sm">
                <div class="card-header"><h6 class="mb-0">Customer Inquiries</h6></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>#ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = $conn->query("SELECT * FROM inquiries ORDER BY submitted_at DESC LIMIT 10");
                                while ($inq = $res->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>#<?php echo $inq['id']; ?></td>
                                    <td><?php echo clean($inq['name']); ?></td>
                                    <td><?php echo clean($inq['email']); ?></td>
                                    <td><?php echo clean($inq['subject']); ?></td>
                                    <td><?php echo date('d M Y', strtotime($inq['submitted_at'])); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $inq['status'] == 'open' ? 'warning text-dark' : 'success'; ?>">
                                            <?php echo ucfirst($inq['status']); ?>
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
 
<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('admin');
 
$success = "";
 
// Update booking status
if (isset($_GET['confirm']) && is_numeric($_GET['confirm'])) {
    $id = (int)$_GET['confirm'];
    $conn->query("UPDATE bookings SET status = 'confirmed' WHERE id = $id");
    $success = "Booking #$id confirmed!";
}
 
// Delete booking
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM bookings WHERE id = $id");
    $success = "Booking #$id deleted!";
}
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
            <a href="bookings.php" class="active"><i class="bi bi-calendar-check"></i> Bookings</a>
            <a href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a>
            <a href="/globetrek/logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
 
        <!-- Main Content -->
        <div class="col-md-10 dash-content">
            <h4 class="mb-4"><i class="bi bi-calendar-check"></i> All Bookings</h4>
 
            <?php if ($success): successMsg($success); endif; ?>
 
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>#ID</th>
                                    <th>Customer</th>
                                    <th>Package</th>
                                    <th>Travel Date</th>
                                    <th>Persons</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT b.*, u.full_name, p.title
                                        FROM bookings b
                                        JOIN users u ON b.customer_id = u.id
                                        JOIN packages p ON b.package_id = p.id
                                        ORDER BY b.booked_at DESC";
                                $res = $conn->query($sql);
 
                                while ($b = $res->fetch_assoc()):
                                ?>
                                <tr>
                                    <td>#<?php echo $b['id']; ?></td>
                                    <td><?php echo clean($b['full_name']); ?></td>
                                    <td><?php echo clean($b['title']); ?></td>
                                    <td><?php echo $b['travel_date']; ?></td>
                                    <td><?php echo $b['num_persons']; ?></td>
                                    <td><?php echo formatPrice($b['total_price']); ?></td>
                                    <td>
                                        <span class="badge status-<?php echo $b['status']; ?>">
                                            <?php echo ucfirst($b['status']); ?>
                                        </span>
                                    </td>
                                    <td class="d-flex gap-1">
                                        <a href="?confirm=<?php echo $b['id']; ?>"
                                           class="btn btn-sm btn-success">Confirm</a>
                                        <a href="?delete=<?php echo $b['id']; ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Delete this booking permanently?')">Delete</a>
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
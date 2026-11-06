<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
// Only customers can access this
requireRole('customer');
 
$user_id = $_SESSION['user_id'];
 
// Count customer's bookings
$total_bookings = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE customer_id = $user_id")->fetch_assoc()['cnt'];
$confirmed      = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE customer_id = $user_id AND status = 'confirmed'")->fetch_assoc()['cnt'];
$pending        = $conn->query("SELECT COUNT(*) as cnt FROM bookings WHERE customer_id = $user_id AND status = 'pending'")->fetch_assoc()['cnt'];
?>
 
<?php include '../includes/header.php'; ?>
<link rel="stylesheet" href="/globetrek/assets/css/dashboard.css">
<?php include '../includes/navbar.php'; ?>
 
<div class="container my-4">
 
    <!-- Welcome Banner -->
    <div class="welcome-box mb-4">
        <h4><i class="bi bi-person-circle"></i> Welcome, <?php echo clean($_SESSION['full_name']); ?>!</h4>
        <p class="mb-0">Manage your bookings and travel plans from here.</p>
    </div>
 
    <!-- Stats Row -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="stat-box">
                <h3><?php echo $total_bookings; ?></h3>
                <p>Total Bookings</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-box" style="border-top-color: #198754;">
                <h3 style="color: #198754;"><?php echo $confirmed; ?></h3>
                <p>Confirmed</p>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="stat-box" style="border-top-color: #ffc107;">
                <h3 style="color: #ffc107;"><?php echo $pending; ?></h3>
                <p>Pending</p>
            </div>
        </div>
    </div>
 
    <!-- Quick Links -->
    <div class="row mb-4">
        <div class="col-md-4 mb-2">
            <a href="/globetrek/packages.php" class="btn btn-primary w-100">
                <i class="bi bi-suitcase-lg"></i> Browse Packages
            </a>
        </div>
        <div class="col-md-4 mb-2">
            <a href="my_bookings.php" class="btn btn-outline-primary w-100">
                <i class="bi bi-calendar-check"></i> My Bookings
            </a>
        </div>
        <div class="col-md-4 mb-2">
            <a href="profile.php" class="btn btn-outline-secondary w-100">
                <i class="bi bi-person-gear"></i> My Profile
            </a>
        </div>
    </div>
 
    <!-- Recent Bookings -->
    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0"><i class="bi bi-clock-history"></i> Recent Bookings</h6>
            <a href="my_bookings.php" class="btn btn-sm btn-outline-primary">View All</a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#ID</th>
                            <th>Package</th>
                            <th>Travel Date</th>
                            <th>Persons</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, p.title FROM bookings b
                                JOIN packages p ON b.package_id = p.id
                                WHERE b.customer_id = $user_id
                                ORDER BY b.booked_at DESC LIMIT 5";
                        $res = $conn->query($sql);
 
                        if ($res->num_rows == 0):
                        ?>
                            <tr><td colspan="6" class="text-center text-muted">No bookings yet. <a href="/globetrek/packages.php">Browse packages</a></td></tr>
                        <?php
                        else:
                            while ($b = $res->fetch_assoc()):
                        ?>
                            <tr>
                                <td>#<?php echo $b['id']; ?></td>
                                <td><?php echo clean($b['title']); ?></td>
                                <td><?php echo $b['travel_date']; ?></td>
                                <td><?php echo $b['num_persons']; ?></td>
                                <td><?php echo formatPrice($b['total_price']); ?></td>
                                <td>
                                    <span class="badge status-<?php echo $b['status']; ?>">
                                        <?php echo ucfirst($b['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php
                            endwhile;
                        endif;
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
 
<?php include '../includes/footer.php'; ?>
 
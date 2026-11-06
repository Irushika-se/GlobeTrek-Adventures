<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('customer');
 
$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";
 
// Handle booking cancellation
if (isset($_GET['cancel']) && is_numeric($_GET['cancel'])) {
    $b_id = (int)$_GET['cancel'];
 
    // Only cancel if it belongs to this customer and is pending
    $check = "SELECT id FROM bookings WHERE id = $b_id AND customer_id = $user_id AND status = 'pending'";
    $res = $conn->query($check);
 
    if ($res->num_rows > 0) {
        $conn->query("UPDATE bookings SET status = 'cancelled' WHERE id = $b_id");
        $success = "Booking #$b_id has been cancelled.";
    } else {
        $error = "Cannot cancel this booking.";
    }
}
?>
 
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
 
<div class="page-banner">
    <div class="container">
        <h2><i class="bi bi-calendar-check"></i> My Bookings</h2>
        <p class="mb-0">View and manage all your travel bookings</p>
    </div>
</div>
 
<div class="container mb-5">
 
    <?php if ($success): successMsg($success); endif; ?>
    <?php if ($error):   errorMsg($error);   endif; ?>
 
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-primary">
                        <tr>
                            <th>#ID</th>
                            <th>Package</th>
                            <th>Destination</th>
                            <th>Travel Date</th>
                            <th>Persons</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = "SELECT b.*, p.title, p.destination FROM bookings b
                                JOIN packages p ON b.package_id = p.id
                                WHERE b.customer_id = $user_id
                                ORDER BY b.booked_at DESC";
                        $res = $conn->query($sql);
 
                        if ($res->num_rows == 0):
                        ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    You have no bookings yet.
                                    <a href="/globetrek/packages.php">Browse packages to book one!</a>
                                </td>
                            </tr>
                        <?php
                        else:
                            while ($b = $res->fetch_assoc()):
                        ?>
                            <tr>
                                <td>#<?php echo $b['id']; ?></td>
                                <td><?php echo clean($b['title']); ?></td>
                                <td><?php echo clean($b['destination']); ?></td>
                                <td><?php echo $b['travel_date']; ?></td>
                                <td><?php echo $b['num_persons']; ?></td>
                                <td><?php echo formatPrice($b['total_price']); ?></td>
                                <td>
                                    <span class="status-<?php echo $b['status']; ?>">
                                        <?php echo ucfirst($b['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($b['status'] == 'pending'): ?>
                                        <a href="payment.php?booking_id=<?php echo $b['id']; ?>"
                                           class="btn btn-sm btn-success me-1">
                                            <i class="bi bi-credit-card"></i> Pay Now
                                        </a>
                                        <a href="edit_booking.php?id=<?php echo $b['id']; ?>"
                                           class="btn btn-sm btn-warning me-1">
                                            <i class="bi bi-pencil"></i> Edit
                                        </a>
                                        <a href="my_bookings.php?cancel=<?php echo $b['id']; ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirm('Cancel this booking?')">
                                            <i class="bi bi-x-circle"></i> Cancel
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted small">-</span>
                                    <?php endif; ?>
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
 
    <a href="dashboard.php" class="btn btn-outline-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Back to Dashboard
    </a>
</div>
 
<?php include '../includes/footer.php'; ?>
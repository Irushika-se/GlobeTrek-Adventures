<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';
 
// Get package ID from URL
if (!isset($_GET['id'])) {
    header("Location: packages.php");
    exit();
}
 
$id = (int)$_GET['id'];
 
// Fetch package details
$sql = "SELECT * FROM packages WHERE id = $id AND status = 'active'";
$result = $conn->query($sql);
 
if ($result->num_rows == 0) {
    header("Location: packages.php");
    exit();
}
 
$pkg = $result->fetch_assoc();
?>
 
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
 
<div class="container my-5">
    <div class="row">
 
        <!-- Package Details Column -->
        <div class="col-md-8">
 
            <!-- Package Image -->
            <img src="/globetrek/assets/images/<?php echo !empty($pkg['image_name']) ? htmlspecialchars($pkg['image_name']) : 'package.jpg'; ?>"
                 class="img-fluid rounded mb-3 w-100"
                 alt="<?php echo clean($pkg['title']); ?>"
                 style="height:350px; object-fit:cover;"
                 onerror="this.src='/globetrek/assets/images/package.jpg'">
 
            <h2><?php echo clean($pkg['title']); ?></h2>
 
            <div class="d-flex gap-3 mb-3 flex-wrap">
                <span class="badge bg-primary fs-6">
                    <i class="bi bi-geo-alt"></i> <?php echo clean($pkg['destination']); ?>
                </span>
                <span class="badge bg-success fs-6">
                    <i class="bi bi-calendar3"></i> <?php echo $pkg['duration_days']; ?> Days
                </span>
                <span class="badge bg-info text-dark fs-6">
                    <i class="bi bi-people"></i> Max <?php echo $pkg['max_persons']; ?> Persons
                </span>
            </div>
 
            <h5>About This Package</h5>
            <p class="text-muted"><?php echo nl2br(clean($pkg['description'])); ?></p>
 
            <?php 
            $inclusions_list = !empty($pkg['inclusions']) ? array_filter(array_map('trim', explode("\n", $pkg['inclusions']))) : [];
            if (!empty($inclusions_list)): ?>
            <h5 class="mt-4">What's Included</h5>
            <ul class="list-group list-group-flush mb-4">
                <?php foreach ($inclusions_list as $item): ?>
                <li class="list-group-item"><?php echo htmlspecialchars($item); ?></li>
                <?php endforeach; ?>
            </ul>
            <?php endif; ?>
 
        </div>
 
        <!-- Booking Sidebar -->
        <div class="col-md-4">
            <div class="card shadow-sm sticky-top" style="top: 80px;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Book This Package</h5>
                </div>
                <div class="card-body">
                    <p class="fs-4 fw-bold text-primary mb-1"><?php echo formatPrice($pkg['price']); ?></p>
                    <p class="text-muted mb-3">per person</p>
 
                    <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] == 'customer'): ?>
                        <!-- Show booking button for logged-in customers -->
                        <a href="booking.php?id=<?php echo $pkg['id']; ?>"
                           class="btn btn-primary d-grid mb-2">
                            <span><i class="bi bi-calendar-check"></i> Book Now</span>
                        </a>
                    <?php elseif (!isset($_SESSION['user_id'])): ?>
                        <!-- Ask to login first -->
                        <a href="login.php" class="btn btn-primary d-block mb-2">
                            <i class="bi bi-lock"></i> Login to Book
                        </a>
                        <p class="text-muted small text-center">
                            Don't have an account? <a href="register.php">Register here</a>
                        </p>
                    <?php else: ?>
                        <div class="alert alert-info small">Only customers can book packages.</div>
                    <?php endif; ?>
 
                    <a href="contact.php" class="btn btn-outline-secondary d-block">
                        <i class="bi bi-chat-dots"></i> Ask a Question
                    </a>
                </div>
            </div>
        </div>
 
    </div>
 
    <!-- Back button -->
    <a href="packages.php" class="btn btn-outline-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Back to Packages
    </a>
</div>
 
<?php include 'includes/footer.php'; ?>
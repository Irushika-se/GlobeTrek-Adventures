<?php
// Load database and functions
include 'includes/db_connect.php';
include 'includes/functions.php';
?>
 
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
 
<div class="container-fluid p-0">
<img src="assets/images/hero.jpg" class="img-fluid w-100" alt="Travel Banner" style="height:400px;object-fit:cover;">
</div>

<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-4 fw-bold">Explore the World with GlobeTrek</h1>
        <p class="lead mb-4">Discover amazing destinations, book tour packages, and create unforgettable memories.</p>

    </div>
</div>
 
<!-- Featured Packages Section -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-4">Popular Tour Packages</h2>
 
        <div class="row">
            <?php
            // Get 3 active packages to show on homepage
            $sql = "SELECT * FROM packages WHERE status = 'active' LIMIT 3";
            $result = $conn->query($sql);
 
            while ($pkg = $result->fetch_assoc()):
            ?>
            <div class="col-md-4 mb-4">
                <div class="card package-card h-100">
                    <img src="assets/images/<?php echo !empty($pkg['image_name']) ? $pkg['image_name'] : 'package.jpg'; ?>" class="card-img-top" style="height:220px;object-fit:cover;" alt="Package">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo clean($pkg['title']); ?></h5>
                        <p class="text-muted mb-1">
                            <i class="bi bi-geo-alt"></i> <?php echo clean($pkg['destination']); ?>
                        </p>
                        <p class="text-muted mb-2">
                            <i class="bi bi-calendar3"></i> <?php echo $pkg['duration_days']; ?> Days
                        </p>
                        <p class="card-text text-muted" style="font-size:0.9rem;">
                            <?php echo clean(substr($pkg['description'], 0, 100)); ?>...
                        </p>
                    </div>
                    <div class="card-footer d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-primary"><?php echo formatPrice($pkg['price']); ?></span>
                        <a href="package_details.php?id=<?php echo $pkg['id']; ?>" class="btn btn-primary btn-sm">
                            View Details
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
 
        <div class="text-center mt-3">
            <a href="packages.php" class="btn btn-outline-primary">See All Packages</a>
        </div>
    </div>
</div>
 

<!-- Why Choose Us Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">Why Choose GlobeTrek Adventures?</h2>
    <div class="row text-center">
        <div class="col-md-3 mb-3"><div class="card h-100 p-3"><h5>Affordable Packages</h5><p class="text-muted">Budget-friendly local and international tours.</p></div></div>
        <div class="col-md-3 mb-3"><div class="card h-100 p-3"><h5>Trusted Service</h5><p class="text-muted">Reliable travel planning and booking services.</p></div></div>
        <div class="col-md-3 mb-3"><div class="card h-100 p-3"><h5>Easy Online Booking</h5><p class="text-muted">Simple package browsing and booking process.</p></div></div>
        <div class="col-md-3 mb-3"><div class="card h-100 p-3"><h5>Customer Support</h5><p class="text-muted">Support for inquiries and travel assistance.</p></div></div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

 
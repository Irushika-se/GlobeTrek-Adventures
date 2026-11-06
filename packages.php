<?php
include 'includes/db_connect.php';
include 'includes/functions.php';
?>
 
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
 
<!-- Page Banner -->
<div class="page-banner">
    <div class="container">
        <h2><i class="bi bi-suitcase-lg"></i> Our Tour Packages</h2>
        <p class="mb-0">Find your perfect adventure from our collection of hand-picked destinations</p>
    </div>
</div>
 
<div class="container mb-5">
 
    <!-- Search Form -->
    <div class="card mb-4 p-3">
        <form method="GET" action="">
            <div class="row g-2 align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Search by destination or package name</label>
                    <input type="text" name="search" class="form-control"
                           placeholder="E.g. Bali, Thailand..."
                           value="<?php echo isset($_GET['search']) ? clean($_GET['search']) : ''; ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
                <div class="col-md-2">
                    <a href="packages.php" class="btn btn-outline-secondary w-100">Clear</a>
                </div>
            </div>
        </form>
    </div>
 
    <!-- Packages Grid -->
    <div class="row">
        <?php
        // Check if searching
        if (isset($_GET['search']) && $_GET['search'] != "") {
            $search = clean($_GET['search']);
            $sql = "SELECT * FROM packages WHERE status = 'active'
                    AND (title LIKE '%$search%' OR destination LIKE '%$search%')";
        } else {
            $sql = "SELECT * FROM packages WHERE status = 'active'";
        }
 
        $result = $conn->query($sql);
 
        if ($result->num_rows == 0):
        ?>
            <div class="col-12">
                <div class="alert alert-info">No packages found. Try a different search.</div>
            </div>
        <?php
        else:
            while ($pkg = $result->fetch_assoc()):
        ?>
        <div class="col-md-4 mb-4">
            <div class="card package-card h-100">
                <img src="/globetrek/assets/images/<?php echo !empty($pkg['image_name']) ? $pkg['image_name'] : 'package.jpg'; ?>" class="card-img-top" alt="Package Image" style="height:220px;object-fit:cover;" onerror="this.src='/globetrek/assets/images/package.jpg'">
                <div class="card-body">
                    <h5 class="card-title"><?php echo clean($pkg['title']); ?></h5>
                    <p class="text-muted mb-1">
                        <i class="bi bi-geo-alt-fill text-danger"></i>
                        <?php echo clean($pkg['destination']); ?>
                    </p>
                    <p class="text-muted mb-2">
                        <i class="bi bi-calendar3 text-primary"></i>
                        <?php echo $pkg['duration_days']; ?> Days |
                        <i class="bi bi-people text-success"></i>
                        Max <?php echo $pkg['max_persons']; ?> persons
                    </p>
                    <p class="card-text text-muted" style="font-size:0.88rem;">
                        <?php echo clean(substr($pkg['description'], 0, 120)); ?>...
                    </p>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <div>
                        <span class="fw-bold text-primary fs-5"><?php echo formatPrice($pkg['price']); ?></span>
                        <small class="text-muted d-block">per person</small>
                    </div>
                    <a href="package_details.php?id=<?php echo $pkg['id']; ?>"
                       class="btn btn-primary">View Details</a>
                </div>
            </div>
        </div>
        <?php
            endwhile;
        endif;
        ?>
    </div>
 
</div>
 
<?php include 'includes/footer.php'; ?>
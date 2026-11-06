<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';

requireRole('staff');

$success = "";
$error   = "";

// Handle update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $pkg_id       = (int)$_POST['pkg_id'];
    $title        = clean($_POST['title']);
    $destination  = clean($_POST['destination']);
    $price        = (float)$_POST['price'];
    $duration     = (int)$_POST['duration_days'];
    $max_persons  = (int)$_POST['max_persons'];
    $description  = clean($_POST['description']);
    $inclusions   = $conn->real_escape_string(strip_tags($_POST['inclusions']));

    if ($price <= 0) {
        $error = "Price must be greater than 0.";
    } elseif (empty($title) || empty($destination)) {
        $error = "Title and destination are required.";
    } else {
        $conn->query("UPDATE packages SET
            title='$title',
            destination='$destination',
            price=$price,
            duration_days=$duration,
            max_persons=$max_persons,
            description='$description',
            inclusions='$inclusions'
            WHERE id=$pkg_id");
        $success = "Package updated successfully!";
    }
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
                <i class="bi bi-briefcase"></i> Staff Panel
            </div>
            <a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
            <a href="inquiries.php"><i class="bi bi-envelope"></i> Inquiries</a>
            <a href="manage_bookings.php"><i class="bi bi-calendar-check"></i> Bookings</a>
            <a href="update_packages.php" class="active"><i class="bi bi-suitcase-lg"></i> Packages</a>
            <a href="/globetrek/logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 dash-content">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Update Packages</h4>

            <?php if ($success): successMsg($success); endif; ?>
            <?php if ($error):   errorMsg($error);   endif; ?>

            <?php
            $packages = $conn->query("SELECT * FROM packages WHERE status='active' ORDER BY id DESC");
            while ($pkg = $packages->fetch_assoc()):
            ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header">
                    <strong><?php echo clean($pkg['title']); ?></strong>
                    — <span class="text-muted"><?php echo clean($pkg['destination']); ?></span>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <input type="hidden" name="pkg_id" value="<?php echo $pkg['id']; ?>">
                        <div class="row g-2 mb-2">
                            <div class="col-md-5">
                                <label class="form-label">Package Title</label>
                                <input type="text" name="title" class="form-control"
                                       value="<?php echo clean($pkg['title']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Destination</label>
                                <input type="text" name="destination" class="form-control"
                                       value="<?php echo clean($pkg['destination']); ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Price (LKR)</label>
                                <input type="number" name="price" class="form-control"
                                       value="<?php echo $pkg['price']; ?>" required>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-3">
                                <label class="form-label">Duration (Days)</label>
                                <input type="number" name="duration_days" class="form-control"
                                       value="<?php echo $pkg['duration_days']; ?>" required>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Max Persons</label>
                                <input type="number" name="max_persons" class="form-control"
                                       value="<?php echo $pkg['max_persons']; ?>" required>
                            </div>
                        </div>
                        <div class="row g-2 mb-2">
                            <div class="col-md-6">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="3"><?php echo clean($pkg['description']); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Inclusions <small class="text-muted">(one per line)</small></label>
                                <textarea name="inclusions" class="form-control" rows="3"><?php echo strip_tags(html_entity_decode($pkg['inclusions'], ENT_QUOTES, 'UTF-8')); ?></textarea>
                            </div>
                        </div>
                        <div class="row g-2">
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-warning w-100">Update</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <?php endwhile; ?>

        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

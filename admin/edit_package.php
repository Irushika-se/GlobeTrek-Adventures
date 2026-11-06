<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';

requireRole('admin');

if (!isset($_GET['id'])) {
    header("Location: manage_packages.php");
    exit();
}

$id = (int)$_GET['id'];

$result = $conn->query("SELECT * FROM packages WHERE id=$id");
$package = $result->fetch_assoc();

if (!$package) {
    header("Location: manage_packages.php");
    exit();
}

$success = "";
$error = "";

if (isset($_POST['update'])) {
    $title       = clean($_POST['title']);
    $destination = clean($_POST['destination']);
    $description = clean($_POST['description']);
    $inclusions  = clean($_POST['inclusions']);
    $price       = (float)$_POST['price'];
    $days        = (int)$_POST['duration_days'];
    $max         = (int)$_POST['max_persons'];
    $image_name  = $package['image_name']; // keep existing by default

    // Handle new image upload
    if (!empty($_FILES['image']['name'])) {
        $ext     = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $image_name = 'pkg_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image_name);
        }
    }

    if (empty($title) || empty($destination) || $price <= 0) {
        $error = "Please fill in all required fields.";
    } else {
        $conn->query("
            UPDATE packages
            SET title='$title', destination='$destination', description='$description',
                inclusions='$inclusions', price=$price, duration_days=$days, max_persons=$max,
                image_name='$image_name'
            WHERE id=$id
        ");
        header("Location: manage_packages.php");
        exit();
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
                <i class="bi bi-speedometer2"></i> Admin Panel
            </div>
            <a href="dashboard.php"><i class="bi bi-house"></i> Dashboard</a>
            <a href="manage_users.php"><i class="bi bi-people"></i> Users</a>
            <a href="manage_packages.php" class="active"><i class="bi bi-suitcase-lg"></i> Packages</a>
            <a href="bookings.php"><i class="bi bi-calendar-check"></i> Bookings</a>
            <a href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a>
            <a href="/globetrek/logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>

        <!-- Main Content -->
        <div class="col-md-10 dash-content">
            <h4 class="mb-4"><i class="bi bi-pencil-square"></i> Edit Package</h4>

            <?php if ($error): errorMsg($error); endif; ?>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Package Title *</label>
                                <input type="text" name="title" class="form-control"
                                       value="<?php echo htmlspecialchars($package['title']); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Destination *</label>
                                <input type="text" name="destination" class="form-control"
                                       value="<?php echo htmlspecialchars($package['destination']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Price (LKR) *</label>
                                <input type="number" name="price" class="form-control" min="0" step="0.01"
                                       value="<?php echo $package['price']; ?>" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Duration (Days)</label>
                                <input type="number" name="duration_days" class="form-control" min="1"
                                       value="<?php echo $package['duration_days']; ?>">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Max Persons</label>
                                <input type="number" name="max_persons" class="form-control" min="1"
                                       value="<?php echo $package['max_persons']; ?>">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Description</label>
                                <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($package['description']); ?></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">What's Included <small class="text-muted">(one item per line)</small></label>
                                <textarea name="inclusions" class="form-control" rows="4" placeholder="e.g.&#10;Return Airfare&#10;Hotel Accommodation&#10;Daily Breakfast"><?php echo htmlspecialchars($package['inclusions'] ?? ''); ?></textarea>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Package Image</label>
                                <?php if (!empty($package['image_name'])): ?>
                                <div class="mb-2">
                                    <img src="/globetrek/assets/images/<?php echo htmlspecialchars($package['image_name']); ?>"
                                         style="height:100px;object-fit:cover;border-radius:6px;" alt="Current Image">
                                    <small class="text-muted ms-2">Current image</small>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="image" class="form-control" accept="image/*">
                                <small class="text-muted">Leave empty to keep current image</small>
                            </div>
                            <div class="col-md-12 d-flex gap-2">
                                <button type="submit" name="update" class="btn btn-primary">
                                    <i class="bi bi-check-circle"></i> Update Package
                                </button>
                                <a href="manage_packages.php" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

<script src="/globetrek/assets/js/main.js"></script>
<?php include '../includes/footer.php'; ?>

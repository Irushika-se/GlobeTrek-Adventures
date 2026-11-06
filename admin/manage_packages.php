<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('admin');
 
$success = "";
$error   = "";
 
// Delete package
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("UPDATE packages SET status = 'inactive' WHERE id = $id");
    $success = "Package deactivated.";
}
 
// Add new package
if (isset($_POST['add_package'])) {
    $title      = clean($_POST['title']);
    $dest       = clean($_POST['destination']);
    $desc       = clean($_POST['description']);
    $price      = (float)$_POST['price'];
    $days       = (int)$_POST['duration_days'];
    $max        = (int)$_POST['max_persons'];
    $inclusions = clean($_POST['inclusions']);
    $image_name = 'package.jpg'; // default

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $ext       = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed   = ['jpg','jpeg','png','webp'];
        if (in_array($ext, $allowed)) {
            $image_name = 'pkg_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['image']['tmp_name'], '../assets/images/' . $image_name);
        }
    }

    if (empty($title) || empty($dest) || $price <= 0) {
        $error = "Please fill in all required fields.";
    } else {
        $conn->query("INSERT INTO packages (title, destination, description, price, duration_days, max_persons, inclusions, image_name)
                      VALUES ('$title','$dest','$desc',$price,$days,$max,'$inclusions','$image_name')");
        $success = "New package added successfully!";
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
            <h4 class="mb-4"><i class="bi bi-suitcase-lg"></i> Manage Tour Packages</h4>
 
            <?php if ($success): successMsg($success); endif; ?>
            <?php if ($error):   errorMsg($error);   endif; ?>
 
            <!-- Add Package Form -->
            <div class="card mb-4">
                <div class="card-header">Add New Package</div>
                <div class="card-body">
                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="row g-2">
                            <div class="col-md-4">
                                <input type="text" name="title" class="form-control" placeholder="Package Title *" required>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="destination" class="form-control" placeholder="Destination *" required>
                            </div>
                            <div class="col-md-2">
                                <input type="number" name="price" class="form-control" placeholder="Price (LKR) *" min="0" required>
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="duration_days" class="form-control" placeholder="Days" value="7" min="1">
                            </div>
                            <div class="col-md-1">
                                <input type="number" name="max_persons" class="form-control" placeholder="Max" value="10" min="1">
                            </div>
                            <div class="col-md-10">
                                <textarea name="description" class="form-control" rows="2" placeholder="Package description"></textarea>
                            </div>
                            <div class="col-md-10">
                                <textarea name="inclusions" class="form-control" rows="2" placeholder="What's Included (one item per line e.g. Hotel Accommodation)"></textarea>
                            </div>
                            <div class="col-md-10">
                                <label class="form-label text-muted small">Package Image (optional)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="add_package" class="btn btn-success w-100 h-100">Add Package</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
 
            <!-- Packages Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>#ID</th>
                                    <th>Title</th>
                                    <th>Destination</th>
                                    <th>Price</th>
                                    <th>Days</th>
                                    <th>Max</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = $conn->query("SELECT * FROM packages ORDER BY created_at DESC");
                                while ($p = $res->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $p['id']; ?></td>
                                    <td><?php echo clean($p['title']); ?></td>
                                    <td><?php echo clean($p['destination']); ?></td>
                                    <td><?php echo formatPrice($p['price']); ?></td>
                                    <td><?php echo $p['duration_days']; ?></td>
                                    <td><?php echo $p['max_persons']; ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $p['status'] == 'active' ? 'success' : 'secondary'; ?>">
                                            <?php echo ucfirst($p['status']); ?>
                                        </span>
                                    </td>
                                    <td class="d-flex gap-1">
                                        <a href="edit_package.php?id=<?php echo $p['id']; ?>"
                                           class="btn btn-sm btn-warning">Edit</a>
                                        <a href="?delete=<?php echo $p['id']; ?>"
                                           class="btn btn-sm btn-danger"
                                           onclick="return confirmDelete()">Remove</a>
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
 
<script src="/globetrek/assets/js/main.js"></script>
<?php include '../includes/footer.php'; ?>
 
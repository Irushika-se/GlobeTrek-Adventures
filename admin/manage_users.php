<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('admin');
 
$success = "";
$error   = "";
 
// Only staff accounts can be deleted
if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    $role_res = $conn->query("SELECT role FROM users WHERE id = $del_id");
    $role_row = $role_res->fetch_assoc();
    if ($role_row['role'] == 'staff') {
        $conn->query("DELETE FROM users WHERE id = $del_id");
        $success = "Staff account deleted successfully.";
    } else {
        $error = "Only staff accounts can be deleted.";
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
            <a href="manage_users.php" class="active"><i class="bi bi-people"></i> Users</a>
            <a href="manage_packages.php"><i class="bi bi-suitcase-lg"></i> Packages</a>
            <a href="bookings.php"><i class="bi bi-calendar-check"></i> Bookings</a>
            <a href="reports.php"><i class="bi bi-file-earmark-text"></i> Reports</a>
            <a href="/globetrek/logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
 
        <!-- Main Content -->
        <div class="col-md-10 dash-content">
            <h4 class="mb-4"><i class="bi bi-people"></i> Manage Users</h4>
 
            <?php if ($success): successMsg($success); endif; ?>
            <?php if ($error): errorMsg($error); endif; ?>
 
            <!-- Add Staff Form -->
            <div class="card mb-4">
                <div class="card-header">Add New Staff Account</div>
                <div class="card-body">
                    <?php
                    // Handle add staff form
                    if (isset($_POST['add_staff'])) {
                        $name  = clean($_POST['full_name']);
                        $email = clean($_POST['email']);
                        $phone = clean($_POST['phone']);
                        $pass  = password_hash($_POST['password'], PASSWORD_DEFAULT);
 
                        $check = $conn->query("SELECT id FROM users WHERE email='$email'");
                        if ($check->num_rows > 0) {
                            errorMsg("Email already exists.");
                        } else {
                            $conn->query("INSERT INTO users (full_name, email, phone, password, role)
                                          VALUES ('$name','$email','$phone','$pass','staff')");
                            successMsg("Staff account created!");
                        }
                    }
                    ?>
                    <form method="POST" action="">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <input type="text" name="full_name" class="form-control" placeholder="Full Name" required>
                            </div>
                            <div class="col-md-3">
                                <input type="email" name="email" class="form-control" placeholder="Email" required>
                            </div>
                            <div class="col-md-2">
                                <input type="text" name="phone" class="form-control" placeholder="Phone">
                            </div>
                            <div class="col-md-2">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" name="add_staff" class="btn btn-success w-100">Add Staff</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
 
            <!-- Users Table -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead class="table-primary">
                                <tr>
                                    <th>#ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $res = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
                                while ($u = $res->fetch_assoc()):
                                ?>
                                <tr>
                                    <td><?php echo $u['id']; ?></td>
                                    <td><?php echo clean($u['full_name']); ?></td>
                                    <td><?php echo clean($u['email']); ?></td>
                                    <td><?php echo clean($u['phone']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $u['role'] == 'admin' ? 'danger' : ($u['role'] == 'staff' ? 'warning text-dark' : 'primary'); ?>">
                                            <?php echo ucfirst($u['role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($u['created_at'])); ?></td>
                                    <td>
                                        <?php if ($u['role'] == 'staff'): ?>
                                            <a href="?delete=<?php echo $u['id']; ?>"
                                               class="btn btn-sm btn-danger"
                                               onclick="return confirm('Delete this staff account?')">Delete</a>
                                        <?php else: ?>
                                            <span class="text-muted small">-</span>
                                        <?php endif; ?>
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
 
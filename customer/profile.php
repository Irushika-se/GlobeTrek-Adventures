<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('customer');
 
$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";
 
// Fetch current user data
$user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = clean($_POST['full_name']);
    $phone     = clean($_POST['phone']);
    $new_pass  = $_POST['new_password'];
 
    if (empty($full_name)) {
        $error = "Full name is required.";
    } else {
        // Update name and phone
        $conn->query("UPDATE users SET full_name='$full_name', phone='$phone' WHERE id=$user_id");
 
        // Update password only if entered
        if (!empty($new_pass)) {
            if (strlen($new_pass) < 6) {
                $error = "New password must be at least 6 characters.";
            } else {
                $hashed = password_hash($new_pass, PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password='$hashed' WHERE id=$user_id");
            }
        }
 
        if (!$error) {
            $_SESSION['full_name'] = $full_name;
            $success = "Profile updated successfully!";
            // Refresh user data
            $user = $conn->query("SELECT * FROM users WHERE id = $user_id")->fetch_assoc();
        }
    }
}
?>
 
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
 
<div class="page-banner">
    <div class="container">
        <h2><i class="bi bi-person-gear"></i> My Profile</h2>
        <p class="mb-0">Update your personal information</p>
    </div>
</div>
 
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
 
                    <?php if ($success): successMsg($success); endif; ?>
                    <?php if ($error):   errorMsg($error);   endif; ?>
 
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="full_name" class="form-control"
                                   value="<?php echo clean($user['full_name']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" class="form-control"
                                   value="<?php echo clean($user['email']); ?>" disabled>
                            <small class="text-muted">Email cannot be changed.</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?php echo clean($user['phone']); ?>">
                        </div>
                        <hr>
                        <h6>Change Password <small class="text-muted">(leave blank to keep current)</small></h6>
                        <div class="mb-3">
                            <label class="form-label">New Password</label>
                            <input type="password" name="new_password" class="form-control"
                                   placeholder="At least 6 characters">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </form>
 
                </div>
            </div>
        </div>
    </div>
</div>
 
<?php include '../includes/footer.php'; ?>
 
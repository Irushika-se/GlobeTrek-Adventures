<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';
 
// If already logged in, redirect to appropriate dashboard
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
    } elseif ($_SESSION['role'] == 'staff') {
        header("Location: staff/dashboard.php");
    } else {
        header("Location: customer/dashboard.php");
    }
    exit();
}
 
$error = "";
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = clean($_POST['email']);
    $password = $_POST['password'];
 
    // Get user by email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);
 
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
 
        // Check password
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role']      = $user['role'];
 
            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: admin/dashboard.php");
            } elseif ($user['role'] == 'staff') {
                header("Location: staff/dashboard.php");
            } else {
                header("Location: customer/dashboard.php");
            }
            exit();
        } else {
            $error = "Incorrect password. Please try again.";
        }
    } else {
        $error = "No account found with that email.";
    }
}
?>
 
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
 
<div class="container d-flex justify-content-center align-items-center" style="min-height:70vh;">
    <div class="auth-card card p-4">
 
        <h3 class="text-center mb-1">
            <i class="bi bi-lock-fill text-primary"></i> Login
        </h3>
        <p class="text-center text-muted mb-4">Sign in to your GlobeTrek account</p>
 
        <!-- Show success message after registration -->
        <?php if (isset($_GET['registered']) && $_GET['registered'] == 1): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-2"></i>Account created successfully! Please log in.
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Show error if any -->
        <?php if ($error): ?>
            <?php errorMsg($error); ?>
        <?php endif; ?>
 
        <form method="POST" action="">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login</button>
            </div>
        </form>
 
        <p class="text-center mt-3 mb-0">
            Don't have an account? <a href="register.php">Register here</a>
        </p>
 
        </div>
</div>
 
<?php include 'includes/footer.php'; ?>
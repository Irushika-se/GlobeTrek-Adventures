<?php
include 'includes/db_connect.php';
include 'includes/functions.php';

if (session_status() == PHP_SESSION_NONE) session_start();
if (isset($_SESSION['user_id'])) redirect('customer/dashboard.php');

$error = $old_name = $old_email = $old_phone = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email    = trim($_POST['email']);
    $phone    = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm_password'];

    $old_name  = htmlspecialchars($fullname);
    $old_email = htmlspecialchars($email);
    $old_phone = htmlspecialchars($phone);

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!empty($phone) && !preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be exactly 10 digits.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "This email is already registered. <a href='login.php'>Login instead?</a>";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO users (full_name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
            $ins->bind_param("ssss", $fullname, $email, $phone, $hashed);
            if ($ins->execute()) {
                redirect('login.php?registered=1');
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    }
}

include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container d-flex justify-content-center align-items-center py-5" style="min-height:80vh;">
    <div class="card shadow" style="width:100%; max-width:520px; border-radius:14px;">

        <div class="card-header text-center py-3" style="background:linear-gradient(135deg,#0d6efd,#0dcaf0); border-radius:14px 14px 0 0;">
            <h4 class="mb-0 text-white"><i class="bi bi-person-plus me-2"></i>Create an Account</h4>
            <p class="text-white-50 small mb-0">Join GlobeTrek Adventures today</p>
        </div>

        <div class="card-body p-4">

            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle me-2"></i><?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" id="registerForm" novalidate>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" name="fullname" id="fullname" class="form-control" placeholder="Enter your full name" value="<?php echo $old_name; ?>" required>
                    </div>
                    <div class="text-danger small" id="err-name"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" id="reg_email" class="form-control" placeholder="your@email.com" value="<?php echo $old_email; ?>" required>
                    </div>
                    <div class="text-danger small" id="err-email"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-telephone"></i></span>
                        <input type="tel" name="phone" id="phone" class="form-control" placeholder="07XXXXXXXX" value="<?php echo $old_phone; ?>" maxlength="10">
                    </div>
                    <div class="text-danger small" id="err-phone"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="password" id="reg_password" class="form-control" placeholder="Minimum 6 characters" required>
                    </div>
                    <div class="mt-1" id="strength-wrap" style="display:none;">
                        <div class="progress" style="height:5px;"><div class="progress-bar" id="strength-bar"></div></div>
                        <small id="strength-label" class="text-muted"></small>
                    </div>
                    <div class="text-danger small" id="err-pw"></div>
                </div>

                <div class="mb-4">
                    <label class="form-label fw-semibold">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" placeholder="Repeat your password" required>
                    </div>
                    <div class="text-danger small" id="err-confirm"></div>
                </div>

                <div class="d-grid">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-check me-1"></i> Create Account
                    </button>
                </div>
            </form>

            <hr class="my-3">
            <p class="text-center text-muted small mb-0">
                Already have an account? <a href="login.php" class="fw-semibold">Login here</a>
            </p>
        </div>
    </div>
</div>

<script>
document.getElementById('reg_password').addEventListener('input', function () {
    const v = this.value, wrap = document.getElementById('strength-wrap');
    if (!v) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'block';
    let s = [v.length>=6, v.length>=10, /[A-Z]/.test(v), /[0-9]/.test(v), /[^A-Za-z0-9]/.test(v)].filter(Boolean).length;
    const lvl = [['20%','bg-danger','Very Weak'],['40%','bg-warning','Weak'],['60%','bg-info','Fair'],['80%','bg-primary','Strong'],['100%','bg-success','Very Strong']][s-1];
    const bar = document.getElementById('strength-bar');
    bar.style.width = lvl[0]; bar.className = 'progress-bar ' + lvl[1];
    document.getElementById('strength-label').textContent = lvl[2];
});

document.getElementById('registerForm').addEventListener('submit', function(e) {
    let ok = true;
    const show = (id, msg) => { document.getElementById(id).textContent = msg; ok = false; };
    const clear = id => document.getElementById(id).textContent = '';

    const name = document.getElementById('fullname').value.trim();
    const email = document.getElementById('reg_email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const pw = document.getElementById('reg_password').value;
    const cpw = document.getElementById('confirm_password').value;

    name  ? clear('err-name')  : show('err-name',  'Full name is required.');
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) ? clear('err-email') : show('err-email', 'Enter a valid email.');
    (!phone || /^[0-9]{10}$/.test(phone)) ? clear('err-phone') : show('err-phone', 'Phone must be 10 digits.');
    pw.length >= 6 ? clear('err-pw') : show('err-pw', 'Password must be at least 6 characters.');
    pw === cpw ? clear('err-confirm') : show('err-confirm', 'Passwords do not match.');

    if (!ok) e.preventDefault();
});
</script>

<?php include 'includes/footer.php'; ?>
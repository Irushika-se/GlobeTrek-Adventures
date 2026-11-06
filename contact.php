<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';

$success = "";
$error   = "";

// Block admin and staff from sending inquiries
$blocked = false;
if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin', 'staff'])) {
    $blocked = true;
}

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !$blocked) {
    $name    = clean($_POST['name']);
    $email   = clean($_POST['email']);
    $subject = clean($_POST['subject']);
    $message = clean($_POST['message']);
 
    if (empty($name) || empty($email) || empty($message)) {
        $error = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO inquiries (name, email, subject, message)
                VALUES ('$name', '$email', '$subject', '$message')";
 
        if ($conn->query($sql)) {
            $success = "Thank you! Your message has been sent. We will reply soon.";
        } else {
            $error = "Could not send message. Please try again.";
        }
    }
}
?>
 
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
 
<div class="page-banner">
    <div class="container">
        <h2>Contact Us</h2>
        <p class="mb-0">Have a question? We'd love to hear from you.</p>
    </div>
</div>
 
<div class="container mb-5">
    <div class="row">
 
        <!-- Contact Form -->
        <div class="col-md-7 mb-4">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-4">Send us a Message</h5>
 
                    <?php if ($success): successMsg($success); endif; ?>
                    <?php if ($blocked): ?>
                        <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-2"></i>Admin and Staff accounts cannot send inquiries through this form.</div>
                    <?php else: ?>
                    <?php if ($error):   errorMsg($error);   endif; ?>
 
                    <form method="POST" action="" onsubmit="return validateContact()">
                        <script src="/globetrek/assets/js/validation.js"></script>
 
                        <div class="mb-3">
                            <label class="form-label">Your Name <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" class="form-control"
                                   placeholder="Full name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email Address <span class="text-danger">*</span></label>
                            <input type="email" id="email" name="email" class="form-control"
                                   placeholder="your@email.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input type="text" name="subject" class="form-control"
                                   placeholder="E.g. Question about Bali package">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Message <span class="text-danger">*</span></label>
                            <textarea id="message" name="message" class="form-control" rows="5"
                                      placeholder="Write your message here..." required></textarea>
                        </div>
 
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-send"></i> Send Message
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
 
        <!-- Contact Info -->
        <div class="col-md-5 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body p-4">
                    <h5 class="mb-4">Our Contact Details</h5>
 
                    <div class="mb-3 d-flex align-items-start gap-3">
                        <i class="bi bi-geo-alt-fill text-primary fs-4"></i>
                        <div>
                            <strong>Address</strong>
                            <p class="text-muted mb-0">45 Marine Drive, Negombo<br>Sri Lanka</p>
                        </div>
                    </div>
 
                    <div class="mb-3 d-flex align-items-start gap-3">
                        <i class="bi bi-telephone-fill text-primary fs-4"></i>
                        <div>
                            <strong>Phone</strong>
                            <p class="text-muted mb-0">+94 31 223 4567</p>
                        </div>
                    </div>
 
                    <div class="mb-3 d-flex align-items-start gap-3">
                        <i class="bi bi-envelope-fill text-primary fs-4"></i>
                        <div>
                            <strong>Email</strong>
                            <p class="text-muted mb-0">info@globetrek.com</p>
                        </div>
                    </div>
 
                    <div class="mb-3 d-flex align-items-start gap-3">
                        <i class="bi bi-clock-fill text-primary fs-4"></i>
                        <div>
                            <strong>Office Hours</strong>
                            <p class="text-muted mb-0">Mon - Fri: 9am to 6pm<br>Sat: 9am to 1pm</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
 
    </div>
</div>
 
<?php include 'includes/footer.php'; ?>
 
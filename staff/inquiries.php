<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('staff');
 
$success = "";
 
// Handle reply submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['reply'])) {
    $inq_id = (int)$_POST['inq_id'];
    $reply  = clean($_POST['reply']);
 
    $conn->query("UPDATE inquiries SET reply='$reply', status='replied' WHERE id=$inq_id");
    $success = "Reply saved successfully!";
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
            <a href="inquiries.php" class="active"><i class="bi bi-envelope"></i> Inquiries</a>
            <a href="manage_bookings.php"><i class="bi bi-calendar-check"></i> Bookings</a>
            <a href="update_packages.php"><i class="bi bi-suitcase-lg"></i> Packages</a>
            <a href="/globetrek/logout.php" class="text-warning"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
 
        <!-- Main Content -->
        <div class="col-md-10 dash-content">
            <h4 class="mb-4"><i class="bi bi-envelope-open"></i> Customer Inquiries</h4>
 
            <?php if ($success): successMsg($success); endif; ?>
 
            <?php
            $res = $conn->query("SELECT * FROM inquiries ORDER BY submitted_at DESC");
            while ($inq = $res->fetch_assoc()):
            ?>
            <div class="card shadow-sm mb-3">
                <div class="card-header d-flex justify-content-between">
                    <span>
                        <strong><?php echo clean($inq['name']); ?></strong>
                        — <?php echo clean($inq['email']); ?>
                    </span>
                    <span>
                        <span class="badge bg-<?php echo $inq['status'] == 'open' ? 'warning text-dark' : 'success'; ?>">
                            <?php echo ucfirst($inq['status']); ?>
                        </span>
                        <small class="text-muted ms-2"><?php echo date('d M Y', strtotime($inq['submitted_at'])); ?></small>
                    </span>
                </div>
                <div class="card-body">
                    <p><strong>Subject:</strong> <?php echo clean($inq['subject']); ?></p>
                    <p><?php echo nl2br(clean($inq['message'])); ?></p>
 
                    <?php if ($inq['reply']): ?>
                        <div class="alert alert-success">
                            <strong>Your Reply:</strong><br>
                            <?php echo nl2br(clean($inq['reply'])); ?>
                        </div>
                    <?php else: ?>
                        <!-- Reply form -->
                        <form method="POST" action="">
                            <input type="hidden" name="inq_id" value="<?php echo $inq['id']; ?>">
                            <div class="mb-2">
                                <textarea name="reply" class="form-control" rows="2"
                                          placeholder="Type your reply here..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-sm btn-primary">Send Reply</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
            <?php endwhile; ?>
 
        </div>
    </div>
</div>
 
<?php include '../includes/footer.php'; ?>
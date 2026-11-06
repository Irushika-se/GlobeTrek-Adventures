<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';
 
requireRole('customer');
 
$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";
 
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $destination  = clean($_POST['destination']);
    $travel_date  = clean($_POST['travel_date']);
    $num_persons  = (int)$_POST['num_persons'];
    $budget       = (float)$_POST['budget'];
    $preferences  = clean($_POST['preferences']);
 
    if (empty($destination) || empty($travel_date)) {
        $error = "Destination and travel date are required.";
    } else {
        $sql = "INSERT INTO custom_trips (customer_id, destination, travel_date, num_persons, budget, preferences)
                VALUES ($user_id, '$destination', '$travel_date', $num_persons, $budget, '$preferences')";
 
        if ($conn->query($sql)) {
            $success = "Your custom trip request has been submitted! Our team will contact you soon.";
        } else {
            $error = "Could not submit request. Please try again.";
        }
    }
}
?>
 
<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>
 
<div class="page-banner">
    <div class="container">
        <h2><i class="bi bi-sliders"></i> Customize Your Trip</h2>
        <p class="mb-0">Tell us your dream destination and we'll plan it for you</p>
    </div>
</div>
 
<div class="container mb-5">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <p class="text-muted mb-4">
                        Can't find the package you want? Fill this form and our travel consultants
                        will create a custom trip just for you!
                    </p>
 
                    <?php if ($success): successMsg($success); endif; ?>
                    <?php if ($error):   errorMsg($error);   endif; ?>
 
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label">Desired Destination <span class="text-danger">*</span></label>
                            <input type="text" name="destination" class="form-control"
                                   placeholder="E.g. Tokyo, Japan" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preferred Travel Date <span class="text-danger">*</span></label>
                            <input type="date" name="travel_date" class="form-control"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Number of Persons</label>
                            <input type="number" name="num_persons" class="form-control"
                                   value="1" min="1" max="50">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Budget (LKR)</label>
                            <input type="number" name="budget" class="form-control"
                                   placeholder="E.g. 100000" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preferences & Special Requirements</label>
                            <textarea name="preferences" class="form-control" rows="4"
                                      placeholder="E.g. honeymoon package, vegetarian meals, beach hotel, adventure activities..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-send"></i> Submit Trip Request
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
 
<?php include '../includes/footer.php'; ?>
 
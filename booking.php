<?php
session_start();
include 'includes/db_connect.php';
include 'includes/functions.php';
include 'includes/auth.php';
 
// Only customers can book
requireRole('customer');
 
// Get package ID
if (!isset($_GET['id'])) {
    header("Location: packages.php");
    exit();
}
 
$pkg_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];
 
// Fetch the package
$sql = "SELECT * FROM packages WHERE id = $pkg_id AND status = 'active'";
$result = $conn->query($sql);
 
if ($result->num_rows == 0) {
    header("Location: packages.php");
    exit();
}
 
$pkg = $result->fetch_assoc();
 
$success = "";
$error   = "";
 
// Handle booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $travel_date     = clean($_POST['travel_date']);
    $num_persons     = (int)$_POST['num_persons'];
    $special_req     = clean($_POST['special_requests']);
    $total_price     = $pkg['price'] * $num_persons;
 
    // Validate
    if (empty($travel_date)) {
        $error = "Please select a travel date.";
    } elseif ($num_persons < 1 || $num_persons > $pkg['max_persons']) {
        $error = "Number of persons must be between 1 and " . $pkg['max_persons'] . ".";
    } else {
        // Save booking to database
        $sql = "INSERT INTO bookings (customer_id, package_id, travel_date, num_persons, total_price, special_requests)
                VALUES ($user_id, $pkg_id, '$travel_date', $num_persons, $total_price, '$special_req')";
 
        if ($conn->query($sql)) {
            $booking_id = $conn->insert_id;
            // Redirect to payment page
            header("Location: customer/payment.php?booking_id=$booking_id");
            exit();
        } else {
            $error = "Could not save booking. Please try again.";
        }
    }
}
?>
 
<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>
 
<div class="page-banner">
    <div class="container">
        <h2><i class="bi bi-calendar-check"></i> Book Your Trip</h2>
        <p class="mb-0"><?php echo clean($pkg['title']); ?></p>
    </div>
</div>
 
<div class="container mb-5">
    <div class="row">
 
        <!-- Booking Form -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-4">Fill in Your Travel Details</h5>
 
                    <?php if ($error): errorMsg($error); endif; ?>
 
                    <form method="POST" action="" onsubmit="return validateBooking()">
                        <script src="/globetrek/assets/js/validation.js"></script>
 
                        <div class="mb-3">
                            <label class="form-label">Travel Date <span class="text-danger">*</span></label>
                            <input type="date" id="travel_date" name="travel_date" class="form-control"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Number of Persons <span class="text-danger">*</span></label>
                            <input type="number" id="num_persons" name="num_persons"
                                   class="form-control" value="1" min="1"
                                   max="<?php echo $pkg['max_persons']; ?>"
                                   onchange="calculateTotal()" required>
                            <small class="text-muted">Maximum <?php echo $pkg['max_persons']; ?> persons</small>
                        </div>
 
                        <!-- Hidden fields for calculation -->
                        <input type="hidden" id="price_per_person" value="<?php echo $pkg['price']; ?>">
                        <input type="hidden" id="total_price" name="total_price" value="<?php echo $pkg['price']; ?>">
 
                        <!-- Total price display -->
                        <div class="alert alert-primary mb-3">
                            <strong>Total Price: <span id="total_price_display"><?php echo formatPrice($pkg['price']); ?></span></strong>
                        </div>
 
                        <div class="mb-3">
                            <label class="form-label">Special Requests (optional)</label>
                            <textarea name="special_requests" class="form-control" rows="3"
                                      placeholder="E.g. vegetarian meals, wheelchair access..."></textarea>
                        </div>
 
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-check-circle"></i> Confirm Booking & Proceed to Payment
                        </button>
                    </form>
                </div>
            </div>
        </div>
 
        <!-- Package Summary -->
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Package Summary</h6>
                </div>
                <div class="card-body">
                    <h6><?php echo clean($pkg['title']); ?></h6>
                    <p class="text-muted mb-1">
                        <i class="bi bi-geo-alt"></i> <?php echo clean($pkg['destination']); ?>
                    </p>
                    <p class="text-muted mb-1">
                        <i class="bi bi-calendar3"></i> <?php echo $pkg['duration_days']; ?> Days
                    </p>
                    <p class="text-muted mb-2">
                        <i class="bi bi-people"></i> Max <?php echo $pkg['max_persons']; ?> Persons
                    </p>
                    <hr>
                    <p class="fw-bold">
                        Price per person: <span class="text-primary"><?php echo formatPrice($pkg['price']); ?></span>
                    </p>
                </div>
            </div>
        </div>
 
    </div>
</div>
 
<?php include 'includes/footer.php'; ?>
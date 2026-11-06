<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';

requireRole('customer');

$user_id = $_SESSION['user_id'];

// Get booking ID
if (!isset($_GET['booking_id'])) {
    header("Location: dashboard.php");
    exit();
}

$booking_id = (int)$_GET['booking_id'];

// Fetch booking that belongs to this customer
$sql = "SELECT b.*, p.title, p.destination FROM bookings b
        JOIN packages p ON b.package_id = p.id
        WHERE b.id = $booking_id AND b.customer_id = $user_id";
$res = $conn->query($sql);

if ($res->num_rows == 0) {
    header("Location: dashboard.php");
    exit();
}

$booking = $res->fetch_assoc();
$success  = "";
$error    = "";

// Block if booking is not pending (already confirmed or cancelled)
if ($booking['status'] !== 'pending') {
    header("Location: my_bookings.php");
    exit();
}

// Check if payment already exists for this booking
$pay_check = $conn->query("SELECT id FROM payments WHERE booking_id = $booking_id AND payment_status = 'paid'");
if ($pay_check->num_rows > 0) {
    header("Location: my_bookings.php");
    exit();
}

// Handle payment form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $method = clean($_POST['payment_method']);

    // Save payment record
    $amount = $booking['total_price'];
    $sql = "INSERT INTO payments (booking_id, amount, payment_method, payment_status)
            VALUES ($booking_id, $amount, '$method', 'paid')";

    if ($conn->query($sql)) {
        // Update booking status to confirmed
        $conn->query("UPDATE bookings SET status = 'confirmed' WHERE id = $booking_id");
        $success = "Payment successful! Your booking is now confirmed.";
        // Refresh booking data after confirmation
        $res2 = $conn->query("SELECT b.*, p.title, p.destination FROM bookings b
                              JOIN packages p ON b.package_id = p.id
                              WHERE b.id = $booking_id");
        $booking = $res2->fetch_assoc();
    } else {
        $error = "Payment failed. Please try again.";
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="page-banner">
    <div class="container">
        <h2><i class="bi bi-credit-card"></i> Payment</h2>
        <p class="mb-0">Complete your payment to confirm the booking</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row">

        <!-- Payment Form -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-4">Payment Details</h5>

                    <?php if ($success): ?>
                        <?php successMsg($success); ?>
                        <div class="text-center mt-3">
                            <a href="my_bookings.php" class="btn btn-primary">View My Bookings</a>
                        </div>
                    <?php else: ?>

                        <?php if ($error): errorMsg($error); endif; ?>

                        <form method="POST" action="">
                            <div class="mb-3">
                                <label class="form-label">Select Payment Method</label>
                                <select name="payment_method" class="form-select" required>
                                    <option value="">-- Choose --</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <!-- Card number (display only - not saved) -->
                            <div class="mb-3">
                                <label class="form-label">Card Number</label>
                                <input type="text" class="form-control" placeholder="**** **** **** ****"
                                       maxlength="19">
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Expiry Date</label>
                                    <input type="text" class="form-control" placeholder="MM/YY">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">CVV</label>
                                    <input type="text" class="form-control" placeholder="***" maxlength="3">
                                </div>
                            </div>

                            <button type="submit" class="btn btn-success btn-lg w-100">
                                <i class="bi bi-lock-fill"></i> Pay <?php echo formatPrice($booking['total_price']); ?>
                            </button>
                        </form>

                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="col-md-5">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">Order Summary</h6>
                </div>
                <div class="card-body">
                    <p><strong>Package:</strong> <?php echo clean($booking['title']); ?></p>
                    <p><strong>Destination:</strong> <?php echo clean($booking['destination']); ?></p>
                    <p><strong>Travel Date:</strong> <?php echo $booking['travel_date']; ?></p>
                    <p><strong>Persons:</strong> <?php echo $booking['num_persons']; ?></p>
                    <hr>
                    <p class="fs-5"><strong>Total: <span class="text-success"><?php echo formatPrice($booking['total_price']); ?></span></strong></p>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include '../includes/footer.php'; ?>

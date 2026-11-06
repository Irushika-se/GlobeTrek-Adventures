<?php
session_start();
include '../includes/db_connect.php';
include '../includes/functions.php';
include '../includes/auth.php';

requireRole('customer');

$user_id = $_SESSION['user_id'];
$success = "";
$error   = "";

// Validate booking ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: my_bookings.php");
    exit();
}

$b_id = (int)$_GET['id'];

// Fetch the booking (must belong to this customer and be pending)
$sql = "SELECT b.*, p.title, p.destination, p.price, p.max_persons, p.duration_days
        FROM bookings b
        JOIN packages p ON b.package_id = p.id
        WHERE b.id = $b_id AND b.customer_id = $user_id AND b.status = 'pending'";
$res = $conn->query($sql);

if ($res->num_rows == 0) {
    header("Location: my_bookings.php");
    exit();
}

$booking = $res->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $travel_date  = clean($_POST['travel_date']);
    $num_persons  = (int)$_POST['num_persons'];
    $special_req  = clean($_POST['special_requests']);
    $total_price  = $booking['price'] * $num_persons;

    if (empty($travel_date)) {
        $error = "Please select a travel date.";
    } elseif ($num_persons < 1 || $num_persons > $booking['max_persons']) {
        $error = "Number of persons must be between 1 and " . $booking['max_persons'] . ".";
    } else {
        $upd = "UPDATE bookings
                SET travel_date = '$travel_date',
                    num_persons = $num_persons,
                    total_price = $total_price,
                    special_requests = '$special_req'
                WHERE id = $b_id AND customer_id = $user_id AND status = 'pending'";

        if ($conn->query($upd)) {
            // Redirect to payment page after successful update
            header("Location: payment.php?booking_id=$b_id");
            exit();
        } else {
            $error = "Could not update booking. Please try again.";
        }
    }
}
?>

<?php include '../includes/header.php'; ?>
<?php include '../includes/navbar.php'; ?>

<div class="page-banner">
    <div class="container">
        <h2><i class="bi bi-pencil-square"></i> Edit Booking #<?php echo $b_id; ?></h2>
        <p class="mb-0">Update your travel details for <?php echo clean($booking['title']); ?></p>
    </div>
</div>

<div class="container mb-5">

    <?php if ($success): successMsg($success); endif; ?>
    <?php if ($error):   errorMsg($error);   endif; ?>

    <div class="row">

        <!-- Edit Form -->
        <div class="col-md-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="mb-4">Update Travel Details</h5>

                    <form method="POST" action="">

                        <div class="mb-3">
                            <label class="form-label">Travel Date <span class="text-danger">*</span></label>
                            <input type="date" name="travel_date" class="form-control"
                                   value="<?php echo $booking['travel_date']; ?>"
                                   min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Number of Persons <span class="text-danger">*</span></label>
                            <input type="number" id="num_persons" name="num_persons"
                                   class="form-control"
                                   value="<?php echo $booking['num_persons']; ?>"
                                   min="1" max="<?php echo $booking['max_persons']; ?>"
                                   onchange="updateTotal()" required>
                            <small class="text-muted">Maximum <?php echo $booking['max_persons']; ?> persons</small>
                        </div>

                        <!-- Hidden price per person -->
                        <input type="hidden" id="price_per_person" value="<?php echo $booking['price']; ?>">

                        <!-- Total price display -->
                        <div class="alert alert-primary mb-3">
                            <strong>Total Price: <span id="total_price_display">
                                LKR <?php echo number_format($booking['total_price'], 2); ?>
                            </span></strong>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Special Requests (optional)</label>
                            <textarea name="special_requests" class="form-control" rows="3"
                                      placeholder="E.g. vegetarian meals, wheelchair access..."><?php echo clean($booking['special_requests']); ?></textarea>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-fill">
                                <i class="bi bi-check-circle"></i> Save Changes
                            </button>
                            <a href="my_bookings.php" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x"></i> Cancel
                            </a>
                        </div>

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
                    <h6><?php echo clean($booking['title']); ?></h6>
                    <p class="text-muted mb-1">
                        <i class="bi bi-geo-alt"></i> <?php echo clean($booking['destination']); ?>
                    </p>
                    <p class="text-muted mb-1">
                        <i class="bi bi-calendar3"></i> <?php echo $booking['duration_days']; ?> Days
                    </p>
                    <p class="text-muted mb-2">
                        <i class="bi bi-people"></i> Max <?php echo $booking['max_persons']; ?> Persons
                    </p>
                    <hr>
                    <p class="fw-bold">
                        Price per person: <span class="text-primary">LKR <?php echo number_format($booking['price'], 2); ?></span>
                    </p>
                    <span class="badge bg-warning text-dark">Pending</span>
                </div>
            </div>
        </div>

    </div>

    <a href="my_bookings.php" class="btn btn-outline-secondary mt-3">
        <i class="bi bi-arrow-left"></i> Back to My Bookings
    </a>
</div>

<script>
function updateTotal() {
    const persons = parseInt(document.getElementById('num_persons').value) || 1;
    const price   = parseFloat(document.getElementById('price_per_person').value) || 0;
    const total   = persons * price;
    document.getElementById('total_price_display').textContent =
        'LKR ' + total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
}
</script>

<?php include '../includes/footer.php'; ?>

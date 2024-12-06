<?php
include '../includes/connection.php';
session_start();

$service = $_SESSION['service'];
$price = $_SESSION['price'];
$address = $_POST['address']; // This should be coming from the hidden form field in book_now.php
$user_id = $_SESSION['user_id']; // Ensure you have this stored in the session

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $payment_method = $_POST['payment_method'];

    // Insert booking and payment details into the database
    $sql = "INSERT INTO bookings (user_id, service, price, address, payment_method, booking_time) 
            VALUES ('$user_id', '$service', '$price', '$address', '$payment_method', NOW())";

    if (mysqli_query($db, $sql)) {
        echo "<script>alert('Payment successful! Your booking is confirmed.'); window.location.href = 'confirmation.php';</script>";
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($db);
    }

    mysqli_close($db);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - Car Wash Service</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h1 class="text-center mt-4 mb-4">Payment - Car Wash Service</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Payment Details</h5>
            <p class="card-text"><strong>Service:</strong> <?php echo htmlspecialchars($service); ?></p>
            <p class="card-text"><strong>Total Price:</strong> $<?php echo htmlspecialchars($price); ?></p>
            <p class="card-text"><strong>Address:</strong> <?php echo htmlspecialchars($address); ?></p>

            <form method="POST" action="payment.php">
                <div class="form-group">
                    <label for="payment_method">Select Payment Method:</label>
                    <select class="form-control" id="payment_method" name="payment_method" required>
                        <option value="Cash">Cash</option>
                        <option value="Online Transfer">Online Transfer</option>
                        <option value="Online Payment">Online Payment</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Pay Now</button>
            </form>
            <a href="book_now.php" class="btn btn-outline-danger btn-block mt-3">Cancel</a>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

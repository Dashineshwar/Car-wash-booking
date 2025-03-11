<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

$user_id = $_SESSION['id']; // Get logged-in user ID

// Fetch pending reviews for the user
$pendingReviewsQuery = "SELECT r.review_id, b.service_type, b.booking_time 
                        FROM reviews r 
                        JOIN booking b ON r.booking_id = b.booking_id 
                        WHERE r.user_id = ? AND r.review_status = 'pending'";
$stmt = $conn->prepare($pendingReviewsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch live bookings for the user
$liveBookingsQuery = "SELECT booking_id, service_type, booking_type, booking_time 
                      FROM booking 
                      WHERE user_id = ? AND status = 'pending' 
                      ORDER BY booking_time DESC";
$stmtLive = $conn->prepare($liveBookingsQuery);
$stmtLive->bind_param("i", $user_id);
$stmtLive->execute();
$resultLive = $stmtLive->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
<style>
html, body {
    font-family: 'Poppins', sans-serif;
    background-color: #fff;
    margin: 0;
    padding: 0;
}

.container {
    max-width: 900px;
    margin: auto;
    padding: 20px;
}

.card {
    background-color: #fff;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    margin-bottom: 20px;
}

.btn-custom {
    background-color: rgba(0, 0, 0, 0.1);
    color: #000;
    border: none;
    padding: 10px 15px;
    border-radius: 5px;
    cursor: pointer;
    transition: background 0.3s;
}

.btn-custom:hover {
    background-color: rgba(0, 0, 0, 0.2);
}

@media (max-width: 768px) {
    .row { text-align: center; }
    .card h4 { text-align: center; }
}

@media (min-width: 992px) {
    .card h4 { text-align: left; }
}
</style>
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <h4>Book a Car Wash</h4>
                <a href="welcome.php" class="btn btn-custom">Book Now</a>
            </div>
        </div>
        
        <!-- Live Bookings Section -->
        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <h4>Live Bookings</h4>
                <ul>
                    <?php if ($resultLive->num_rows > 0): ?>
                        <?php while ($rowLive = $resultLive->fetch_assoc()): ?>
                            <li>
                                <?php echo htmlspecialchars($rowLive['service_type']) . " - " . htmlspecialchars($rowLive['booking_type']); ?> 
                                (<?php echo date('d M Y, h:i A', strtotime($rowLive['booking_time'])); ?>)
                                <a href="reschedule_booking.php?booking_id=<?php echo $rowLive['booking_id']; ?>" class="btn btn-sm btn-custom">Reschedule</a>
                                
                            </li>
                        <?php endwhile; ?>
                        <a href="view_booking.php" class="btn btn-sm btn-custom">View all bookings</a>
                    <?php else: ?>
                        
                        <li>No live bookings.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Pending Reviews Section -->
        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <h4>Pending Reviews</h4>
                <ul>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <li>
                                Review for <?php echo htmlspecialchars($row['service_type']); ?> 
                                (<?php echo date('d M Y, h:i A', strtotime($row['booking_time'])); ?>)
                                <a href="submit_review.php?review_id=<?php echo $row['review_id']; ?>" class="btn btn-sm btn-custom">Leave Review</a>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li>No pending reviews.</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <div class="col-md-6 col-12 mb-4">
            <div class="card">
                <h4>Leave us a Feedback</h4>
                <a href="feedback.php" class="btn btn-custom">Give feedback now</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>

<?php
$stmt->close();
$stmtLive->close();
$conn->close();
?>

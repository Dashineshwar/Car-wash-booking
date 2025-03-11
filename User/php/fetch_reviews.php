<?php
include '../includes/session.php';
include '../includes/connection.php';

if (!isset($_SESSION['id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['id'];

// Fetch pending reviews for the user
$pendingReviewsQuery = "SELECT r.review_id, b.service_type, b.booking_time 
                        FROM reviews r 
                        JOIN booking b ON r.booking_id = b.booking_id 
                        WHERE r.user_id = ? AND r.review_status = 'pending'";
$stmt = $conn->prepare($pendingReviewsQuery);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Reviews</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 700px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: rgba(0, 0, 0, 0.1);
            color: black;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-custom:hover {
            background-color: rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Pending Reviews</h2>
    <hr>

    <ul class="list-group">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong><?php echo htmlspecialchars($row['service_type']); ?></strong> <br>
                        <small><?php echo date('d M Y, h:i A', strtotime($row['booking_time'])); ?></small>
                    </span>
                    <a href="submit_review.php?review_id=<?php echo $row['review_id']; ?>" class="btn btn-custom">Leave Review</a>
                </li>
            <?php endwhile; ?>
        <?php else: ?>
            <li class="list-group-item text-center">No pending reviews.</li>
        <?php endif; ?>
    </ul>

    <div class="text-center mt-3">
        <a href="home.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
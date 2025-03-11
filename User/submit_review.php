<?php
include '../includes/session.php';
include '../includes/connection.php';

if (!isset($_SESSION['id'])) {
    die("Unauthorized access.");
}

$user_id = $_SESSION['id'];
$review_id = isset($_GET['review_id']) ? intval($_GET['review_id']) : 0;

// Fetch review details
$reviewQuery = "SELECT r.review_id, b.service_type, b.booking_time 
                FROM reviews r 
                JOIN booking b ON r.booking_id = b.booking_id 
                WHERE r.review_id = ? AND r.user_id = ? AND r.review_status = 'pending'";
$stmt = $conn->prepare($reviewQuery);
$stmt->bind_param("ii", $review_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$review = $result->fetch_assoc();

if (!$review) {
    die("Invalid review or already submitted.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = intval($_POST['rating']);
    $review_text = trim($_POST['review_text']);

    if ($rating < 1 || $rating > 5) {
        die("Invalid rating value.");
    }

    // Update review in the database
    $updateReviewQuery = "UPDATE reviews SET rating = ?, review_text = ?, review_status = 'completed' WHERE review_id = ? AND user_id = ?";
    $stmt = $conn->prepare($updateReviewQuery);
    $stmt->bind_param("isii", $rating, $review_text, $review_id, $user_id);
    if ($stmt->execute()) {
        header("Location: home.php?review_success");
        exit();
    } else {
        die("Failed to submit review.");
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Review</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
        .btn-custom {
            background-color: rgba(0, 0, 0, 0.1);
            color: black;
            border: none;
            padding: 10px 15px;
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
        <h2 class="text-center">Submit Review</h2>
        <hr>
        <p><strong>Service:</strong> <?php echo htmlspecialchars($review['service_type']); ?></p>
        <p><strong>Booking Time:</strong> <?php echo date('d M Y, h:i A', strtotime($review['booking_time'])); ?></p>
        
        <form method="POST">
            <div class="form-group">
                <label for="rating">Rating (1-5):</label>
                <select name="rating" id="rating" class="form-control" required>
                    <option value="">Select Rating</option>
                    <option value="1">1 - Poor</option>
                    <option value="2">2 - Fair</option>
                    <option value="3">3 - Good</option>
                    <option value="4">4 - Very Good</option>
                    <option value="5">5 - Excellent</option>
                </select>
            </div>
            <div class="form-group">
                <label for="review_text">Your Review:</label>
                <textarea name="review_text" id="review_text" class="form-control" rows="4" placeholder="Write your feedback..." required></textarea>
            </div>
            <button type="submit" class="btn btn-custom">Submit Review</button>
        </form>
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

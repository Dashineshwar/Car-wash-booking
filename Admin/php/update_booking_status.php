<?php
include '../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    if (empty($booking_id) || empty($status)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        exit();
    }

    // Update the booking status
    $query = "UPDATE booking SET status = '$status' WHERE booking_id = '$booking_id'";
    if (mysqli_query($conn, $query)) {
        if ($status === 'done') {
            // Fetch booking details
            $selectQuery = "SELECT rider_id, price FROM booking WHERE booking_id = '$booking_id'";
            $result = mysqli_query($conn, $selectQuery);

            if ($result && mysqli_num_rows($result) > 0) {
                $row = mysqli_fetch_assoc($result);
                $rider_id = $row['rider_id'];
                $price = $row['price'];

                // Calculate shares
                $rider_share = $price * 0.8;
                $company_share = $price * 0.2;

                // Update rider wallet
                $updateRiderWallet = "UPDATE rider SET wallet = wallet + $rider_share WHERE rider_id = '$rider_id'";
                mysqli_query($conn, $updateRiderWallet);

                // Update company wallet
                $updateCompanyWallet = "UPDATE wallet SET wallet = wallet + $company_share WHERE id = 1";
                mysqli_query($conn, $updateCompanyWallet);

                // Step 6: Insert a new review entry for this booking
                $insertReviewQuery = "INSERT INTO reviews (booking_id, user_id, review_status) VALUES (?, ?, 'pending')";
                $insertReviewStmt = $conn->prepare($insertReviewQuery);
                $insertReviewStmt->bind_param("ii", $booking_id, $user_id);

                if (!$insertReviewStmt->execute()) {
                    throw new Exception("Failed to insert review entry: " . $insertReviewStmt->error);
                }
                $insertCompanyStmt->execute(); // Insert company transaction

                echo json_encode(['status' => 'success', 'message' => 'Status updated and wallets adjusted']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Booking not found']);
            }
        } else {
            echo json_encode(['status' => 'success', 'message' => 'Status updated']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
    }

    mysqli_close($conn);
}

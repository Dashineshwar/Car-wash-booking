<?php
include '../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    if (empty($booking_id) || empty($status)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        exit();
    }

    // Debugging: Output booking_id and status
    error_log("Booking ID: $booking_id, Status: $status");

    // Begin transaction for atomicity
    mysqli_begin_transaction($conn);
    try {
        // Update the booking status in the database
        $updateQuery = "UPDATE booking SET status = ? WHERE booking_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $status, $booking_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update booking status: " . $stmt->error);
        }

        if ($status === 'done') {
            // Fetch all required data for the completed booking
            $selectQuery = "SELECT rider_id, price, user_id, service_type, booking_type, payment_status FROM booking WHERE booking_id = ?";
            $selectStmt = $conn->prepare($selectQuery);
            $selectStmt->bind_param("i", $booking_id);
            $selectStmt->execute();
            $result = $selectStmt->get_result();

            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $rider_id = $row['rider_id'];
                $price = $row['price'];
                $user_id = $row['user_id'];
                $service_type = $row['service_type'];
                $booking_type = $row['booking_type'];
                $payment_status = $row['payment_status'];

                // Calculate 80% for the rider and 20% for the company
                $rider_share = $price * 0.8;
                $company_share = $price * 0.2;

                // Update the rider's wallet
                $updateRiderWalletQuery = "UPDATE rider SET wallet = wallet + ? WHERE rider_id = ?";
                $updateRiderStmt = $conn->prepare($updateRiderWalletQuery);
                $updateRiderStmt->bind_param("di", $rider_share, $rider_id);
                if (!$updateRiderStmt->execute()) {
                    throw new Exception("Failed to update rider's wallet: " . $updateRiderStmt->error);
                }

                // Update the company wallet
                $updateCompanyWalletQuery = "UPDATE wallet SET wallet = wallet + ? WHERE id = 1"; // Assuming company wallet ID = 1
                $updateCompanyStmt = $conn->prepare($updateCompanyWalletQuery);
                $updateCompanyStmt->bind_param("d", $company_share);
                if (!$updateCompanyStmt->execute()) {
                    throw new Exception("Failed to update company wallet: " . $updateCompanyStmt->error);
                }

                // Fetch updated wallet balances for rider and company
                $riderWalletQuery = "SELECT wallet FROM rider WHERE rider_id = ?";
                $riderWalletStmt = $conn->prepare($riderWalletQuery);
                $riderWalletStmt->bind_param("i", $rider_id);
                $riderWalletStmt->execute();
                $riderWalletResult = $riderWalletStmt->get_result();
                $rider_wallet = 0;
                if ($riderWalletResult && $riderWalletResult->num_rows > 0) {
                    $riderWalletRow = $riderWalletResult->fetch_assoc();
                    $rider_wallet = $riderWalletRow['wallet'];
                }

                $companyWalletQuery = "SELECT wallet FROM wallet WHERE id = 1"; // Assuming company wallet ID = 1
                $companyWalletStmt = $conn->prepare($companyWalletQuery);
                $companyWalletStmt->execute();
                $companyWalletResult = $companyWalletStmt->get_result();
                $company_wallet = 0;
                if ($companyWalletResult && $companyWalletResult->num_rows > 0) {
                    $companyWalletRow = $companyWalletResult->fetch_assoc();
                    $company_wallet = $companyWalletRow['wallet'];
                }

                // Log the successful transaction into the transactions table
                $transactionInsertQuery = "INSERT INTO transactions (booking_id, rider_id, user_id, amount, previous_amount, current_amount, payment_status, service_type, booking_type, transaction_time)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $insertStmt = $conn->prepare($transactionInsertQuery);
                $previous_amount = $rider_wallet - $rider_share; // Calculate previous wallet amount
                $insertStmt->bind_param("iiidddsss", $booking_id, $rider_id, $user_id, $price, $previous_amount, $rider_wallet, $payment_status, $service_type, $booking_type);
                if (!$insertStmt->execute()) {
                    throw new Exception("Failed to insert transaction: " . $insertStmt->error);
                }

                // Commit transaction
                mysqli_commit($conn);
                echo json_encode(['status' => 'success', 'rider_wallet' => $rider_wallet, 'company_wallet' => $company_wallet]);
            } else {
                throw new Exception("Booking not found");
            }
        } else {
            echo json_encode(['status' => 'success']); // Just return success for non-'done' status updates
        }
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        error_log($e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } finally {
        // Close the connection
        $stmt->close();
        mysqli_close($conn);
    }
}
?>

<?php
include '../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = $_POST['booking_id'];
    $status = $_POST['status'];

    if (empty($booking_id) || empty($status)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing data']);
        exit();
    }

    mysqli_begin_transaction($conn);
    try {
        // Step 1: Update booking status
        $updateQuery = "UPDATE booking SET status = ? WHERE booking_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $status, $booking_id);

        if (!$stmt->execute()) {
            throw new Exception("Failed to update booking status: " . $stmt->error);
        }

        if ($status === 'done') {
            // Fetch booking data
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

                // Calculate amounts
                $agent_amount = $price * 0.8; // Rider's share
                $company_amount = $price * 0.2; // Company's share

                // Step 2: Fetch rider's wallet balance (previous amount)
                $riderWalletQuery = "SELECT wallet FROM rider WHERE rider_id = ?";
                $riderWalletStmt = $conn->prepare($riderWalletQuery);
                $riderWalletStmt->bind_param("i", $rider_id);
                $riderWalletStmt->execute();
                $riderWalletResult = $riderWalletStmt->get_result();
                $previous_rider_wallet = 0;

                if ($riderWalletResult && $riderWalletResult->num_rows > 0) {
                    $walletRow = $riderWalletResult->fetch_assoc();
                    $previous_rider_wallet = $walletRow['wallet'];
                } else {
                    throw new Exception("Rider wallet not found");
                }

                // Update rider's wallet
                $updateRiderWalletQuery = "UPDATE rider SET wallet = wallet + ? WHERE rider_id = ?";
                $updateRiderWalletStmt = $conn->prepare($updateRiderWalletQuery);
                $updateRiderWalletStmt->bind_param("di", $agent_amount, $rider_id);
                if (!$updateRiderWalletStmt->execute()) {
                    throw new Exception("Failed to update rider's wallet: " . $updateRiderWalletStmt->error);
                }

                // Step 3: Fetch company wallet balance (previous amount)
                $companyWalletQuery = "SELECT wallet FROM wallet WHERE id = 1"; // Assuming company ID is 1
                $companyWalletResult = mysqli_query($conn, $companyWalletQuery);
                $previous_company_wallet = 0;

                if ($companyWalletResult && mysqli_num_rows($companyWalletResult) > 0) {
                    $companyWalletRow = mysqli_fetch_assoc($companyWalletResult);
                    $previous_company_wallet = $companyWalletRow['wallet'];
                } else {
                    throw new Exception("Company wallet not found");
                }

                // Update company's wallet
                $updateCompanyWalletQuery = "UPDATE wallet SET wallet = wallet + ? WHERE id = 1";
                $updateCompanyStmt = $conn->prepare($updateCompanyWalletQuery);
                $updateCompanyStmt->bind_param("d", $company_amount);
                if (!$updateCompanyStmt->execute()) {
                    throw new Exception("Failed to update company wallet: " . $updateCompanyStmt->error);
                }

                // Fetch updated company wallet balance (current amount)
                $companyWalletResult = mysqli_query($conn, $companyWalletQuery);
                $current_company_wallet = 0;

                if ($companyWalletResult && mysqli_num_rows($companyWalletResult) > 0) {
                    $companyWalletRow = mysqli_fetch_assoc($companyWalletResult);
                    $current_company_wallet = $companyWalletRow['wallet'];
                } else {
                    throw new Exception("Failed to fetch updated company wallet balance");
                }

                // Step 4: Insert transaction for the user (80%)
                $insertUserTransactionQuery = "INSERT INTO transactions (booking_id, rider_id, user_id, amount, previous_amount, current_amount, payment_status, service_type, booking_type, transaction_time)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $insertUserStmt = $conn->prepare($insertUserTransactionQuery);
                $current_rider_wallet = $previous_rider_wallet + $agent_amount;
                $insertUserStmt->bind_param("iiidddsss", $booking_id, $rider_id, $user_id, $agent_amount, $previous_rider_wallet, $current_rider_wallet, $payment_status, $service_type, $booking_type);

                if (!$insertUserStmt->execute()) {
                    throw new Exception("Failed to insert user transaction: " . $insertUserStmt->error);
                }

                // Step 5: Insert transaction for the company (20%)
                $insertCompanyTransactionQuery = "INSERT INTO company_transactions (booking_id, rider_id, user_id, amount, previous_amount, current_amount, payment_status, service_type, booking_type, com_pre_wallet, com_curr_wallet, agent_amount, company_amount, transaction_time)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
                $insertCompanyStmt = $conn->prepare($insertCompanyTransactionQuery);
                $insertCompanyStmt->bind_param("iiiddddsssddd", $booking_id, $rider_id, $user_id, $price, $previous_company_wallet, $current_company_wallet, $payment_status, $service_type, $booking_type, $previous_company_wallet, $current_company_wallet, $agent_amount, $company_amount);

                if (!$insertCompanyStmt->execute()) {
                    throw new Exception("Failed to insert company transaction: " . $insertCompanyStmt->error);
                }


                // Step 6: Insert a new review entry for this booking
                $insertReviewQuery = "INSERT INTO reviews (booking_id, user_id, review_status) VALUES (?, ?, 'pending')";
                $insertReviewStmt = $conn->prepare($insertReviewQuery);
                $insertReviewStmt->bind_param("ii", $booking_id, $user_id);

                if (!$insertReviewStmt->execute()) {
                    throw new Exception("Failed to insert review entry: " . $insertReviewStmt->error);
                }
                $insertCompanyStmt->execute(); // Insert company transaction



                // Commit all changes
                mysqli_commit($conn);
                echo json_encode(['status' => 'success', 'message' => 'Transactions logged successfully']);
            } else {
                throw new Exception("Booking not found");
            }
        } else {
            echo json_encode(['status' => 'success']); // Just return success for non-'done' status updates
        }
    } catch (Exception $e) {
        mysqli_rollback($conn);
        error_log($e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } finally {
        mysqli_close($conn);
    }
}
?>

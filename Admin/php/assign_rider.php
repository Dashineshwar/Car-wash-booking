<?php
include '../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : null;
    $rider_id = isset($_POST['rider_id']) ? intval($_POST['rider_id']) : null;

    if (!$booking_id || !$rider_id) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid booking or rider ID']);
        exit;
    }

    try {
        // Start a transaction
        mysqli_begin_transaction($conn);

        // Fetch the current rider assigned to the booking
        $currentRiderQuery = "SELECT rider_id FROM booking WHERE booking_id = '$booking_id'";
        $currentRiderResult = mysqli_query($conn, $currentRiderQuery);

        if ($currentRiderResult && mysqli_num_rows($currentRiderResult) > 0) {
            $currentRiderRow = mysqli_fetch_assoc($currentRiderResult);
            $current_rider_id = $currentRiderRow['rider_id'];

            // Remove the current rider's booking from booked_slots
            if ($current_rider_id) {
                $deleteSlotQuery = "DELETE FROM booked_slots WHERE booking_id = '$booking_id' AND rider_id = '$current_rider_id'";
                mysqli_query($conn, $deleteSlotQuery);
            }
        }

        // Update the booking table with the new rider
        $updateBookingQuery = "UPDATE booking SET rider_id = '$rider_id' WHERE booking_id = '$booking_id'";
        if (!mysqli_query($conn, $updateBookingQuery)) {
            throw new Exception("Failed to update booking with new rider: " . mysqli_error($conn));
        }

        // Add the new rider to booked_slots
        $timeSlotQuery = "SELECT booking_time FROM booking WHERE booking_id = '$booking_id'";
        $timeSlotResult = mysqli_query($conn, $timeSlotQuery);
        if ($timeSlotResult && mysqli_num_rows($timeSlotResult) > 0) {
            $timeSlotRow = mysqli_fetch_assoc($timeSlotResult);
            $time_slot = $timeSlotRow['booking_time'];

            $insertSlotQuery = "INSERT INTO booked_slots (booking_id, rider_id, slot_time) VALUES ('$booking_id', '$rider_id', '$time_slot')";
            if (!mysqli_query($conn, $insertSlotQuery)) {
                throw new Exception("Failed to add new rider to booked_slots: " . mysqli_error($conn));
            }
        }

        // Commit the transaction
        mysqli_commit($conn);

        echo json_encode(['status' => 'success', 'message' => 'Rider reassigned successfully']);
    } catch (Exception $e) {
        // Rollback on error
        mysqli_rollback($conn);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    // Close the connection
    mysqli_close($conn);
}

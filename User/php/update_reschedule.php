<?php
include '../../includes/session.php';
include '../../includes/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
    $new_date = isset($_POST['new_date']) ? trim($_POST['new_date']) : '';
    $new_time = isset($_POST['new_time']) ? trim($_POST['new_time']) : '';

    if ($booking_id === 0 || empty($new_date) || empty($new_time)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields.']);
        exit();
    }

    // Combine date and time
    $new_booking_time = $new_date . ' ' . $new_time;

    // Get the old slot details and assigned rider
    $oldSlotQuery = "SELECT booking_time, rider_id, postcode FROM booking WHERE booking_id = ?";
    if ($stmt = $conn->prepare($oldSlotQuery)) {
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $oldBooking = $result->fetch_assoc();
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: Unable to fetch old booking.']);
        exit();
    }

    $old_booking_time = $oldBooking['booking_time'];
    $rider_id = $oldBooking['rider_id'];
    $postcode = $oldBooking['postcode'];

    // Check if the new slot is available
    $checkQuery = "SELECT COUNT(*) AS count FROM booked_slots WHERE slot_date = ? AND slot_time = ? AND postcode = ?";
    if ($stmt = $conn->prepare($checkQuery)) {
        $stmt->bind_param("sss", $new_date, $new_time, $postcode);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        if ($row['count'] >= 5) { // Assuming max 5 riders per slot
            echo json_encode(['status' => 'error', 'message' => 'Selected time slot is already fully booked.']);
            exit();
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: Failed to check slot availability.']);
        exit();
    }

    // Begin Transaction
    $conn->begin_transaction();

    try {
        // Update the booking time
        $updateQuery = "UPDATE booking SET booking_time = ?, status = 'pending' WHERE booking_id = ?";
        $stmt = $conn->prepare($updateQuery);
        $stmt->bind_param("si", $new_booking_time, $booking_id);
        $stmt->execute();
        $stmt->close();

        // Remove old slot from `booked_slots`
        $deleteOldSlotQuery = "DELETE FROM booked_slots WHERE slot_date = ? AND slot_time = ? AND booking_id = ?";
        $stmt = $conn->prepare($deleteOldSlotQuery);
        $stmt->bind_param("ssi", explode(' ', $old_booking_time)[0], explode(' ', $old_booking_time)[1], $booking_id);
        $stmt->execute();
        $stmt->close();

        // Insert the new slot into `booked_slots`
        $insertNewSlotQuery = "INSERT INTO booked_slots (slot_date, slot_time, rider_id, booking_id, postcode) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertNewSlotQuery);
        $stmt->bind_param("ssisi", $new_date, $new_time, $rider_id, $booking_id, $postcode);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        if (isset($_POST['ajax'])) { 
            echo json_encode(['status' => 'success', 'message' => 'Booking rescheduled successfully.', 'redirect' => '../User/view_booking.php']);
        } else {
            $_SESSION['success_message'] = 'Booking rescheduled successfully.';
            header("Location: ../view_booking.php");
        }
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => 'Failed to reschedule booking.']);
        exit();
    }
}

$conn->close();
?>

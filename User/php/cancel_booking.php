<?php
include '../../includes/connection.php';

$booking_id = $_POST['booking_id'];

// Update booking status
$query = "UPDATE booking SET status = 'cancelled' WHERE booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);
$success = $stmt->execute();

// Remove related slots if booking is cancelled
if ($success) {
    $query = "DELETE FROM booked_slots WHERE booking_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>

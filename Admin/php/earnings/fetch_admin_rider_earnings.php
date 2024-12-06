<?php
session_start();
include '../../includes/connection.php';

// Sanitize and set filters
$rider_id = $_GET['rider_id'] ?? null;
$service_type = $_GET['service_type'] ?? null;
$booking_type = $_GET['booking_type'] ?? null;
$date_from = $_GET['date_from'] ?? null;
$date_to = $_GET['date_to'] ?? null;

// Default to current month if no date is provided
if (!$date_from && !$date_to) {
    $date_from = date('Y-m-01 00:00:00');
    $date_to = date('Y-m-t 23:59:59');
}

try {
    $date_from = (new DateTime($date_from))->format('Y-m-d H:i:s');
    $date_to = (new DateTime($date_to))->format('Y-m-d H:i:s');
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Invalid date format.']);
    exit;
}

// Build the query dynamically based on filters
$query = "SELECT t.transaction_id, t.booking_id, t.amount, t.transaction_time, t.service_type, t.booking_type, r.username 
          FROM transactions t 
          JOIN rider r ON t.rider_id = r.rider_id 
          WHERE t.transaction_time BETWEEN ? AND ?";
$params = [$date_from, $date_to];
$types = "ss";

if ($rider_id) {
    $query .= " AND t.rider_id = ?";
    $params[] = $rider_id;
    $types .= "i";
}

if ($service_type) {
    $query .= " AND t.service_type = ?";
    $params[] = $service_type;
    $types .= "s";
}

if ($booking_type) {
    $query .= " AND t.booking_type = ?";
    $params[] = $booking_type;
    $types .= "s";
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
$total_earnings = 0;

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    $total_earnings += $row['amount'];
}

$response = [
    'error' => false,
    'bookings' => $transactions,
    'total' => $total_earnings
];

header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conn->close();
?>

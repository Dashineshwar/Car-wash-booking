<?php
session_start();

// Check if rider is logged in
if (!isset($_SESSION['riderId'])) {
    echo json_encode(['error' => true, 'message' => 'Rider not logged in.']);
    exit;
}

$rider_id = $_SESSION['riderId'];

include '../../includes/connection.php';


// Sanitize and set date range
$date_from = $_GET['date_from'] ?? null;
$date_to = $_GET['date_to'] ?? null;

// Default to the current month if no dates are provided
if (!$date_from && !$date_to) {
    $date_from = date('Y-m-01 00:00:00'); // First day of the month
    $date_to = date('Y-m-t 23:59:59');    // Last day of the month
}

// Validate and format dates
try {
    $date_from = (new DateTime($date_from))->format('Y-m-d H:i:s');
    $date_to = (new DateTime($date_to))->format('Y-m-d H:i:s');
} catch (Exception $e) {
    echo json_encode(['error' => true, 'message' => 'Invalid date format.']);
    exit;
}

// Query to fetch earnings
$query = "SELECT transaction_id, booking_id, amount, transaction_time, service_type, booking_type 
          FROM transactions 
          WHERE rider_id = ? AND transaction_time BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['error' => true, 'message' => 'Database query failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param('iss', $rider_id, $date_from, $date_to);
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
    'total' => $total_earnings,
    'date_from' => $date_from,
    'date_to' => $date_to,
    'days' => (new DateTime($date_from))->diff(new DateTime($date_to))->days + 1,
];

header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conn->close();
?>

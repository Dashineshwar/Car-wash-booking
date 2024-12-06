<?php
session_start();
include '../../includes/connection.php';

// Sanitize and set date range
$date_from = $_GET['date_from'] ?? null;
$date_to = $_GET['date_to'] ?? null;

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

$query = "SELECT transaction_id, booking_id, amount, agent_amount, company_amount, transaction_time, service_type, booking_type 
          FROM company_transactions 
          WHERE transaction_time BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('ss', $date_from, $date_to);
$stmt->execute();
$result = $stmt->get_result();

$transactions = [];
$total_earnings = 0;

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    $total_earnings += $row['company_amount'];
}

$response = [
    'error' => false,
    'bookings' => $transactions,
    'total' => $total_earnings,
    'date_from' => $date_from,
    'date_to' => $date_to
];

header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conn->close();
?>

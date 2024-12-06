<?php
include '../../includes/connection.php';

// Sanitize input
$rider_id = $_GET['rider_id'] ?? null;
$transaction_id = $_GET['transaction_id'] ?? null;
$booking_id = $_GET['booking_id'] ?? null;
$from_date = $_GET['from_date'] ?? date('Y-m-d'); // Default to today's date
$to_date = $_GET['to_date'] ?? date('Y-m-d');     // Default to today's date

// Build the query dynamically
$query = "SELECT t.transaction_id, t.booking_id, t.amount, t.transaction_time, t.service_type, t.booking_type, r.username 
          FROM transactions t 
          JOIN rider r ON t.rider_id = r.rider_id 
          WHERE 1=1";
$params = [];
$types = "";

if (!empty($rider_id)) {
    $query .= " AND t.rider_id = ?";
    $params[] = $rider_id;
    $types .= "i";
}

if (!empty($transaction_id)) {
    $query .= " AND t.transaction_id = ?";
    $params[] = $transaction_id;
    $types .= "i";
}

if (!empty($booking_id)) {
    $query .= " AND t.booking_id = ?";
    $params[] = $booking_id;
    $types .= "i";
}

// Always apply the date range filter (default: today)
$query .= " AND t.transaction_time BETWEEN ? AND ?";
$params[] = $from_date . ' 00:00:00';
$params[] = $to_date . ' 23:59:59';
$types .= "ss";

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!$stmt) {
    echo json_encode(['error' => true, 'message' => 'Failed to prepare the query: ' . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Fetch transactions and calculate total earnings
$transactions = [];
$total_earnings = 0;

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    $total_earnings += $row['amount'];
}

$response = [
    'error' => false,
    'transactions' => $transactions,
    'total' => $total_earnings
];

header('Content-Type: application/json');
echo json_encode($response);

$stmt->close();
$conn->close();
?>

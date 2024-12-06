<?php
include '../../includes/connection.php';

// Sanitize input
$transaction_id = $_GET['transaction_id'] ?? null;
$booking_id = $_GET['booking_id'] ?? null;
$rider_id = $_GET['rider_id'] ?? null;
$user_id = $_GET['user_id'] ?? null;
$service_type = $_GET['service_type'] ?? null;
$booking_type = $_GET['booking_type'] ?? null;
$payment_status = $_GET['payment_status'] ?? null;
$from_date = $_GET['from_date'] ?? date('Y-m-d'); // Default to today's date
$to_date = $_GET['to_date'] ?? date('Y-m-d');     // Default to today's date

// Build the query dynamically
$query = "SELECT transaction_id, booking_id, rider_id, user_id, service_type, booking_type, amount, 
                 previous_amount, current_amount, agent_amount, company_amount, payment_status, transaction_time 
          FROM company_transactions 
          WHERE 1=1";
$params = [];
$types = "";

if (!empty($transaction_id)) {
    $query .= " AND transaction_id = ?";
    $params[] = $transaction_id;
    $types .= "i";
}

if (!empty($booking_id)) {
    $query .= " AND booking_id = ?";
    $params[] = $booking_id;
    $types .= "i";
}

if (!empty($rider_id)) {
    $query .= " AND rider_id = ?";
    $params[] = $rider_id;
    $types .= "i";
}

if (!empty($user_id)) {
    $query .= " AND user_id = ?";
    $params[] = $user_id;
    $types .= "i";
}

if (!empty($service_type)) {
    $query .= " AND service_type = ?";
    $params[] = $service_type;
    $types .= "s";
}

if (!empty($booking_type)) {
    $query .= " AND booking_type = ?";
    $params[] = $booking_type;
    $types .= "s";
}

if (!empty($payment_status)) {
    $query .= " AND payment_status = ?";
    $params[] = $payment_status;
    $types .= "s";
}

// Always apply the date range filter
$query .= " AND transaction_time BETWEEN ? AND ?";
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

// Fetch transactions and calculate total company earnings
$transactions = [];
$total_earnings = 0;

while ($row = $result->fetch_assoc()) {
    $transactions[] = $row;
    $total_earnings += $row['company_amount'];
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

<?php
// Include the Dompdf library
require_once '../../includes/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Database connection
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

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND transaction_time BETWEEN ? AND ?";
    $params[] = $from_date . ' 00:00:00';
    $params[] = $to_date . ' 23:59:59';
    $types .= "ss";
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Failed to prepare the database query: ' . $conn->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML for PDF
$html = "<h2 style='text-align: center;'>Company Transactions</h2>";
$html .= "<p style='text-align: center;'>Filtered results</p>";
$html .= '<table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Booking ID</th>
                    <th>Rider ID</th>
                    <th>User ID</th>
                    <th>Service Type</th>
                    <th>Booking Type</th>
                    <th>Amount (RM)</th>
                    <th>Previous Amount (RM)</th>
                    <th>Current Amount (RM)</th>
                    <th>Agent Amount (RM)</th>
                    <th>Company Amount (RM)</th>
                    <th>Payment Status</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>';

$total_earnings = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_earnings += $row['company_amount'];
        $html .= "<tr>
                    <td>{$row['transaction_id']}</td>
                    <td>{$row['booking_id']}</td>
                    <td>{$row['rider_id']}</td>
                    <td>{$row['user_id']}</td>
                    <td>{$row['service_type']}</td>
                    <td>{$row['booking_type']}</td>
                    <td>" . number_format($row['amount'], 2) . "</td>
                    <td>" . number_format($row['previous_amount'], 2) . "</td>
                    <td>" . number_format($row['current_amount'], 2) . "</td>
                    <td>" . number_format($row['agent_amount'], 2) . "</td>
                    <td>" . number_format($row['company_amount'], 2) . "</td>
                    <td>{$row['payment_status']}</td>
                    <td>{$row['transaction_time']}</td>
                  </tr>";
    }
} else {
    $html .= "<tr><td colspan='13' style='text-align: center;'>No transactions found for the selected filters.</td></tr>";
}
$html .= "</tbody></table>";
$html .= "<h3 style='text-align: right;'>Total Company Earnings: RM " . number_format($total_earnings, 2) . "</h3>";

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'landscape');
$dompdf->render();

// Generate the filename
$filename = "Company_Transactions_" . date('Ymd') . ".pdf";

// Stream the PDF as a downloadable file
header('Content-Type: application/pdf');
$dompdf->stream($filename, ["Attachment" => true]);

// Close the database connection
$stmt->close();
$conn->close();
?>

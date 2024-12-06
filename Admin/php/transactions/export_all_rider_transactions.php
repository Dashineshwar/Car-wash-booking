<?php
// Include the Dompdf library
require_once '../../includes/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Database connection
include '../../includes/connection.php';

// Sanitize input
$rider_id = $_GET['rider_id'] ?? null;
$transaction_id = $_GET['transaction_id'] ?? null;
$booking_id = $_GET['booking_id'] ?? null;
$from_date = $_GET['from_date'] ?? null;
$to_date = $_GET['to_date'] ?? null;

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

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND t.transaction_time BETWEEN ? AND ?";
    $params[] = $from_date . ' 00:00:00';
    $params[] = $to_date . ' 23:59:59';
    $types .= "ss";
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Failed to prepare the database query: ' . $conn->error);
}

if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Start generating the HTML for the PDF
$html = "<h2 style='text-align: center;'>All Rider Transactions</h2>";
$html .= "<p style='text-align: center;'>Filtered results</p>";
$html .= '<table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Rider</th>
                    <th>Booking ID</th>
                    <th>Service Type</th>
                    <th>Booking Type</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>';

$total_earnings = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total_earnings += $row['amount'];
        $html .= "<tr>
                    <td>{$row['transaction_id']}</td>
                    <td>{$row['username']}</td>
                    <td>{$row['booking_id']}</td>
                    <td>{$row['service_type']}</td>
                    <td>{$row['booking_type']}</td>
                    <td>" . number_format($row['amount'], 2) . "</td>
                    <td>{$row['transaction_time']}</td>
                  </tr>";
    }
} else {
    $html .= "<tr><td colspan='7' style='text-align: center;'>No transactions found for the selected filters.</td></tr>";
}
$html .= "</tbody></table>";
$html .= "<h3 style='text-align: right;'>Total Earnings: RM " . number_format($total_earnings, 2) . "</h3>";

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Generate the filename
$filename = "All_Rider_Transactions_" . date('Ymd') . ".pdf";

// Stream the PDF as a downloadable file
header('Content-Type: application/pdf');
$dompdf->stream($filename, ["Attachment" => true]);

// Close the database connection
$stmt->close();
$conn->close();
?>

<?php
// Include the Dompdf library
require_once '../../includes/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Database connection
include '../../includes/connection.php';

// Sanitize and validate input dates
$date_from = $_GET['date_from'] ?? date('Y-m-01'); // Default to the first day of the current month
$date_to = $_GET['date_to'] ?? date('Y-m-d');      // Default to today

try {
    $date_from = (new DateTime($date_from))->format('Y-m-d 00:00:00');
    $date_to = (new DateTime($date_to))->format('Y-m-d 23:59:59');
} catch (Exception $e) {
    die('Invalid date format provided.');
}

// Query to fetch company transactions for the given date range
$query = "SELECT transaction_id, booking_id, amount, agent_amount, company_amount, transaction_time, service_type, booking_type 
          FROM company_transactions 
          WHERE transaction_time BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Failed to prepare the database query.');
}

$stmt->bind_param('ss', $date_from, $date_to);
$stmt->execute();
$result = $stmt->get_result();

// Start generating the HTML for the PDF
$html = "<h2 style='text-align: center;'>Company Earnings Summary</h2>";
$html .= "<p style='text-align: center;'>Showing earnings from <b>$date_from</b> to <b>$date_to</b>.</p>";
$html .= '<table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Booking ID</th>
                    <th>Service Type</th>
                    <th>Booking Type</th>
                    <th>Agent Amount (RM)</th>
                    <th>Company Amount (RM)</th>
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
                    <td>{$row['service_type']}</td>
                    <td>{$row['booking_type']}</td>
                    <td>" . number_format($row['agent_amount'], 2) . "</td>
                    <td>" . number_format($row['company_amount'], 2) . "</td>
                    <td>{$row['transaction_time']}</td>
                  </tr>";
    }
} else {
    $html .= "<tr><td colspan='7' style='text-align: center;'>No transactions found for the selected date range.</td></tr>";
}
$html .= "</tbody></table>";
$html .= "<h3 style='text-align: right;'>Total Company Earnings: RM " . number_format($total_earnings, 2) . "</h3>";

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Stream the PDF as a downloadable file
$filename = "Company_Earnings_" . date('Ymd', strtotime($date_from)) . "_to_" . date('Ymd', strtotime($date_to)) . ".pdf";
$dompdf->stream($filename, ["Attachment" => true]);

// Close the database connection
$stmt->close();
$conn->close();
?>

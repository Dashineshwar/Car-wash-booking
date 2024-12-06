<?php
// Include the Dompdf library
require_once '../../includes/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
include '../../includes/connection.php';
include '../../includes/rider_session.php';

// Get the rider ID and date range
$rider_id = $_GET['riderId'] ?? null;
$date_from = $_GET['date_from'] ?? date('Y-m-01');
$date_to = $_GET['date_to'] ?? date('Y-m-d');

// Validate rider_id
if (!$rider_id) {
    die('Invalid rider ID.');
}

// Fetch the username for the rider
$user_query = "SELECT username FROM rider WHERE rider_id = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param('i', $rider_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
if ($user_result->num_rows === 0) {
    die('Rider not found.');
}
$user_data = $user_result->fetch_assoc();
$username = $user_data['username'];

// Fetch transactions for the given date range
$query = "SELECT transaction_id, booking_id, amount, transaction_time, service_type, booking_type 
          FROM transactions 
          WHERE rider_id = ? AND DATE(transaction_time) BETWEEN ? AND ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('iss', $rider_id, $date_from, $date_to);
$stmt->execute();
$result = $stmt->get_result();

if ($stmt->error) {
    die('Database query failed: ' . $stmt->error);
}

// Prepare the HTML for the PDF
$html = "<h2 style='text-align: center;'>Earnings Summary</h2>";
$html .= "<p style='text-align: center;'><b>Username:</b> $username | <b>ID:</b> $rider_id</p>";
$html .= "<p style='text-align: center;'>Showing earnings from <b>$date_from</b> to <b>$date_to</b>.</p>";
$html .= '<table border="1" style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Booking ID</th>
                    <th>Service Type</th>
                    <th>Booking Type</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                </tr>
            </thead><tbody>';

$total = 0;
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $total += $row['amount'];
        $html .= "<tr>
                    <td>{$row['transaction_id']}</td>
                    <td>{$row['booking_id']}</td>
                    <td>{$row['service_type']}</td>
                    <td>{$row['booking_type']}</td>
                    <td>" . number_format($row['amount'], 2) . "</td>
                    <td>{$row['transaction_time']}</td>
                  </tr>";
    }
} else {
    $html .= "<tr><td colspan='6'>No transactions found.</td></tr>";
}
$html .= "</tbody></table>";
$html .= "<h3 style='text-align: right;'>Total Earnings: RM " . number_format($total, 2) . "</h3>";

// Initialize Dompdf
$dompdf = new Dompdf();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Clear the output buffer and stream the PDF
ob_end_clean();

// Generate the dynamic filename
$filename = "{$username}_{$rider_id}_{$date_from}_{$date_to}.pdf";

header("Content-Type: application/pdf");
$dompdf->stream($filename, ["Attachment" => true]);

// Close the database connection
$stmt->close();
$conn->close();
?>

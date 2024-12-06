<?php
// Include the Dompdf library
require_once '../../includes/dompdf/autoload.inc.php';
use Dompdf\Dompdf;

// Database connection
include '../../includes/connection.php';

// Sanitize and validate input filters
$rider_id = $_GET['rider_id'] ?? null;
$service_type = $_GET['service_type'] ?? null;
$booking_type = $_GET['booking_type'] ?? null;
$date_from = $_GET['date_from'] ?? date('Y-m-01'); // Default to first day of the current month
$date_to = $_GET['date_to'] ?? date('Y-m-d');      // Default to today

try {
    $date_from = (new DateTime($date_from))->format('Y-m-d 00:00:00');
    $date_to = (new DateTime($date_to))->format('Y-m-d 23:59:59');
} catch (Exception $e) {
    die('Invalid date format provided.');
}

// Fetch rider name if rider_id is provided
$rider_name = "All Riders"; // Default to "All Riders" if no specific rider is selected
if (!empty($rider_id)) {
    $riderQuery = "SELECT username FROM rider WHERE rider_id = ?";
    $riderStmt = $conn->prepare($riderQuery);
    $riderStmt->bind_param("i", $rider_id);
    $riderStmt->execute();
    $riderResult = $riderStmt->get_result();

    if ($riderResult->num_rows > 0) {
        $riderData = $riderResult->fetch_assoc();
        $rider_name = $riderData['username'];
    }
    $riderStmt->close();
}

// Sanitize Rider Name for Filename
$sanitized_rider_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $rider_name);

// Build the query dynamically based on filters
$query = "SELECT t.transaction_id, t.booking_id, t.amount, t.transaction_time, t.service_type, t.booking_type, r.username 
          FROM transactions t 
          JOIN rider r ON t.rider_id = r.rider_id 
          WHERE t.transaction_time BETWEEN ? AND ?";
$params = [$date_from, $date_to];
$types = "ss";

if (!empty($rider_id)) {
    $query .= " AND t.rider_id = ?";
    $params[] = $rider_id;
    $types .= "i";
}

if (!empty($service_type)) {
    $query .= " AND t.service_type = ?";
    $params[] = $service_type;
    $types .= "s";
}

if (!empty($booking_type)) {
    $query .= " AND t.booking_type = ?";
    $params[] = $booking_type;
    $types .= "s";
}

// Prepare and execute the query
$stmt = $conn->prepare($query);
if (!$stmt) {
    die('Failed to prepare the database query: ' . $conn->error);
}
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Start generating the HTML for the PDF
$html = "<h2 style='text-align: center;'>Rider Earnings Summary</h2>";
$html .= "<p style='text-align: center;'><b>Rider:</b> $rider_name</p>";
$html .= "<p style='text-align: center;'>Filtered from <b>$date_from</b> to <b>$date_to</b>.</p>";
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
$filename = "Rider_Earnings_" . $sanitized_rider_name . "_" . $rider_id . "_" . date('Ymd', strtotime($date_from)) . "_to_" . date('Ymd', strtotime($date_to)) . ".pdf";

// Stream the PDF as a downloadable file
header('Content-Type: application/pdf');
$dompdf->stream($filename, ["Attachment" => true]);

// Close the database connection
$stmt->close();
$conn->close();
?>

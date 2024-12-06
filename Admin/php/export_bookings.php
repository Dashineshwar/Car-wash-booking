<?php
include '../includes/connection.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$rider_id = isset($_GET['rider_id']) ? mysqli_real_escape_string($conn, $_GET['rider_id']) : '';
$sort = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'booking_time';
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'today-tab'; // Default to today's bookings
$filename = isset($_GET['filename']) ? $_GET['filename'] : 'bookings_export.csv';
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($conn, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($conn, $_GET['date_to']) : '';

$allowed_sort_columns = ['booking_time', 'status', 'price'];
if (!in_array($sort, $allowed_sort_columns)) {
    $sort = 'booking_time'; // Default sorting column
}

// Base query
$where_clause = "WHERE 1=1";

// Apply tab-specific filters
$current_date = date('Y-m-d');
if ($tab === 'today-tab') {
    $where_clause .= " AND DATE(booking.booking_time) = '$current_date'";
} elseif ($tab === 'future-tab') {
    $where_clause .= " AND DATE(booking.booking_time) > '$current_date'";
} elseif ($tab === 'completed-tab') {
    $where_clause .= " AND booking.status = 'done'";
}

// Apply rider filter if specified
if ($rider_id) {
    $where_clause .= " AND booking.rider_id = '$rider_id'";
}

// Apply search filter if specified
if ($search) {
    $where_clause .= " AND (booking.service_type LIKE '%$search%' 
                            OR booking.booking_type LIKE '%$search%' 
                            OR users.phone_no LIKE '%$search%' 
                            OR CONCAT(booking.address_line_1, ' ', booking.city, ' ', booking.state, ' ', booking.country) LIKE '%$search%')";
}

// Apply date range filters if specified
if ($date_from && $date_to) {
    $where_clause .= " AND DATE(booking.booking_time) BETWEEN '$date_from' AND '$date_to'";
} elseif ($date_from) {
    $where_clause .= " AND DATE(booking.booking_time) >= '$date_from'";
} elseif ($date_to) {
    $where_clause .= " AND DATE(booking.booking_time) <= '$date_to'";
}

// Query to fetch filtered data
$query = "SELECT rider.username AS rider_name, booking.service_type, booking.booking_type, 
                 booking.price, booking.booking_time, booking.status,
                 CONCAT(booking.address_line_1, ', ', booking.city, ', ', booking.state, ', ', booking.country) AS address
          FROM booking
          LEFT JOIN users ON booking.user_id = users.id
          LEFT JOIN rider ON booking.rider_id = rider.rider_id
          $where_clause
          ORDER BY $sort";

$result = mysqli_query($conn, $query);

if (!$result) {
    die('Error fetching data: ' . mysqli_error($conn));
}

// Set headers for CSV export
header('Content-Type: text/csv');
header("Content-Disposition: attachment;filename=$filename");

$output = fopen('php://output', 'w');
fputcsv($output, ['Rider', 'Service Type', 'Booking Type', 'Price (RM)', 'Booking Time', 'Address', 'Status']);

while ($row = mysqli_fetch_assoc($result)) {
    $row['booking_time'] = date('d/m/Y, h:i:s A', strtotime($row['booking_time']));
    fputcsv($output, [
        $row['rider_name'],
        $row['service_type'],
        $row['booking_type'],
        $row['price'],
        $row['booking_time'],
        $row['address'],
        $row['status']
    ]);
}

fclose($output);
mysqli_close($conn);
?>

<?php
include '../includes/connection.php';
session_start();

$current_date = date('Y-m-d');
$rider_id = isset($_GET['rider_id']) ? mysqli_real_escape_string($conn, $_GET['rider_id']) : '';
$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$sort = isset($_GET['sort']) ? mysqli_real_escape_string($conn, $_GET['sort']) : 'booking_time';
$date_from = isset($_GET['date_from']) ? mysqli_real_escape_string($conn, $_GET['date_from']) : '';
$date_to = isset($_GET['date_to']) ? mysqli_real_escape_string($conn, $_GET['date_to']) : '';

$response = ["today" => [], "future" => [], "completed" => []];

$where_clause = "WHERE 1=1"; // Base where clause

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

// Validate sorting column to prevent SQL injection
$allowed_sort_columns = ['booking_time', 'status', 'price'];
if (!in_array($sort, $allowed_sort_columns)) {
    $sort = 'booking_time'; // Default sorting column
}

// Function to merge address components
function getMergedAddress($row) {
    $address_parts = array_filter([
        $row['address_line_1'],
        $row['address_line_2'],
        $row['postcode'],
        $row['city'],
        $row['state'],
        $row['country']
    ]);
    return implode(', ', $address_parts);
}

// Query for today's bookings
$today_query = "SELECT booking.booking_id, booking.user_id, booking.service_type, booking.booking_type, booking.price, 
                       booking.booking_time, booking.status, users.phone_no, rider.username AS rider_name,
                       booking.address_line_1, booking.address_line_2, booking.postcode, booking.city, booking.state, booking.country
                FROM booking 
                JOIN users ON booking.user_id = users.id 
                LEFT JOIN rider ON booking.rider_id = rider.rider_id 
                $where_clause AND booking.status = 'pending' AND DATE(booking.booking_time) = '$current_date'
                ORDER BY $sort";
$today_result = mysqli_query($conn, $today_query);

if (!$today_result) {
    die("Error in today's bookings query: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($today_result)) {
    $row['address'] = getMergedAddress($row); // Merge address components
    $response["today"][] = $row;
}

// Query for future bookings
$future_query = "SELECT booking.booking_id, booking.user_id, booking.service_type, booking.booking_type, booking.price, 
                        booking.booking_time, booking.status, users.phone_no, rider.username AS rider_name,
                        booking.address_line_1, booking.address_line_2, booking.postcode, booking.city, booking.state, booking.country
                 FROM booking 
                 JOIN users ON booking.user_id = users.id 
                 LEFT JOIN rider ON booking.rider_id = rider.rider_id 
                 $where_clause AND booking.status = 'pending' AND DATE(booking.booking_time) > '$current_date'
                 ORDER BY $sort";
$future_result = mysqli_query($conn, $future_query);

if (!$future_result) {
    die("Error in future bookings query: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($future_result)) {
    $row['address'] = getMergedAddress($row); // Merge address components
    $response["future"][] = $row;
}

// Query for completed bookings
$completed_query = "SELECT booking.booking_id, booking.user_id, booking.service_type, booking.booking_type, booking.price, 
                           booking.booking_time, booking.status, users.phone_no, rider.username AS rider_name,
                           booking.address_line_1, booking.address_line_2, booking.postcode, booking.city, booking.state, booking.country
                    FROM booking 
                    JOIN users ON booking.user_id = users.id 
                    LEFT JOIN rider ON booking.rider_id = rider.rider_id 
                    $where_clause AND booking.status = 'done'
                    ORDER BY $sort";
$completed_result = mysqli_query($conn, $completed_query);

if (!$completed_result) {
    die("Error in completed bookings query: " . mysqli_error($conn));
}

while ($row = mysqli_fetch_assoc($completed_result)) {
    $row['address'] = getMergedAddress($row); // Merge address components
    $response["completed"][] = $row;
}

// Send JSON response
echo json_encode($response);

// Close the database connection
mysqli_close($conn);
?>

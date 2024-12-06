<?php
include '../includes/connection.php';
session_start();

$rider_id = $_SESSION["riderId"];
$current_date = date('Y-m-d');

$response = [
    "today" => [],
    "future" => [],
    "completed" => []
];

// Query for today's bookings
$today_query = "SELECT booking.booking_id, booking.user_id, booking.service_type, booking.booking_type, booking.price, 
                       booking.booking_time, booking.address_line_1, booking.address_line_2, booking.postcode, 
                       booking.city, booking.state, booking.country, booking.status, users.phone_no 
                FROM booking 
                JOIN users ON booking.user_id = users.id 
                WHERE booking.rider_id = '$rider_id' AND booking.status = 'pending' AND DATE(booking.booking_time) = '$current_date'";
$today_result = mysqli_query($conn, $today_query);
while ($row = mysqli_fetch_assoc($today_result)) {
    $response["today"][] = $row;
}

// Query for future bookings
$future_query = "SELECT booking.booking_id, booking.user_id, booking.service_type, booking.booking_type, booking.price, 
                        booking.booking_time, booking.address_line_1, booking.address_line_2, booking.postcode, 
                        booking.city, booking.state, booking.country, booking.status, users.phone_no 
                 FROM booking 
                 JOIN users ON booking.user_id = users.id 
                 WHERE booking.rider_id = '$rider_id' AND booking.status = 'pending' AND DATE(booking.booking_time) > '$current_date'";
$future_result = mysqli_query($conn, $future_query);
while ($row = mysqli_fetch_assoc($future_result)) {
    $response["future"][] = $row;
}

// Query for completed bookings
$completed_query = "SELECT booking.booking_id, booking.user_id, booking.service_type, booking.booking_type, booking.price, 
                           booking.booking_time, booking.address_line_1, booking.address_line_2, booking.postcode, 
                           booking.city, booking.state, booking.country, booking.status, users.phone_no 
                    FROM booking 
                    JOIN users ON booking.user_id = users.id 
                    WHERE booking.rider_id = '$rider_id' AND booking.status = 'done'
                    ORDER BY booking.booking_time DESC";

$completed_result = mysqli_query($conn, $completed_query);
while ($row = mysqli_fetch_assoc($completed_result)) {
    $response["completed"][] = $row;
}

echo json_encode($response);
mysqli_close($conn);
?>

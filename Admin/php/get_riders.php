<?php
include '../includes/connection.php';

$booking_id = $_GET['booking_id'];
$time_slot = $_GET['time_slot'];
$postcode = $_GET['postcode'];

// Query to fetch all riders and their availability
$query = "
    SELECT rider_id, username, serving_postcode,
           CASE WHEN serving_postcode = '$postcode' THEN 'same' ELSE 'other' END AS location_status
    FROM rider
    WHERE available = 1
      AND rider_id NOT IN (
          SELECT rider_id FROM booked_slots WHERE slot_time = '$time_slot'
      )
";
$result = mysqli_query($conn, $query);

$riders = [];
while ($row = mysqli_fetch_assoc($result)) {
    $riders[] = $row;
}

echo json_encode($riders);

mysqli_close($conn);
?>

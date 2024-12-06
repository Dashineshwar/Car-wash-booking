<?php
include '../includes/connection.php';

// Get the date, postcode, and service duration from the AJAX request
$date = $_POST['date'];
$postcode = $_POST['postcode'];
$service_duration = $_POST['service_duration'];

// Fetch available riders in the user's postcode
$riders_query = "SELECT COUNT(rider_id) as available_riders FROM rider WHERE serving_postcode = '$postcode' AND available = 1";
$riders_result = mysqli_query($conn, $riders_query);
$available_riders = 0;

if ($riders_result && mysqli_num_rows($riders_result) > 0) {
    $rider_row = mysqli_fetch_assoc($riders_result);
    $available_riders = $rider_row['available_riders'];  // Count of available riders
}

// Fetch booked slots for the selected date
$booked_slots = [];
$slots_query = "SELECT slot_time, COUNT(rider_id) as booked_riders FROM booked_slots WHERE slot_date = '$date' GROUP BY slot_time";
$slots_result = mysqli_query($conn, $slots_query);

while ($slot_row = mysqli_fetch_assoc($slots_result)) {
    $booked_slots[$slot_row['slot_time']] = $slot_row['booked_riders'];  // Store number of booked riders per slot
}

// Send booked slots and available riders count back as JSON response
$response = [
    'booked_slots' => $booked_slots,
    'available_riders' => $available_riders
];

echo json_encode($response);
?>

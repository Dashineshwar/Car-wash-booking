<?php
include '../includes/connection.php';

$date = $_POST['date'];
$postcode = $_POST['postcode'];

// Fetch total available riders in the postcode
$riders_query = "SELECT COUNT(*) as available_riders FROM rider 
                 WHERE serving_postcode = '$postcode' AND available = 1";
$riders_result = mysqli_query($conn, $riders_query);
$riders_row = mysqli_fetch_assoc($riders_result);
$total_riders = $riders_row['available_riders'];

// Fetch booked slots for the selected date
$booked_slots = [];
$slots_query = "SELECT slot_time, COUNT(rider_id) as booked_riders 
                FROM booked_slots WHERE slot_date = '$date' 
                AND postcode = '$postcode' GROUP BY slot_time";
$slots_result = mysqli_query($conn, $slots_query);

while ($slot_row = mysqli_fetch_assoc($slots_result)) {
    $booked_slots[$slot_row['slot_time']] = $slot_row['booked_riders'];
}

// Generate available slots dynamically (8 AM - 8 PM in 15-minute intervals)
$all_time_slots = [];
for ($hour = 8; $hour < 20; $hour++) {
    for ($minute = 0; $minute < 60; $minute += 15) {
        $time_slot = sprintf("%02d:%02d:00", $hour, $minute);
        $all_time_slots[] = $time_slot;
    }
}

// Calculate available slots
$available_slots = [];
foreach ($all_time_slots as $slot) {
    if (!isset($booked_slots[$slot]) || $booked_slots[$slot] < $total_riders) {
        $available_slots[] = $slot;
    }
}

// Send available slots back as JSON response
$response = [
    'available_slots' => $available_slots
];

echo json_encode($response);
?>

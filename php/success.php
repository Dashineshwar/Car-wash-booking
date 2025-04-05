<?php
session_start();
include '../includes/connection.php';

// ✅ Step 1: Check if payment was successful
if (!isset($_GET['billplz']['id']) || !isset($_GET['billplz']['paid']) || $_GET['billplz']['paid'] !== "true") {
    echo "Payment failed or was not completed.";
    exit();
}

// ✅ Step 2: Retrieve Billplz Payment Details
$payment_id = $_GET['billplz']['id']; // Billplz Payment ID

// ✅ Step 3: Extract Booking Details from SESSION (since Billplz doesn't store metadata like Stripe)
$user_id = $_SESSION["id"];
$phone_no = $_SESSION["phone"];
$plate = $_SESSION["plate"];
$service_type = $_SESSION["service"];
$booking_type = $_SESSION["type"];
$price = $_SESSION["price"];
$description = $_SESSION["description"];
$date = $_SESSION["date"];
$time = $_SESSION["time"];
$address_line_1 = $_SESSION["address_line_1"];
$address_line_2 = $_SESSION["address_line_2"];
$postcode = $_SESSION["postcode"];
$city = $_SESSION["city"];
$state = $_SESSION["state"];
$country = $_SESSION["country"];

// ✅ Step 4: Ensure booking time format is correct
$booking_time = date('Y-m-d H:i:s', strtotime("$date $time"));

// ✅ Step 5: Find an Available Rider
$rider_query = "SELECT rider_id FROM rider WHERE serving_postcode = '$postcode' AND available = 1";
$rider_result = mysqli_query($conn, $rider_query);

$rider_id = null; // Default value if no riders are available

if (mysqli_num_rows($rider_result) > 0) {
    while ($rider_row = mysqli_fetch_assoc($rider_result)) {
        $potential_rider_id = $rider_row['rider_id'];

        // Check if rider is available for the selected time slot
        $slot_check_query = "SELECT * FROM booked_slots WHERE rider_id = '$potential_rider_id' AND slot_date = '$date' AND slot_time = '$time'";
        $slot_check_result = mysqli_query($conn, $slot_check_query);

        if (mysqli_num_rows($slot_check_result) == 0) {
            // Rider is available, assign them
            $rider_id = $potential_rider_id;
            break;
        }
    }
}

// ✅ Step 6: Store Booking Data in DB if Rider is Available
if ($rider_id) {
    // Insert booking details
    $booking_query = "INSERT INTO booking (user_id, service_type, booking_type, price, booking_time, address_line_1, address_line_2, postcode, city, state, country, phone_no, payment_status, rider_id, status)
                      VALUES ('$user_id', '$service_type', '$booking_type', '$price', '$booking_time', '$address_line_1', '$address_line_2', '$postcode', '$city', '$state', '$country', '$phone_no', 'paid', '$rider_id', 'pending')";
    mysqli_query($conn, $booking_query);

    // Get last inserted booking_id
    $booking_id = mysqli_insert_id($conn);

    // Insert the booked slot
    $booked_slot_query = "INSERT INTO booked_slots (rider_id, slot_time, slot_date, booking_id, postcode) 
                          VALUES ('$rider_id', '$time', '$date', '$booking_id', '$postcode')";
    mysqli_query($conn, $booked_slot_query);
} else {
    echo "No riders available for the selected time slot.";
    exit();
}

// ✅ Step 7: Redirect to Thank You Page
header("Location: thank_you.php");
exit();
?>

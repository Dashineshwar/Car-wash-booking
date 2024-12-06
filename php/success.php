<?php
include '../includes/session.php';
require '../vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_51PoGYVP0jYcAiYG4mQDz0U0hTRyrSpJk6lwlr9gEKSoAWV9fSBCpJSSKcdryu6G1XgKjwa99GfzfMWWs5YsdH6cu005nZLTegM');

// Retrieve the session ID from the URL
$session_id = $_GET['session_id'];

try {
    // Retrieve the session from Stripe
    $session = \Stripe\Checkout\Session::retrieve($session_id);

    // Assuming you have a database connection established
    include '../includes/connection.php';

    // Extract user details from the session
    $user_id = $_SESSION["id"];
    $phone_no = $_SESSION["phone"];

    // Extract the metadata details from the Stripe session
    $plate = $session->metadata->plate;
    $service_type = $session->metadata->service;
    $booking_type = $session->metadata->type;
    $price = $session->amount_total / 100; // Convert from cents to MYR
    $description = $session->metadata->description;
    $date = $session->metadata->date;
    $time = $session->metadata->time;

    // Extract the individual address components from the metadata
    $address_line_1 = $session->metadata->address_line_1;
    $address_line_2 = $session->metadata->address_line_2;
    $postcode = $session->metadata->postcode;
    $city = $session->metadata->city;
    $state = $session->metadata->state;
    $country = $session->metadata->country;

    // Combine the address components into one string
    $full_address = trim($address_line_1 . ', ' . $address_line_2 . ', ' . $postcode . ', ' . $city . ', ' . $state . ', ' . $country, ', ');

    $payment_status = 'paid';
    $status = 'pending'; // Initial status after booking

    // Combine date and time into one datetime string for the booking_time field
    $booking_time = date('Y-m-d H:i:s', strtotime("$date $time"));

    // Step 1: Find an available rider
    // Query to find all riders serving the same postcode
    $rider_query = "SELECT rider_id FROM rider WHERE serving_postcode = '$postcode' AND available = 1";
    $rider_result = mysqli_query($conn, $rider_query);

    $rider_id = null; // Initialize with null in case no rider is available

    if (mysqli_num_rows($rider_result) > 0) {
        while ($rider_row = mysqli_fetch_assoc($rider_result)) {
            $potential_rider_id = $rider_row['rider_id'];

            // Check if the rider is available for the selected time slot in booked_slots
            $slot_check_query = "SELECT * FROM booked_slots WHERE rider_id = '$potential_rider_id' AND slot_date = '$date' AND slot_time = '$time'";
            $slot_check_result = mysqli_query($conn, $slot_check_query);

            if (mysqli_num_rows($slot_check_result) == 0) {
                // Rider is available, assign them
                $rider_id = $potential_rider_id;
                break;
            }
        }
    }

    if ($rider_id) {
        // Step 2: Insert booking details into the booking table first
        $booking_query = "INSERT INTO booking (user_id, service_type, booking_type, price, booking_time, address_line_1, address_line_2, postcode, city, state, country, phone_no, payment_status, rider_id, status)
                          VALUES ('$user_id', '$service_type', '$booking_type', '$price', '$booking_time', '$address_line_1', '$address_line_2', '$postcode', '$city', '$state', '$country', '$phone_no', '$payment_status', '$rider_id', '$status')";
        mysqli_query($conn, $booking_query);

        // Get the last inserted booking_id
        $booking_id = mysqli_insert_id($conn);

        // Step 3: Insert the time slot into booked_slots using the newly generated booking_id
        $booked_slot_query = "INSERT INTO booked_slots (rider_id, slot_time, slot_date, booking_id, postcode) 
                              VALUES ('$rider_id', '$time', '$date', '$booking_id', '$postcode')";
        mysqli_query($conn, $booked_slot_query);
    } else {
        // Handle case when no riders are available
        echo "No riders available for the selected time slot.";
        exit();
    }

    // Redirect to a thank you or confirmation page
    header("Location: thank_you.php");
    exit();
} catch (Exception $e) {
    echo 'Payment failed: ' . $e->getMessage();
}
?>

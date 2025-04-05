<?php
// payment_callback.php
include '../includes/connection.php';

// Enable logging for debugging (optional but highly recommended)
$logFile = 'billplz_callback_log.txt';
file_put_contents($logFile, json_encode($_GET) . PHP_EOL, FILE_APPEND);

// Step 1: Validate incoming Billplz data
$bill_id = $_GET['billplz']['id'] ?? null;
$paid = $_GET['billplz']['paid'] ?? 'false'; // Can be "true" or "false"

// Step 2: Stop if bill_id is missing
if (!$bill_id) {
    http_response_code(400); // Bad request
    echo "Missing Billplz Bill ID.";
    exit();
}

// Step 3: Retrieve booking details from temporary table (if using one)
$sql = "SELECT * FROM booking_temp WHERE billplz_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $bill_id);
$stmt->execute();
$result = $stmt->get_result();
$booking_data = $result->fetch_assoc();
$stmt->close();

if (!$booking_data) {
    http_response_code(404); // Not found
    echo "No matching booking found for Bill ID.";
    exit();
}

// Step 4: If payment is successful, create the actual booking
if ($paid === 'true') {
    // Extract data from booking_temp
    $user_id = $booking_data['user_id'];
    $plate = $booking_data['plate'];
    $phone_no = $booking_data['phone'];
    $service_type = $booking_data['service'];
    $booking_type = $booking_data['type'];
    $price = $booking_data['price'];
    $description = $booking_data['description'];
    $date = $booking_data['date'];
    $time = $booking_data['time'];
    $address_line_1 = $booking_data['address_line_1'];
    $address_line_2 = $booking_data['address_line_2'];
    $postcode = $booking_data['postcode'];
    $city = $booking_data['city'];
    $state = $booking_data['state'];
    $country = $booking_data['country'];
    $booking_time = date('Y-m-d H:i:s', strtotime("$date $time"));

    // Find an available rider
    $rider_query = "SELECT rider_id FROM rider WHERE serving_postcode = '$postcode' AND available = 1";
    $rider_result = mysqli_query($conn, $rider_query);

    $rider_id = null;
    if (mysqli_num_rows($rider_result) > 0) {
        while ($rider_row = mysqli_fetch_assoc($rider_result)) {
            $potential_rider_id = $rider_row['rider_id'];
            $slot_check_query = "SELECT * FROM booked_slots WHERE rider_id = '$potential_rider_id' AND slot_date = '$date' AND slot_time = '$time'";
            $slot_check_result = mysqli_query($conn, $slot_check_query);
            if (mysqli_num_rows($slot_check_result) == 0) {
                $rider_id = $potential_rider_id;
                break;
            }
        }
    }

    // Step 5: Insert into booking table if rider is available
    if ($rider_id) {
        $insert_booking = "INSERT INTO booking (user_id, service_type, booking_type, price, booking_time, address_line_1, address_line_2, postcode, city, state, country, phone_no, payment_status, rider_id, status, plate, billplz_id)
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'paid', ?, 'pending', ?, ?)";
        $stmt = $conn->prepare($insert_booking);
        $stmt->bind_param("issdssssssssssiss", $user_id, $service_type, $booking_type, $price, $booking_time, $address_line_1, $address_line_2, $postcode, $city, $state, $country, $phone_no, $rider_id, $plate, $bill_id);
        $stmt->execute();
        $booking_id = $stmt->insert_id;
        $stmt->close();

        // Insert slot record
        $slot_query = "INSERT INTO booked_slots (rider_id, slot_time, slot_date, booking_id, postcode) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($slot_query);
        $stmt->bind_param("issis", $rider_id, $time, $date, $booking_id, $postcode);
        $stmt->execute();
        $stmt->close();
    }

    // Step 6: Clean up the temporary table
    $stmt = $conn->prepare("DELETE FROM booking_temp WHERE billplz_id = ?");
    $stmt->bind_param("s", $bill_id);
    $stmt->execute();
    $stmt->close();

    http_response_code(200); // Success
    echo "Booking confirmed and stored.";
} else {
    http_response_code(200);
    echo "Payment not completed.";
}
?>

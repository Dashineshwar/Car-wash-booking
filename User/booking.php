<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

// Retrieve service details from query parameters
$plate = isset($_GET['plate']) ? $_GET['plate'] : 'Unknown Plate';
$service_id = isset($_GET['service']) ? $_GET['service'] : 'Unknown Service';
$type = isset($_GET['type']) ? $_GET['type'] : 'Unknown Type';
$price = isset($_GET['price']) ? $_GET['price'] : 'Unknown Price';

// Fetch the user's address and postcode from the database
$user_id = $_SESSION['id'];
$query = "SELECT address_line_1, address_line_2, postcode, city, state, country FROM users WHERE id = '$user_id'";
$result = mysqli_query($conn, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $address_line_1 = $row['address_line_1'];
    $address_line_2 = $row['address_line_2'];
    $postcode = $row['postcode'];
    $city = $row['city'];
    $state = $row['state'];
    $country = $row['country'];
} else {
    $address_line_1 = $address_line_2 = $postcode = $city = $state = $country = 'Not available';
}

// Fetch service duration from the database
$service_query = "SELECT duration FROM services WHERE id = '$service_id'";
$service_result = mysqli_query($conn, $service_query);

if ($service_result && mysqli_num_rows($service_result) > 0) {
    $service_row = mysqli_fetch_assoc($service_result);
    $service_duration = $service_row['duration'];  // Duration in minutes
} else {
    $service_duration = 60;  // Default to 1 hour if not found
}

// Define working hours (8 AM to 8 PM)
$working_hours_start = 8;
$working_hours_end = 20;

// Fetch available riders in the user's postcode
$riders_query = "SELECT COUNT(rider_id) as available_riders FROM rider WHERE serving_postcode = '$postcode' AND available = 1";
$riders_result = mysqli_query($conn, $riders_query);
$available_riders = 0;

if ($riders_result && mysqli_num_rows($riders_result) > 0) {
    $rider_row = mysqli_fetch_assoc($riders_result);
    $available_riders = $rider_row['available_riders'];  // Count of available riders
}

// Fetch booked time slots for today initially (default day is today)
$booked_slots = [];
$slots_query = "SELECT slot_time, COUNT(rider_id) as booked_riders FROM booked_slots WHERE slot_date = CURDATE() GROUP BY slot_time";
$slots_result = mysqli_query($conn, $slots_query);

while ($slot_row = mysqli_fetch_assoc($slots_result)) {
    $booked_slots[$slot_row['slot_time']] = $slot_row['booked_riders'];  // Store number of booked riders per slot
}

// Generate all time slots from 8 AM to 8 PM in 15-minute intervals
$all_time_slots = [];
for ($hour = $working_hours_start; $hour < $working_hours_end; $hour++) {
    for ($minute = 0; $minute < 60; $minute += 15) {
        $time_slot = str_pad($hour, 2, "0", STR_PAD_LEFT) . ':' . str_pad($minute, 2, "0", STR_PAD_LEFT) . ':00';
        $all_time_slots[] = $time_slot;
    }
}

// Block time slots based on service duration and available riders
$available_slots = [];
foreach ($all_time_slots as $slot) {
    $is_fully_booked = false;

    // Check if the time slot is fully booked based on rider availability
    if (isset($booked_slots[$slot])) {
        // Check if all riders are booked for this time slot
        if ($booked_slots[$slot] >= $available_riders) {
            $is_fully_booked = true;
        }
    }

    // If the slot is not fully booked, it's available
    if (!$is_fully_booked) {
        $available_slots[] = $slot;

        // Now block the next time slots based on the service duration
        $slots_to_block = ceil($service_duration / 15);  // Number of 15-min slots to block
        for ($i = 1; $i < $slots_to_block; $i++) {
            $next_slot = date("H:i:s", strtotime("+$i * 15 minutes", strtotime($slot)));
            // Check if this next slot is not fully booked, if it isn't, block it
            if (!isset($booked_slots[$next_slot]) || $booked_slots[$next_slot] < $available_riders) {
                $available_slots[] = $next_slot;  // Block subsequent slots based on duration
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Book Later - Car Wash Service</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    #map {
        height: 300px;
        margin-bottom: 20px;
    }
    .card {
        max-width: 600px;
        margin: 0 auto;
    }
</style>
</head>
<body>

<div class="container">
    <h1 class="text-center mt-4 mb-4">Step 2 - Book your slot</h1>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Booking Details</h5>
            <p class="card-text"><strong>Car Number Plate:</strong> <?php echo htmlspecialchars($plate); ?></p>
            <p class="card-text"><strong>Service Chosen:</strong> <?php echo htmlspecialchars($service_id); ?></p>
            <p class="card-text"><strong>Type of Booking:</strong> <?php echo htmlspecialchars($type); ?></p>
            <p class="card-text"><strong>Total Price:</strong> RM<?php echo htmlspecialchars($price); ?></p>

            <!-- Editable Address Section -->
            <p class="card-text"><strong>Your Address:</strong> 
                <span id="user-address"><?php echo htmlspecialchars("$address_line_1, $address_line_2, $postcode, $city, $state, $country"); ?></span>
                <button id="edit-address-btn" class="btn btn-sm btn-primary ml-2">Edit</button>
            </p>
            <div id="address-edit" style="display: none;">
                <form id="address-form">
                    <div class="form-group">
                        <label for="address_line_1">Address Line 1:</label>
                        <input type="text" class="form-control" id="address_line_1" name="address_line_1" value="<?php echo htmlspecialchars($address_line_1); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address_line_2">Address Line 2:</label>
                        <input type="text" class="form-control" id="address_line_2" name="address_line_2" value="<?php echo htmlspecialchars($address_line_2); ?>">
                    </div>
                    <div class="form-group">
                        <label for="postcode">Postcode:</label>
                        <input type="text" class="form-control" id="postcode" name="postcode" value="<?php echo htmlspecialchars($postcode); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="city">City:</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($city); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="state">State:</label>
                        <input type="text" class="form-control" id="state" name="state" value="<?php echo htmlspecialchars($state); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="country">Country:</label>
                        <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($country); ?>" required>
                    </div>
                    <button type="button" id="save-address-btn" class="btn btn-success">Save</button>
                    <button type="button" id="reset-address-btn" class="btn btn-secondary">Reset</button>
                </form>
            </div>

            <h5 class="mt-4">Select Time Slot</h5>
            <form action="../php/stripe_charge.php" method="post" id="bookingForm">
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" class="form-control" id="date" name="date" required>
                </div>
                <div class="form-group">
                    <label for="time">Available Time Slots:</label>
                    <select class="form-control" id="time" name="time" required>
                        <option value="">Select a time slot</option>
                        <?php if (!empty($available_slots)): ?>
                            <?php foreach ($available_slots as $slot): ?>
                                <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="Add any additional details or instructions here..."></textarea>
                </div>
                
                <!-- Hidden fields for Stripe -->
                <input type="hidden" name="plate" value="<?php echo htmlspecialchars($plate); ?>">
                <input type="hidden" name="service" value="<?php echo htmlspecialchars($service_id); ?>">
                <input type="hidden" name="type" value="<?php echo htmlspecialchars($type); ?>">
                <input type="hidden" name="price" value="<?php echo htmlspecialchars($price); ?>">
                <input type="hidden" name="address_line_1" value="<?php echo htmlspecialchars($address_line_1); ?>">
                <input type="hidden" name="address_line_2" value="<?php echo htmlspecialchars($address_line_2); ?>">
                <input type="hidden" name="postcode" value="<?php echo htmlspecialchars($postcode); ?>">
                <input type="hidden" name="city" value="<?php echo htmlspecialchars($city); ?>">
                <input type="hidden" name="state" value="<?php echo htmlspecialchars($state); ?>">
                <input type="hidden" name="country" value="<?php echo htmlspecialchars($country); ?>">

                <button type="submit" class="btn btn-primary btn-block mb-3">Pay Now</button>
            </form>

            <a href="welcome.php" class="btn btn-outline-danger btn-block">Cancel Booking</a>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
$(document).ready(function() {
    const originalAddress = {
        line1: "<?php echo htmlspecialchars($address_line_1); ?>",
        line2: "<?php echo htmlspecialchars($address_line_2); ?>",
        postcode: "<?php echo htmlspecialchars($postcode); ?>",
        city: "<?php echo htmlspecialchars($city); ?>",
        state: "<?php echo htmlspecialchars($state); ?>",
        country: "<?php echo htmlspecialchars($country); ?>"
    };

    $('#edit-address-btn').click(function() {
        $('#user-address').hide();
        $('#edit-address-btn').hide();
        $('#address-edit').fadeIn();
    });

    $('#save-address-btn').click(function() {
        const updatedAddress = {
            line1: $('#address_line_1').val(),
            line2: $('#address_line_2').val(),
            postcode: $('#postcode').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            country: $('#country').val()
        };

        $('#user-address').text(
            `${updatedAddress.line1}, ${updatedAddress.line2}, ${updatedAddress.postcode}, ${updatedAddress.city}, ${updatedAddress.state}, ${updatedAddress.country}`
        );

        $('input[name="address_line_1"]').val(updatedAddress.line1);
        $('input[name="address_line_2"]').val(updatedAddress.line2);
        $('input[name="postcode"]').val(updatedAddress.postcode);
        $('input[name="city"]').val(updatedAddress.city);
        $('input[name="state"]').val(updatedAddress.state);
        $('input[name="country"]').val(updatedAddress.country);

        $('#address-edit').hide();
        $('#user-address').fadeIn();
        $('#edit-address-btn').fadeIn();
    });

    $('#reset-address-btn').click(function() {
        $('#address_line_1').val(originalAddress.line1);
        $('#address_line_2').val(originalAddress.line2);
        $('#postcode').val(originalAddress.postcode);
        $('#city').val(originalAddress.city);
        $('#state').val(originalAddress.state);
        $('#country').val(originalAddress.country);
    });

    $('#date').on('change', function() {
        const selectedDate = $(this).val();
        const postcode = '<?php echo $postcode; ?>';

        const today = new Date();
        const currentHour = today.getHours();
        const currentMinute = today.getMinutes();
        const currentDate = today.toISOString().split('T')[0];

        const timeDropdown = $('#time');
        timeDropdown.empty();
        timeDropdown.append('<option value="">Select a time slot</option>');

        $.ajax({
            url: '../php/fetch_slots.php',
            type: 'POST',
            data: {
                postcode: postcode,
                date: selectedDate,
                service_duration: '<?php echo $service_duration; ?>'
            },
            success: function(response) {
                const responseData = JSON.parse(response);
                const bookedSlots = responseData.booked_slots;
                const availableRiders = responseData.available_riders;

                const workingHoursStart = 8;
                const workingHoursEnd = 20;
                const allTimeSlots = [];

                for (let hour = workingHoursStart; hour < workingHoursEnd; hour++) {
                    for (let minute = 0; minute < 60; minute += 15) {
                        const timeSlot = ('0' + hour).slice(-2) + ':' + ('0' + minute).slice(-2) + ':00';
                        const slotDateTime = new Date(selectedDate + ' ' + timeSlot);

                        if (selectedDate === currentDate && (slotDateTime.getHours() < currentHour || (slotDateTime.getHours() === currentHour && slotDateTime.getMinutes() <= currentMinute))) {
                            continue;
                        }

                        allTimeSlots.push(timeSlot);
                    }
                }

                const availableSlots = allTimeSlots.filter(slot => !(bookedSlots.hasOwnProperty(slot) && bookedSlots[slot] >= availableRiders));

                if (availableSlots.length > 0) {
                    availableSlots.forEach(slot => {
                        timeDropdown.append('<option value="' + slot + '">' + slot + '</option>');
                    });
                } else {
                    timeDropdown.append('<option value="">No slots available</option>');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error: " + xhr.responseText);
                alert('Error fetching time slots. Please try again.');
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>

<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

// Retrieve service details from query parameters
$plate = isset($_GET['plate']) ? $_GET['plate'] : 'Unknown Plate';
$service_id = isset($_GET['service']) ? $_GET['service'] : 'Unknown Service';
$type = isset($_GET['type']) ? $_GET['type'] : 'Unknown Type';
$price = isset($_GET['price']) && is_numeric($_GET['price']) ? $_GET['price'] : 0;

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Step 2 - Book your slot</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
  <style>
body {
    background-color: #f5f7fa;
    font-family: 'Poppins', sans-serif;
    color: #1f2937;
}

.booking-card {
    background-color: #ffffff;
    border-radius: 20px;
    padding: 30px;
    margin: 40px auto;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
    max-width: 800px;
}

h1 {
    text-align: center;
    font-weight: 600;
    color: #1f2937;
    margin-top: 40px;
}

h5 {
    font-weight: 600;
    color: #1f2937;
}

.form-group label {
    font-weight: 500;
    color: #374151;
}

textarea.form-control,
input.form-control,
select.form-control {
    border-radius: 12px;
    border: 1px solid #d1d5db;
    padding: 10px;
    background-color: #f9fafb;
}

select.form-control:focus {
    border-color: #1f2937; /* dark border */
    box-shadow: none;      /* removes blue glow */
    outline: none;
}

.btn-primary {
    background-color: #1f2937;
    border-color: #1f2937;
    border-radius: 30px;
    padding: 10px 25px;
    font-weight: 500;
}

.btn-primary:hover {
    background-color: #111827;
    border-color: #111827;
}

.btn-success {
    background-color: #2563EB;
    border-color: #2563EB;
    border-radius: 30px;
    font-weight: 500;
}

.btn-success:hover {
    background-color: #1d4ed8;
    border-color: #1d4ed8;
}

.btn-secondary {
    background-color: #6b7280;
    border-color: #6b7280;
    border-radius: 30px;
    font-weight: 500;
}

.btn-secondary:hover {
    background-color: #4b5563;
    border-color: #4b5563;
}

.btn-outline-danger {
    border-radius: 30px;
    font-weight: 500;
    padding: 10px 25px;
    color: white;
    background-color: #ef4444; /* Red by default */
    border: 2px solid #ef4444;
    transition: all 0.3s ease;
}

.btn-outline-danger:hover {
    background-color: transparent;
    color: #ef4444;
    border: 2px solid #ef4444;
}


#edit-address-btn {
    padding: 6px 14px;
    font-size: 12px;
    margin-left: 10px;
    border-radius: 20px;
    background-color: #1f2937;
    color: #ffffff;
    border: none;
}

#edit-address-btn:hover {
    background-color: #111827;
}

#user-address {
    display: inline-block;
    margin-right: 10px;
    font-weight: 500;
    color: #374151;
}

#address-edit {
    margin-top: 20px;
    padding: 20px;
    background: #f3f4f6;
    border-radius: 12px;
}

  </style>
</head>
<body>


<h1 style="font-size: 25px;">Step 2 - Book your slot</h1>
<div class="booking-card">

    <div class="booking-section">
        <h5>Booking Details</h5> <br>
        <p><strong>Car Number Plate:</strong> <?php echo htmlspecialchars($plate); ?></p>
        <p><strong>Service Chosen:</strong> <?php echo htmlspecialchars($service_id); ?></p>
        <p><strong>Type of Booking:</strong> <?php echo htmlspecialchars($type); ?></p>
        <p><strong>Total Price:</strong> RM<?php echo htmlspecialchars($price); ?></p>

        <p><strong>Your Address:</strong>
            <span id="user-address"><?php echo htmlspecialchars("$address_line_1, $address_line_2, $postcode, $city, $state, $country"); ?></span>
            <button id="edit-address-btn" class="btn btn-sm btn-primary">Edit</button>
        </p>

        <div id="address-edit" style="display: none;">
            <form id="address-form">
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label>Address Line 1:</label>
                        <input type="text" class="form-control" id="address_line_1" value="<?php echo htmlspecialchars($address_line_1); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Address Line 2:</label>
                        <input type="text" class="form-control" id="address_line_2" value="<?php echo htmlspecialchars($address_line_2); ?>">
                    </div>
                    <div class="form-group col-md-6">
                        <label>Postcode:</label>
                        <input type="text" class="form-control" id="postcode" value="<?php echo htmlspecialchars($postcode); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>City:</label>
                        <input type="text" class="form-control" id="city" value="<?php echo htmlspecialchars($city); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>State:</label>
                        <input type="text" class="form-control" id="state" value="<?php echo htmlspecialchars($state); ?>" required>
                    </div>
                    <div class="form-group col-md-6">
                        <label>Country:</label>
                        <input type="text" class="form-control" id="country" value="<?php echo htmlspecialchars($country); ?>" required>
                    </div>
                </div>
                <button type="button" id="save-address-btn" class="btn btn-success">Save</button>
                <button type="button" id="reset-address-btn" class="btn btn-secondary">Reset</button>
            </form>
        </div>

        <h5>Select Time Slot</h5>
        <form action="../php/billplz_payment.php" method="post" id="bookingForm">
            <div class="form-group">
                <label for="date">Date:</label>
                <input type="date" class="form-control" id="date" name="date" required>
            </div>
            <div class="form-group">
                <label for="time">Available Time Slots:</label>
                <select class="form-control" id="time" name="time" required>
                    <option value="">Select a time slot</option>
                    <?php foreach ($available_slots as $slot): ?>
                        <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="description">Description:</label>
                <textarea class="form-control" name="description" placeholder="Add any additional details or instructions here..."></textarea>
            </div>

            <!-- Hidden Fields -->
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

            <button type="submit" class="btn btn-primary btn-block">Pay Now</button>
        </form>

        <a href="welcome.php" class="btn btn-outline-danger btn-block mt-2">Cancel Booking</a>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    const originalAddress = {
        line1: "<?php echo htmlspecialchars($address_line_1); ?>",
        line2: "<?php echo htmlspecialchars($address_line_2); ?>",
        postcode: "<?php echo htmlspecialchars($postcode); ?>",
        city: "<?php echo htmlspecialchars($city); ?>",
        state: "<?php echo htmlspecialchars($state); ?>",
        country: "<?php echo htmlspecialchars($country); ?>"
    };

    $('#edit-address-btn').click(() => {
        $('#user-address').hide();
        $('#edit-address-btn').hide();
        $('#address-edit').fadeIn();
    });

    $('#save-address-btn').click(() => {
        const updated = {
            line1: $('#address_line_1').val(),
            line2: $('#address_line_2').val(),
            postcode: $('#postcode').val(),
            city: $('#city').val(),
            state: $('#state').val(),
            country: $('#country').val()
        };

        $('#user-address').text(`${updated.line1}, ${updated.line2}, ${updated.postcode}, ${updated.city}, ${updated.state}, ${updated.country}`).fadeIn();
        $('input[name="address_line_1"]').val(updated.line1);
        $('input[name="address_line_2"]').val(updated.line2);
        $('input[name="postcode"]').val(updated.postcode);
        $('input[name="city"]').val(updated.city);
        $('input[name="state"]').val(updated.state);
        $('input[name="country"]').val(updated.country);
        $('#address-edit').hide();
        $('#edit-address-btn').fadeIn();
    });

    $('#reset-address-btn').click(() => {
        $('#address_line_1').val(originalAddress.line1);
        $('#address_line_2').val(originalAddress.line2);
        $('#postcode').val(originalAddress.postcode);
        $('#city').val(originalAddress.city);
        $('#state').val(originalAddress.state);
        $('#country').val(originalAddress.country);
    });

    $('#date').on('change', function () {
        const selectedDate = $(this).val();
        const postcode = "<?php echo $postcode; ?>";
        const service_duration = "<?php echo $service_duration; ?>";
        const timeDropdown = $('#time');
        const today = new Date();
        const currentHour = today.getHours();
        const currentMinute = today.getMinutes();
        const currentDate = today.toISOString().split('T')[0];

        timeDropdown.empty().append('<option value="">Select a time slot</option>');

        $.post('../php/fetch_slots.php', { date: selectedDate, postcode: postcode, service_duration: service_duration }, function (response) {
            const data = JSON.parse(response);
            const bookedSlots = data.booked_slots;
            const availableRiders = data.available_riders;
            const workingHoursStart = 8;
            const workingHoursEnd = 20;

            for (let h = workingHoursStart; h < workingHoursEnd; h++) {
                for (let m = 0; m < 60; m += 15) {
                    const slot = ('0' + h).slice(-2) + ':' + ('0' + m).slice(-2) + ':00';
                    const slotTime = new Date(`${selectedDate} ${slot}`);

                    if (selectedDate === currentDate && (slotTime.getHours() < currentHour || (slotTime.getHours() === currentHour && slotTime.getMinutes() <= currentMinute))) {
                        continue;
                    }

                    if (!bookedSlots.hasOwnProperty(slot) || bookedSlots[slot] < availableRiders) {
                        timeDropdown.append(`<option value="${slot}">${slot}</option>`);
                    }
                }
            }

            if (timeDropdown.children('option').length <= 1) {
                timeDropdown.append('<option value="">No available slots</option>');
            }
        });
    });
});
</script>

<?php include '../includes/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

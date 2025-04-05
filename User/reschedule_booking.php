<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

if (!isset($_GET['booking_id'])) {
    die("Invalid request. Booking ID missing.");
}

$booking_id = $_GET['booking_id'];

// Fetch booking details
$query = "SELECT b.service_type, b.booking_time, b.postcode, b.price, b.user_id, b.rider_id, u.address_line_1, u.address_line_2, u.city, u.state, u.country
          FROM booking b
          JOIN users u ON b.user_id = u.id
          WHERE b.booking_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Booking not found.");
}

$booking = $result->fetch_assoc();

// Fetch booked slots for the given postcode
$booked_slots = [];
$slots_query = "SELECT slot_time, COUNT(rider_id) as booked_riders FROM booked_slots WHERE slot_date = CURDATE() AND postcode = ? GROUP BY slot_time";
$slots_stmt = $conn->prepare($slots_query);
$slots_stmt->bind_param("s", $booking['postcode']);
$slots_stmt->execute();
$slots_result = $slots_stmt->get_result();

while ($slot_row = $slots_result->fetch_assoc()) {
    $booked_slots[$slot_row['slot_time']] = $slot_row['booked_riders'];
}

// Generate available slots dynamically (8 AM - 8 PM in 15-min intervals)
$all_time_slots = [];
for ($hour = 8; $hour < 20; $hour++) {
    for ($minute = 0; $minute < 60; $minute += 15) {
        $time_slot = sprintf("%02d:%02d:00", $hour, $minute);
        $all_time_slots[] = $time_slot;
    }
}

$available_slots = [];
foreach ($all_time_slots as $slot) {
    if (!isset($booked_slots[$slot]) || $booked_slots[$slot] < 5) { // Assuming max 5 riders per slot
        $available_slots[] = $slot;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reschedule Booking</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

<div class="container">
    <h2 class="text-center mt-4">Reschedule Booking</h2>

    <div class="card">
        <div class="card-body">
            <h5 class="card-title">Booking Details</h5>
            <p><strong>Service Type:</strong> <?php echo htmlspecialchars($booking['service_type']); ?></p>
            <p><strong>Current Booking Time:</strong> <?php echo date('d M Y, h:i A', strtotime($booking['booking_time'])); ?></p>
            <input type="hidden" id="current_date" value="<?php echo date('Y-m-d', strtotime($booking['booking_time'])); ?>">
            <input type="hidden" id="current_time" value="<?php echo date('H:i:s', strtotime($booking['booking_time'])); ?>">

            <p><strong>Address:</strong> <?php echo htmlspecialchars("{$booking['address_line_1']}, {$booking['address_line_2']}, {$booking['city']}, {$booking['state']}, {$booking['country']}"); ?></p>
            
            <form action="php/update_reschedule.php" method="POST">
                <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">

                <div class="form-group">
                    <label for="new_date">Select New Date:</label>
                    <input type="date" class="form-control" id="new_date" name="new_date" required>
                </div>

                <div class="form-group">
                    <label for="new_time">Select New Time Slot:</label>
                    <select class="form-control" id="new_time" name="new_time" required>
                        <option value="">Choose a time slot</option>
                        <?php foreach ($all_time_slots as $slot): ?>
                            <?php if (!isset($booked_slots[$slot]) || $booked_slots[$slot] < 5): // Only show available slots ?>
                                <option value="<?php echo $slot; ?>"><?php echo $slot; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                </div>



                <button type="submit" class="btn btn-success btn-block">Confirm Reschedule</button>
                <a href="view_booking.php" class="btn btn-danger btn-block">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$('#new_date').on('change', function () {
    const rawDate = $(this).val(); // format might be dd/mm/yyyy or yyyy-mm-dd
    const selectedDate = new Date(rawDate);
    
    const yyyy = selectedDate.getFullYear();
    const mm = String(selectedDate.getMonth() + 1).padStart(2, '0');
    const dd = String(selectedDate.getDate()).padStart(2, '0');
    const formattedDate = `${yyyy}-${mm}-${dd}`; // '2025-03-07'

    const postcode = "<?php echo $booking['postcode']; ?>";
    const currentTime = $('#current_time').val();
    const currentDate = $('#current_date').val();

    $.ajax({
        url: '../php/fetch_reschedule_slots.php',
        type: 'POST',
        data: {
            date: formattedDate,
            postcode: postcode,
            current_time: currentTime,
            current_date: currentDate
        },
        success: function (response) {
            const responseData = JSON.parse(response);
            const timeDropdown = $('#new_time');
            timeDropdown.empty();

            if (responseData.available_slots.length > 0) {
                timeDropdown.append('<option value="">Choose a time slot</option>');
                responseData.available_slots.forEach(slot => {
                    timeDropdown.append(`<option value="${slot}">${slot}</option>`);
                });
            } else {
                timeDropdown.append('<option value="">No slots available</option>');
            }
        },
        error: function (xhr) {
            console.error("Error fetching time slots: " + xhr.responseText);
        }
    });
});

</script>

</body>
</html>

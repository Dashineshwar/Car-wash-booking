<?php
include 'includes/rider_session.php';  // Include session file to check if the rider is logged in
include 'includes/connection.php';      // Include the database connection
include 'includes/topbar.php';          // Optional topbar

$rider_id = $_SESSION["riderId"];  // Assuming rider_id is stored in the session after login
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Rider Dashboard</title>
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
<style>
    html, body {
        height: 100%;
        margin: 0;
        display: flex;
        flex-direction: column;
    }
    #content {
        flex: 1 0 auto;
    }
    .table-container {
        margin: 20px auto;
        max-width: 1200px;
    }
    .status-dropdown {
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    .btn-update-status {
        margin-top: 10px;
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-update-status:hover {
        background-color: #0056b3;
    }
    .contact-icons {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }
    .contact-icons i {
        font-size: 24px;
        cursor: pointer;
        transition: color 0.3s ease;
    }
    .contact-icons i:hover {
        color: #28a745;
    }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
</head>
<body>

<div id="content">
    <div class="container table-container">
        <h1 class="text-center mb-4">Your Bookings</h1>

        <ul class="nav nav-tabs" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="today-tab" data-toggle="tab" href="#today" role="tab" aria-controls="today" aria-selected="true">Today's Bookings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="future-tab" data-toggle="tab" href="#future" role="tab" aria-controls="future" aria-selected="false">Future Bookings</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="completed-tab" data-toggle="tab" href="#completed" role="tab" aria-controls="completed" aria-selected="false">Completed Bookings</a>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Today's Bookings Tab -->
            <div class="tab-pane fade show active" id="today" role="tabpanel" aria-labelledby="today-tab">
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Service Type</th>
                            <th>Booking Type</th>
                            <th>Price (RM)</th>
                            <th>Booking Time</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Future Bookings Tab -->
            <div class="tab-pane fade" id="future" role="tabpanel" aria-labelledby="future-tab">
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Service Type</th>
                            <th>Booking Type</th>
                            <th>Price (RM)</th>
                            <th>Booking Time</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Update Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Completed Bookings Tab -->
            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Service Type</th>
                            <th>Booking Type</th>
                            <th>Price (RM)</th>
                            <th>Booking Time</th>
                            <th>Address</th>
                            <th>Contact</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    function fetchBookingsData() {
        $.ajax({
            url: 'php/fetch_rider_bookings.php',
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                updateTable("#today", data.today);
                updateTable("#future", data.future);
                updateTable("#completed", data.completed);
            },
            error: function() {
                console.log('Error fetching data');
            }
        });
    }

    function updateTable(tableId, bookings) {
        let tableBody = $(tableId + ' tbody');
        tableBody.empty();

        if (bookings.length > 0) {
            bookings.forEach(function(booking) {
                let row = `
                    <tr>
                        <td>${booking.service_type}</td>
                        <td>${booking.booking_type}</td>
                        <td>${booking.price}</td>
                        <td>${new Date(booking.booking_time).toLocaleString()}</td>
                        <td>${booking.address_line_1}, ${booking.address_line_2}, ${booking.postcode}, ${booking.city}, ${booking.state}, ${booking.country}</td>
                        <td class="contact-icons">
                            <a href="tel:+60${booking.phone_no}" title="Call"><i class="fas fa-phone-alt"></i></a>
                            <a href="https://api.whatsapp.com/send?phone=60${booking.phone_no}" title="WhatsApp" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        </td>
                        <td><span>${booking.status}</span></td>
                        <td>
                            <select class="status-dropdown" data-booking-id="${booking.booking_id}">
                                <option value="pending" ${booking.status === 'pending' ? 'selected' : ''}>Pending</option>
                                <option value="done" ${booking.status === 'done' ? 'selected' : ''}>Done</option>
                                <option value="cancelled" ${booking.status === 'cancelled' ? 'selected' : ''}>Cancelled</option>
                            </select>
                            <button class="btn-update-status" data-booking-id="${booking.booking_id}">Update</button>
                        </td>
                    </tr>
                `;
                tableBody.append(row);
            });
        } else {
            tableBody.append('<tr><td colspan="8" class="text-center">No bookings found</td></tr>');
        }
    }

    fetchBookingsData();
    setInterval(fetchBookingsData, 10000);

    $(document).on('click', '.btn-update-status', function() {
        let booking_id = $(this).data('booking-id');
        let new_status = $(this).siblings('.status-dropdown').val();

        $.ajax({
            url: 'php/update_booking_status.php',
            type: 'POST',
            data: { booking_id: booking_id, status: new_status },
            success: function(response) {
                let data = JSON.parse(response);
                if (data.status === 'success') {
                    fetchBookingsData();
                } else {
                    alert('Error: ' + data.message);
                }
            },
            error: function() {
                alert('Error updating status. Please try again.');
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
mysqli_close($conn);
?>

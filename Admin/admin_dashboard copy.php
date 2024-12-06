<?php
include 'includes/rider_session.php';
include 'includes/connection.php';
include 'includes/topbar.php';

$admin_id = $_SESSION["id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard</title>
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
    .status-dropdown, .assign-rider-dropdown {
        padding: 5px;
        border-radius: 5px;
        border: 1px solid #ccc;
        font-size: 14px;
    }
    .btn-update-status, .btn-assign-rider {
        margin-top: 10px;
        padding: 5px 10px;
        background-color: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
    }
    .btn-update-status:hover, .btn-assign-rider:hover {
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
        <h1 class="text-center mb-4">All Bookings</h1>

        <div class="d-flex justify-content-between mb-4">
            <select id="riderFilter" class="form-control w-25">
                <option value="">All Riders</option>
            </select>
            <button id="exportBtn" class="btn btn-primary">Export CSV</button>
        </div>

        <div class="d-flex justify-content-between mb-4">
            <input type="text" id="searchInput" placeholder="Search bookings..." class="form-control w-50">
            <select id="sortOption" class="form-control w-25">
                <option value="booking_time">Sort by Date</option>
                <option value="status">Sort by Status</option>
                <option value="price">Sort by Price</option>
            </select>
        </div>

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
            <div class="tab-pane fade show active" id="today" role="tabpanel" aria-labelledby="today-tab">
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Rider</th>
                            <th>Service Type</th>
                            <th>Booking Type</th>
                            <th>Price (RM)</th>
                            <th>Booking Time</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Assign Rider / Update Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="future" role="tabpanel" aria-labelledby="future-tab">
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Rider</th>
                            <th>Service Type</th>
                            <th>Booking Type</th>
                            <th>Price (RM)</th>
                            <th>Booking Time</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="completed" role="tabpanel" aria-labelledby="completed-tab">
                <table class="table table-bordered table-striped mt-4">
                    <thead>
                        <tr>
                            <th>Rider</th>
                            <th>Service Type</th>
                            <th>Booking Type</th>
                            <th>Price (RM)</th>
                            <th>Booking Time</th>
                            <th>Address</th>
                            <th>Contact</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    fetchBookingsData();

    // Function to fetch bookings data
    function fetchBookingsData() {
        const searchQuery = $('#searchInput').val();
        const riderFilter = $('#riderFilter').val();
        const sortOption = $('#sortOption').val();

        $.ajax({
            url: 'php/fetch_all_bookings.php',
            type: 'GET',
            data: { search: searchQuery, rider_id: riderFilter, sort: sortOption },
            dataType: 'json',
            success: function (data) {
                updateTable("#today", data.today, true);
                updateTable("#future", data.future, false);
                updateTable("#completed", data.completed, false);
            },
            error: function () {
                console.log('Error fetching data');
            }
        });
    }

    // Function to fetch available riders for dropdown
    function fetchAvailableRiders(booking_id, postcode, time_slot, dropdown) {
        $.ajax({
            url: 'php/get_riders.php',
            type: 'GET',
            data: { booking_id: booking_id, postcode: postcode, time_slot: time_slot },
            success: function (response) {
                const riders = JSON.parse(response);
                dropdown.empty().append('<option value="">Select Rider</option>');
                riders.forEach(rider => {
                    const warning = rider.location_status === 'other' ? ' (Other Postcode)' : '';
                    dropdown.append(`<option value="${rider.rider_id}">${rider.username}${warning}</option>`);
                });
            },
            error: function () {
                console.log('Error fetching riders');
            }
        });
    }

    // Function to update tables with fetched data
    function updateTable(tableId, bookings, allowUpdate) {
        const tableBody = $(tableId + ' tbody');
        tableBody.empty();

        bookings.forEach(booking => {
            let row = `<tr>
                        <td>${booking.rider_name || 'Unassigned'}</td>
                        <td>${booking.service_type}</td>
                        <td>${booking.booking_type}</td>
                        <td>${booking.price}</td>
                        <td>${new Date(booking.booking_time).toLocaleString()}</td>
                        <td>${booking.address}</td>
                        <td class="contact-icons">
                            <a href="tel:+60${booking.phone_no}"><i class="fas fa-phone-alt"></i></a>
                            <a href="https://api.whatsapp.com/send?phone=60${booking.phone_no}" target="_blank"><i class="fab fa-whatsapp"></i></a>
                        </td>
                        <td>${booking.status}</td>`;

            if (allowUpdate) {
                row += `<td>
                    <select class="assign-rider-dropdown" data-booking-id="${booking.booking_id}" data-postcode="${booking.postcode}" data-slot="${booking.booking_time}">
                        <option value="">Select Rider</option>
                    </select>
                    <button class="btn-assign-rider" data-booking-id="${booking.booking_id}">Assign</button>
                </td>`;
            } else {
                row += `<td>-</td>`;
            }

            row += '</tr>';
            tableBody.append(row);
        });
    }

    // Event listener for search input
    $('#searchInput').on('input', function () {
        fetchBookingsData();
    });

    // Event listener for rider filter
    $('#riderFilter').on('change', function () {
        fetchBookingsData();
    });

    // Event listener for sort option
    $('#sortOption').on('change', function () {
        fetchBookingsData();
    });

    // Export CSV button
    $('#exportBtn').on('click', function () {
        const searchQuery = $('#searchInput').val();
        const riderFilter = $('#riderFilter').val();
        const sortOption = $('#sortOption').val();

        const url = `php/export_bookings.php?search=${searchQuery}&rider_id=${riderFilter}&sort=${sortOption}`;
        window.open(url, '_blank');
    });

    // Event listener for assign rider button
    $(document).on('click', '.btn-assign-rider', function () {
        const booking_id = $(this).data('booking-id');
        const dropdown = $(this).siblings('.assign-rider-dropdown');
        const rider_id = dropdown.val();
        const rider_text = dropdown.find('option:selected').text();

        if (!rider_id) {
            alert('Please select a rider before assigning.');
            return;
        }

        if (rider_text.includes('(Other Postcode)')) {
            if (!confirm('The selected rider is from a different postcode. Do you wish to proceed?')) {
                return;
            }
        }

        $.ajax({
            url: 'php/assign_rider.php',
            type: 'POST',
            data: { booking_id: booking_id, rider_id: rider_id },
            success: function (response) {
                const data = JSON.parse(response);
                if (data.status === 'success') {
                    alert('Rider assigned successfully.');
                    fetchBookingsData();
                } else {
                    alert(data.message);
                }
            },
            error: function () {
                alert('Error assigning rider. Please try again.');
            }
        });
    });

    // Populate rider dropdown when dropdown is focused
    $(document).on('focus', '.assign-rider-dropdown', function () {
        const booking_id = $(this).data('booking-id');
        const postcode = $(this).data('postcode');
        const time_slot = $(this).data('slot');
        fetchAvailableRiders(booking_id, postcode, time_slot, $(this));
    });

    // Auto-refresh bookings every 5 seconds
    setInterval(fetchBookingsData, 5000);
});

</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>

<?php
mysqli_close($conn);
?>

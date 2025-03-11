<?php
include '../includes/session.php';
include '../includes/connection.php';
include '../includes/topbar.php';

// Fetch the user's ID from the session
$user_id = $_SESSION['id'];
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Bookings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"> 
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <style>
        body {
            background-color: #f8f9fa;
        }
        .container {
            margin-top: 30px;
        }
    </style>
</head>
<body>
<div class="container">
    <h2 class="text-center mb-4">Your Bookings</h2>

    <!-- Date Filter -->
    <div class="mb-3 d-flex justify-content-center">
        <div class="form-group mr-2">
            <label for="date-from">From:</label>
            <input type="date" id="date-from" class="form-control">
        </div>
        <div class="form-group mr-2">
            <label for="date-to">To:</label>
            <input type="date" id="date-to" class="form-control">
        </div>
        <div class="form-group align-self-end mr-2">
            <button id="filter-btn" class="btn btn-primary">Filter</button>
        </div>
        <div class="form-group align-self-end">
            <button id="reset-btn" class="btn btn-secondary">Reset Filter</button>
        </div>
    </div>

    <!-- Bookings Table -->
    <table id="bookings-table" class="table table-bordered">
        <thead>
        <tr>
            <th>Service Type</th>
            <th>Booking Type</th>
            <th>Price</th>
            <th>Booking Time</th>
            <th>Address</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
</div>

<script>
    $(document).ready(function () {
        const userId = <?php echo $user_id; ?>;

        // Function to populate the table with bookings
        function populateTable(bookings) {
            const tbody = $('#bookings-table tbody');
            tbody.empty();

            if (bookings.length > 0) {
                bookings.forEach(booking => {
                    const row = `
                        <tr>
                            <td>${booking.service_type}</td>
                            <td>${booking.booking_type}</td>
                            <td>RM ${booking.price}</td>
                            <td>${new Date(booking.booking_time).toLocaleString()}</td>
                            <td>${booking.address}</td>
                            <td>${booking.status}</td>
                            <td>
                                ${booking.status === 'pending' ? `
                                    <a href="reschedule_booking.php?booking_id=${booking.booking_id}&current_date=${booking.booking_time}" 
                                        class="btn btn-warning btn-sm">Reschedule</a>
                                ` : ''}
                            </td>
                        </tr>
                    `;
                    tbody.append(row);
                });
            } else {
                tbody.html('<tr><td colspan="7" class="text-center">No bookings found.</td></tr>');
            }
        }

        // Fetch and display bookings based on filters
        function fetchBookings(dateFrom = '', dateTo = '') {
            $.ajax({
                url: 'php/fetch_booking.php',
                type: 'GET',
                data: {
                    user_id: userId,
                    date_from: dateFrom,
                    date_to: dateTo
                },
                dataType: 'json',
                success: function (response) {
                    if (response.bookings) {
                        populateTable(response.bookings);
                    } else {
                        $('#bookings-table tbody').html('<tr><td colspan="7" class="text-center">No bookings found.</td></tr>');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error fetching bookings:', error);
                    alert('Failed to fetch bookings. Please try again.');
                }
            });
        }

        // Fetch today's and future bookings by default
        function fetchTodayAndFutureBookings() {
            const today = new Date().toISOString().split('T')[0];
            fetchBookings(today, ''); // Empty `date_to` fetches all future bookings
        }

        // Fetch bookings on page load
        fetchTodayAndFutureBookings();

        // Fetch bookings on filter button click
        $('#filter-btn').click(function () {
            const dateFrom = $('#date-from').val();
            const dateTo = $('#date-to').val();
            fetchBookings(dateFrom, dateTo);
        });

        // Reset filter to show today's and future bookings
        $('#reset-btn').click(function () {
            $('#date-from').val('');
            $('#date-to').val('');
            fetchTodayAndFutureBookings();
        });
    });
</script>

<?php include '../includes/footer.php'; ?>
</body>
</html>

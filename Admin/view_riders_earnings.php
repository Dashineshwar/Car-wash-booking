<?php
include 'includes/admin_session.php';
include 'includes/connection.php';
include 'includes/topbar.php';

// Fetch all riders for the dropdown
$ridersQuery = "SELECT rider_id, username FROM rider";
$ridersResult = mysqli_query($conn, $ridersQuery);
$riders = [];
while ($row = mysqli_fetch_assoc($ridersResult)) {
    $riders[] = $row;
}

// Fetch service types and booking types for dropdowns
$servicesQuery = "SELECT DISTINCT service_type FROM transactions";
$servicesResult = mysqli_query($conn, $servicesQuery);

$bookingTypesQuery = "SELECT DISTINCT booking_type FROM transactions";
$bookingTypesResult = mysqli_query($conn, $bookingTypesQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: View Riders Earnings</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
<div class="container mt-4">
    <h2 class="text-center mb-4">Admin: View Riders Earnings</h2>
    <form id="filter-form" class="form-inline justify-content-center mb-4">
        <label for="rider-select" class="mr-2">Rider:</label>
        <select id="rider-select" name="rider_id" class="form-control mr-3">
            <option value="">All Riders</option>
            <?php foreach ($riders as $rider): ?>
                <option value="<?= $rider['rider_id'] ?>"><?= $rider['username'] ?></option>
            <?php endforeach; ?>
        </select>
        <label for="service-select" class="mr-2">Service:</label>
        <select id="service-select" name="service_type" class="form-control mr-3">
            <option value="">All Services</option>
            <?php while ($row = mysqli_fetch_assoc($servicesResult)): ?>
                <option value="<?= $row['service_type'] ?>"><?= $row['service_type'] ?></option>
            <?php endwhile; ?>
        </select>
        <label for="booking-select" class="mr-2">Booking Type:</label>
        <select id="booking-select" name="booking_type" class="form-control mr-3">
            <option value="">All Booking Types</option>
            <?php while ($row = mysqli_fetch_assoc($bookingTypesResult)): ?>
                <option value="<?= $row['booking_type'] ?>"><?= $row['booking_type'] ?></option>
            <?php endwhile; ?>
        </select>
        <label for="from-date" class="mr-2">From:</label>
        <input type="date" id="from-date" name="from_date" class="form-control mr-3">
        <label for="to-date" class="mr-2">To:</label>
        <input type="date" id="to-date" name="to_date" class="form-control mr-3">
        <button type="button" id="filter-button" class="btn btn-primary">Filter</button>
    </form>
    <button id="export-pdf" class="btn btn-success mb-4" onclick="exportToPDF()">Export to PDF</button>
    <div id="earnings-summary">
        <p>Use the filters above to view earnings.</p>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Transaction ID</th>
                    <th>Rider</th>
                    <th>Booking ID</th>
                    <th>Service Type</th>
                    <th>Booking Type</th>
                    <th>Amount (RM)</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <!-- Dynamic rows will be added here -->
            </tbody>
        </table>
        <h4 class="text-right">Total Earnings: RM 0.00</h4>
    </div>
</div>

<script>
    function exportToPDF() {
        const riderId = document.getElementById('rider-select').value;
        const serviceType = document.getElementById('service-select').value;
        const bookingType = document.getElementById('booking-select').value;
        const dateFrom = document.getElementById('from-date').value;
        const dateTo = document.getElementById('to-date').value;

        const url = `php/earnings/export_admin_rider_earnings.php?rider_id=${riderId}&service_type=${serviceType}&booking_type=${bookingType}&date_from=${dateFrom}&date_to=${dateTo}`;
        window.open(url, '_blank');
    }

    $(document).ready(function () {
        $('#filter-button').click(function () {
            const riderId = $('#rider-select').val();
            const serviceType = $('#service-select').val();
            const bookingType = $('#booking-select').val();
            const fromDate = $('#from-date').val();
            const toDate = $('#to-date').val();

            $.ajax({
                url: 'php/earnings/fetch_admin_rider_earnings.php',
                type: 'GET',
                data: {
                    rider_id: riderId,
                    service_type: serviceType,
                    booking_type: bookingType,
                    date_from: fromDate,
                    date_to: toDate
                },
                dataType: 'json',
                success: function (response) {
                    if (response.error) {
                        alert(response.message);
                        return;
                    }

                    const earnings = response.bookings;
                    const totalEarnings = response.total;

                    // Update table rows
                    let tableRows = '';
                    earnings.forEach(earning => {
                        tableRows += `
                            <tr>
                                <td>${earning.transaction_id}</td>
                                <td>${earning.username}</td>
                                <td>${earning.booking_id}</td>
                                <td>${earning.service_type}</td>
                                <td>${earning.booking_type}</td>
                                <td>${parseFloat(earning.amount).toFixed(2)}</td>
                                <td>${earning.transaction_time}</td>
                            </tr>
                        `;
                    });
                    $('table tbody').html(tableRows);

                    // Update total earnings
                    $('h4.text-right').text(`Total Earnings: RM ${parseFloat(totalEarnings).toFixed(2)}`);
                },
                error: function () {
                    alert('Failed to fetch earnings.');
                }
            });
        });
    });
</script>
</body>
</html>
